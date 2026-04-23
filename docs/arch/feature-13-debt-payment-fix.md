# Architecture: Feature #13 — Оплата долга: не сдвигать дату, пересчитывать верно

## ADR-1: Убрать recalculateNextPaymentDate() из Payment::afterSave/afterDelete

**Context:** `Payment` (основной долг) и `Month` (ежемесячный процент) — разные таблицы. Дата `date_constribution` должна сдвигаться только при оплате ежемесячного процента. Сейчас `Payment::afterSave()` сдвигает дату при каждом платеже по долгу.

**Decision:** Удалить вызов `credit->recalculateNextPaymentDate()` из `Payment::afterSave()` и `Payment::afterDelete()`. Дата управляется только в `actionPaymentMonth` (установка) и `actionDeleteMonth` (откат) через прямое присвоение `$model->date_constribution = $date`.

**Consequences:** `recalculateNextPaymentDate()` в модели Credit остаётся, но не используется из Payment. Метод можно оставить для возможного будущего использования. Тесты `CreditDateTest` не затрагиваются — они тестируют метод напрямую.

---

## ADR-2: Убрать ручное вычитание debt в actionReceivedCredit

**Context:** `Payment::afterSave()` вызывает `recalculateDebt()`, которая уже корректно считает `debt = sum - totalPaid` и сохраняет. Затем контроллер дополнительно делает `$model->debt = $model->debt - $sum` — двойное вычитание.

**Decision:** Убрать строку `$model->debt = $model->debt - $sum` из `actionReceivedCredit`. После `$payment->save()` `debt` уже верный. Аналогично в `actionDeletePayment`: строки `$model->debt = $model->debt + $payment->sum` тоже дублируют `afterDelete → recalculateDebt()`.

**Consequences:** Контроллер становится чище. Логика пересчёта долга централизована в `Payment::afterSave/afterDelete → Credit::recalculateDebt()`.

---

## ADR-3: Исправить intval → float при расчёте month_payment

**Context:** `round(intval($model->debt * $model->percant)/100, 2)` — `intval` обрезает дробную часть произведения, что при граничных значениях (например, `debt=1999.99, percant=5`) даёт `round(99999/100,2) = 999.99` вместо `round(99999.5/100,2) = 1000.00`.

**Decision:** Заменить на `round($model->debt * $model->percant / 100, 2)`. Чистое float-умножение с финальным округлением до 2 знаков.

**Consequences:** Финансово корректное округление. Небольшие расхождения с историческими данными возможны, но это исправление ошибки.

---

## ERD

Изменений схемы БД нет.

---

## Затронутые файлы

| Файл | Изменение |
|------|-----------|
| `models/Payment.php` | `afterSave()`: убрать `recalculateNextPaymentDate()`; `afterDelete()`: то же |
| `controllers/CreditController.php` | `actionReceivedCredit`: убрать `$model->debt - $sum`, fix intval; `actionDeletePayment`: убрать `$model->debt + sum`, fix intval |
| `tests/unit/models/CreditDebtTest.php` | Добавить тесты: date не меняется после payment, month_payment корректен |

---

## Diff-план

### models/Payment.php — afterSave()
```php
// УБРАТЬ эту строку:
$credit->recalculateNextPaymentDate();
```

### models/Payment.php — afterDelete()
```php
// УБРАТЬ эту строку:
$credit->recalculateNextPaymentDate();
```

### controllers/CreditController.php — actionReceivedCredit()
```php
// УБРАТЬ (дублирует afterSave):
$model->debt = $model->debt - $sum;

// ЗАМЕНИТЬ:
$model->month_payment = round(intval($model->debt * $model->percant)/100, 2);
// НА:
$model->month_payment = round($model->debt * $model->percant / 100, 2);
```

### controllers/CreditController.php — actionDeletePayment()
```php
// УБРАТЬ (дублирует afterDelete):
$model->debt = $model->debt + $payment->sum;

// ЗАМЕНИТЬ:
$model->month_payment = round(intval($model->debt * $model->percant)/100, 2);
// НА:
$model->month_payment = round($model->debt * $model->percant / 100, 2);
```

---

## Security Considerations

- Нет новых endpoint'ов
- Финансовые операции: round() вместо intval() — устраняет ошибку округления
- CSRF protection без изменений
- RBAC: не затронуто
