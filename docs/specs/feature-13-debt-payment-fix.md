# Feature #13: Оплата долга — не сдвигать дату, пересчитывать month_payment верно

## Описание

При оплате основного долга (`actionReceivedCredit`) система содержит три связанных бага:

1. **Дата сдвигается** — `Payment::afterSave()` вызывает `recalculateNextPaymentDate()`, которая суммирует ВСЕ платежи из таблицы `payment` и сдвигает `date_constribution` вперёд. Но дата должна сдвигаться только при оплате ежемесячного процента (`actionPaymentMonth`).

2. **Двойное вычитание долга** — `Payment::afterSave()` уже вызывает `recalculateDebt()` (считает `debt = sum - totalPaid`). Затем контроллер `actionReceivedCredit` дополнительно делает `$model->debt = $model->debt - $sum` на уже пересчитанной модели → долг вычитается дважды.

3. **Некорректное округление** — `round(intval($model->debt * $model->percant)/100, 2)`: `intval` обрезает дробную часть произведения до целого, что даёт неверный результат при дробных значениях. Тот же баг в `actionDeletePayment`.

---

## Functional Requirements

- FR-1: `Payment::afterSave()` и `Payment::afterDelete()` НЕ вызывают `recalculateNextPaymentDate()` — дата управляется только через `actionPaymentMonth` и `actionDeleteMonth`
- FR-2: `actionReceivedCredit` не дублирует вычитание долга — `recalculateDebt()` в `afterSave` уже даёт верный результат
- FR-3: `month_payment` пересчитывается как `round($debt * $percant / 100, 2)` без `intval`
- FR-4: `actionPaymentMonth` продолжает обновлять `date_constribution` через `$model->date_constribution = $date`
- FR-5: `actionDeletePayment` — те же исправления что FR-2, FR-3

## Non-Functional Requirements

- NFR-1: Все существующие unit-тесты (36 шт.) проходят без изменений
- NFR-2: Новые unit-тесты покрывают исправленное поведение
- NFR-3: PHP 7.1 совместимость

---

## Acceptance Criteria

### AC-1: date_constribution не изменяется при оплате долга

**Given** кредит с `date_constribution = '2026-05-01'` и нулевым балансом платежей  
**When** вызывается `actionReceivedCredit(sum=500, ...)`  
**Then** `credit.date_constribution` остаётся `'2026-05-01'`

### AC-2: debt пересчитывается корректно (без двойного вычитания)

**Given** кредит `sum=5000, debt=3000`, платежей на 2000  
**When** вызывается `actionReceivedCredit(sum=500, ...)`  
**Then** `credit.debt = 2500` (не 1500)

### AC-3: month_payment рассчитывается без intval

**Given** кредит `debt=1999.99, percant=5`  
**When** вызывается `actionReceivedCredit` или `actionDeletePayment`  
**Then** `credit.month_payment = round(1999.99 * 5 / 100, 2) = 100.00` (не 99.99)

### AC-4: actionPaymentMonth по-прежнему обновляет дату

**Given** кредит с `date_constribution = '2026-05-01'`  
**When** вызывается `actionPaymentMonth(date='2026-06-01', ...)`  
**Then** `credit.date_constribution = '2026-06-01'`

### AC-5: Регрессия — все существующие тесты зелёные

**Given** изменения в `Payment.php` и `CreditController.php`  
**When** `php vendor/bin/codecept run unit`  
**Then** OK (36+ tests, все assertions зелёные)

---

## Out of Scope

- Изменение схемы таблицы `payment` (добавление поля `type`)
- Изменение `actionPaymentMonth` логики помимо уже работающей
- Изменение отображения во views
- `actionDeleteMonth` — дата там управляется вручную корректно

---

## Technical Notes

- `Payment::afterSave()` / `afterDelete()` → убрать вызов `recalculateNextPaymentDate()`
- `actionReceivedCredit` строка 215: убрать `$model->debt = $model->debt - $sum` (дублирует afterSave)
- `actionReceivedCredit` строка 216: `round(intval(...)/100,2)` → `round($model->debt * $model->percant / 100, 2)`
- `actionDeletePayment` строка 320: та же замена intval → round; убрать ручной пересчёт debt если afterDelete уже даёт верное значение
- Проверить: `actionDeletePayment` строки 318-321 — там debt восстанавливается вручную `debt + payment->sum`, но `afterDelete` через `recalculateDebt()` уже делает то же самое
