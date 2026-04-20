# Architecture: Feature #18 — Умный расчёт date_constribution

## ADR-1: Новый метод recalculateNextPaymentDateFromMonth()

**Context:** actionPaymentMonth и actionDeleteMonth сейчас делают простой ±1 месяц (после #16). Нужна логика с накопленной суммой.

**Decision:** Добавить `Credit::recalculateNextPaymentDateFromMonth()` по аналогии с существующим `recalculateNextPaymentDate()`, но суммирующий `Month` записи, а не `Payment`. Вызывать из обоих контроллерных actions вместо strtotime(±1 MONTH).

**Consequences:** Единственный источник правды для даты — `date_constribution_start` + накопленная сумма Month-платежей. Нет state-зависимости от предыдущего значения `date_constribution`.

---

## ERD

Изменений в схеме БД нет.

---

## Затронутые файлы

| Файл | Изменение |
|------|-----------|
| `models/Credit.php` | +`recalculateNextPaymentDateFromMonth()` |
| `controllers/CreditController.php` | `actionPaymentMonth`: вызов нового метода; `actionDeleteMonth`: вызов нового метода |

---

## Code Stub: Credit.php

```php
/**
 * Recalculate next payment date based on total Month payments from start date.
 */
public function recalculateNextPaymentDateFromMonth()
{
    $startDate = $this->date_constribution_start;
    if (!$startDate || $this->month_payment <= 0) {
        return;
    }

    $totalPaid = (float) Month::find()
        ->where(['id_credit' => $this->id])
        ->sum('sum');

    $monthsCovered = 0;
    $remaining = $totalPaid;
    while ($remaining >= $this->month_payment) {
        $monthsCovered++;
        $remaining -= $this->month_payment;
    }

    $dateAt = $startDate;
    for ($i = 0; $i < $monthsCovered; $i++) {
        $dateAt = date('Y-m-d', strtotime('+1 MONTH', strtotime($dateAt)));
    }

    $this->date_constribution = $dateAt;
    $this->save(false);
}
```

## Diff-план: CreditController.php

### actionPaymentMonth — заменить прямое присвоение:
```php
// УБРАТЬ:
$model->date_constribution = date('Y-m-d', strtotime('+1 MONTH', strtotime($model->date_constribution)));
$model->save();
// ЗАМЕНИТЬ НА:
$model->recalculateNextPaymentDateFromMonth();
```

### actionDeleteMonth — аналогично:
```php
// УБРАТЬ:
$model->date_constribution = date('Y-m-d', strtotime('-1 MONTH', strtotime($model->date_constribution)));
$model->save(); // (внутри метода)
// ЗАМЕНИТЬ НА:
$model->recalculateNextPaymentDateFromMonth();
```

---

## Security Considerations

- Нет новых endpoints
- Нет пользовательских входных данных в новом методе (данные из БД)
- CSRF без изменений
- Удалённый ранее параметр `$date` не возвращается
