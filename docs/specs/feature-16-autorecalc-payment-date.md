# Feature #16: Авторасчёт date_constribution в actionPaymentMonth/actionDeleteMonth

## Описание

После удаления DatePicker с `id="date"` в issue #12 JS-функция `receivedCredit()` передаёт в `actionPaymentMonth` пустой параметр `$date`. Контроллер записывал `$model->date_constribution = $date` — пустая строка затирает дату. Аналогичная проблема в `actionDeleteMonth`.

**Решение:** бэкенд сам рассчитывает новую дату, не полагаясь на фронтенд.

---

## Functional Requirements

- FR-1: `actionPaymentMonth` устанавливает `date_constribution = старая_дата + 1 месяц`
- FR-2: `actionDeleteMonth` устанавливает `date_constribution = старая_дата - 1 месяц`
- FR-3: Параметр `$date` в обоих actions игнорируется (или удаляется)
- FR-4: JS `main.js` — параметр `date` убирается из AJAX-запроса `payment-month`

## Non-Functional Requirements

- NFR-1: PHP 7.1 — использовать `strtotime('+1 MONTH', ...)`, не `DateInterval`
- NFR-2: Все 40 unit-тестов проходят

---

## Acceptance Criteria

### AC-1: actionPaymentMonth сдвигает дату +1 месяц

**Given** кредит с `date_constribution = '2026-05-01'`
**When** вызывается `actionPaymentMonth(sum=..., id_credit=...)`
**Then** `credit.date_constribution = '2026-06-01'`

### AC-2: actionDeleteMonth откатывает дату -1 месяц

**Given** кредит с `date_constribution = '2026-06-01'`
**When** вызывается `actionDeleteMonth(id=...)`
**Then** `credit.date_constribution = '2026-05-01'`

### AC-3: Дата не зависит от параметра $date с фронтенда

**Given** JS передаёт `date=""` (пустая строка после удаления DatePicker)
**When** вызывается `actionPaymentMonth`
**Then** `credit.date_constribution` корректна (не пустая)

### AC-4: Регрессия — тесты не ломаются

**Given** изменения в `CreditController.php`
**When** `php vendor/bin/codecept run unit`
**Then** OK (40 tests, все assertions)

---

## Out of Scope

- Изменение модели `Credit` или `Month`
- Изменение view-файлов (кроме необязательной чистки JS)
- Сложная логика (не +1/-1 месяц, а расчёт по истории платежей)

---

## Technical Notes

- `actionPaymentMonth`: загрузить модель Credit, `$model->date_constribution = date('Y-m-d', strtotime('+1 MONTH', strtotime($model->date_constribution)))`
- `actionDeleteMonth`: аналогично `strtotime('-1 MONTH', ...)`
- `main.js` строка 171: убрать `date:$("#date").val()` из параметров AJAX
- `main.js` строка 42: `deleteMonthBtn` — убрать `&date=...` из URL
