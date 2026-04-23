# Architecture: Feature #6 — Автопересчёт даты следующего платежа

**Date:** 2026-04-16

---

## Проблема

`getDateConstribution()` использует `$this->date_constribution` как **исходную** точку и динамически сдвигает её через все платежи. Если перезаписывать это поле в БД — метод получит уже сдвинутую дату как базу и пересчитает неверно.

Нужно разделить две концепции:
- `date_constribution_start` — исходная дата из договора (неизменна)
- `date_constribution` — актуальная дата следующего платежа (автообновляется)

---

## ADR-1: Добавить `date_constribution_start`

**Context:** Для идемпотентного пересчёта даты нужна исходная точка отсчёта.

**Decision:** Добавить колонку `date_constribution_start DATE` в таблицу `credit`. Заполнять при создании кредита = `date_constribution`. При пересчёте всегда стартовать от `date_constribution_start`.

**Migration:**
```sql
ALTER TABLE credit ADD COLUMN date_constribution_start DATE NULL AFTER date_constribution;
UPDATE credit SET date_constribution_start = date_constribution WHERE date_constribution_start IS NULL;
```

**Consequences:**
- (+) Идемпотентный пересчёт: результат одинаков при любом порядке платежей
- (+) `getDateConstribution()` продолжает работать без изменений (читает `date_constribution` из DB, теперь актуальное)
- (-) Требует миграции и изменения `CreditController::actionCreate()` для заполнения нового поля

---

## ADR-2: Алгоритм `recalculateNextPaymentDate()`

**Decision:** Аналог `recalculateDebt()` — пересчёт от нуля через все платежи.

```php
public function recalculateNextPaymentDate()
{
    $dateAt = $this->date_constribution_start;
    if (!$dateAt) {
        return; // поле не заполнено — миграция не применена
    }

    $totalPaid = (float) Payment::find()
        ->where(['id_credit' => $this->id])
        ->sum('sum');

    $monthsCovered = 0;
    $remaining = $totalPaid;
    while ($remaining >= $this->month_payment && $this->month_payment > 0) {
        $monthsCovered++;
        $remaining -= $this->month_payment;
    }

    for ($i = 0; $i < $monthsCovered; $i++) {
        $dateAt = date('Y-m-d', strtotime('+1 MONTH', strtotime($dateAt)));
    }

    $this->date_constribution = $dateAt;
    $this->save(false);
}
```

**Rationale:**
- `SUM(payments.sum)` — один запрос, идемпотентен
- Не мутирует объекты Payment (в отличие от старого `getDateConstribution()`)
- Обрабатывает дробные случаи: остаток `< month_payment` не даёт сдвига

**Edge cases:**
- `month_payment = 0` → защита от деления на ноль через `$this->month_payment > 0`
- `date_constribution_start = null` → ранний выход без паники

---

## ADR-3: Обновление `Payment::afterSave()` / `afterDelete()`

```php
public function afterSave($insert, $changedAttributes)
{
    parent::afterSave($insert, $changedAttributes);
    $credit = $this->getCredit()->one();
    if ($credit !== null) {
        $credit->recalculateDebt();
        $credit->recalculateNextPaymentDate(); // добавить
    }
}

public function afterDelete()
{
    parent::afterDelete();
    $credit = Credit::findOne($this->id_credit);
    if ($credit !== null) {
        $credit->recalculateDebt();
        $credit->recalculateNextPaymentDate(); // добавить
    }
}
```

---

## ADR-4: Упрощение `getDateConstribution()`

После реализации `date_constribution` всегда актуален в БД. Метод `getDateConstribution()` можно упростить до чтения из БД + цветовой индикации, убрав цикл по платежам. Но это **вне scope** данной feature — делается отдельно чтобы не ломать ничего.

---

## Затронутые файлы

| Файл | Изменение |
|------|-----------|
| `console/migrations/m260416_000001_add_date_constribution_start.php` | Новый файл миграции |
| `models/Credit.php` | Добавить `recalculateNextPaymentDate()`, поле `date_constribution_start` в `rules()` и `attributeLabels()` |
| `models/Payment.php` | Вызов `recalculateNextPaymentDate()` в `afterSave()` и `afterDelete()` |
| `controllers/CreditController.php` | При создании кредита заполнять `date_constribution_start = date_constribution` |
| `tests/unit/models/CreditDateTest.php` | Новый файл unit-тестов |

---

## Схема БД (после миграции)

```
credit:
  date_constribution      DATE  -- актуальная дата (автообновляется)
  date_constribution_start DATE -- исходная дата из договора (неизменна)
```
