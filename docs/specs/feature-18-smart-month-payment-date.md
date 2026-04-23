# Feature #18: Умный расчёт date_constribution по Month-платежам

## Описание

После каждого ежемесячного платежа (actionPaymentMonth) и после удаления (actionDeleteMonth) дата следующей оплаты должна пересчитываться на основе **суммарных** Month-платежей, а не просто ±1 месяц.

---

## Functional Requirements

- FR-1: `Credit::recalculateNextPaymentDateFromMonth()` суммирует все Month-записи по `id_credit`
- FR-2: Количество покрытых месяцев = `floor(totalMonthPaid / month_payment)`
- FR-3: `date_constribution = date_constribution_start + N месяцев` (N = покрытые месяцы)
- FR-4: Если N = 0 (totalMonthPaid < month_payment) → date = date_constribution_start
- FR-5: `actionPaymentMonth` вызывает новый метод вместо прямого присвоения +1 MONTH
- FR-6: `actionDeleteMonth` вызывает новый метод вместо прямого присвоения -1 MONTH

## Non-Functional Requirements

- NFR-1: PHP 7.1 — strtotime('+1 MONTH'), без DateInterval
- NFR-2: Все существующие 44 unit-теста проходят
- NFR-3: Нет дополнительных запросов к БД сверх необходимого (1 SUM-запрос)

---

## Acceptance Criteria

### AC-1: Partial payment — дата не меняется

**Given** кредит: date_constribution_start='2026-05-01', month_payment=300; Month-платежей нет или сумма < 300
**When** `recalculateNextPaymentDateFromMonth()` вызван
**Then** `date_constribution = '2026-05-01'`

### AC-2: Ровно один месяц

**Given** кредит: date_constribution_start='2026-05-01', month_payment=300; суммарно Month=300
**When** `recalculateNextPaymentDateFromMonth()` вызван
**Then** `date_constribution = '2026-06-01'`

### AC-3: Два полных месяца + остаток

**Given** кредит: date_constribution_start='2026-05-01', month_payment=300; суммарно Month=700
**When** `recalculateNextPaymentDateFromMonth()` вызван
**Then** `date_constribution = '2026-07-01'` (2 полных месяца, 100 остаток не учитывается)

### AC-4: Учитываются ВСЕ предыдущие Month-платежи

**Given** 3 отдельных Month-платежа по 300 (суммарно 900)
**When** `recalculateNextPaymentDateFromMonth()` вызван
**Then** `date_constribution = '2026-08-01'` (+3 месяца)

### AC-5: После удаления Month-платежа дата корректно пересчитывается

**Given** суммарно Month=900 (+3 месяца); один платёж 300 удаляется
**When** `actionDeleteMonth` вызван → `recalculateNextPaymentDateFromMonth()`
**Then** `date_constribution = '2026-07-01'` (+2 месяца от start)

### AC-6: Регрессия — тесты не ломаются

**Given** изменения в Credit.php, CreditController.php
**When** `php vendor/bin/codecept run unit`
**Then** OK (≥ 44 tests)

---

## Out of Scope

- Изменение модели Payment (долговые платежи)
- Изменение recalculateNextPaymentDate() (для Payment)
- Изменение view-файлов
- Автоматический вызов из Month::afterSave (это следующий шаг)

---

## Technical Notes

- Новый метод: `Credit::recalculateNextPaymentDateFromMonth()`
- Базовая дата: `$this->date_constribution_start` (не текущий date_constribution)
- Источник данных: `Month::find()->where(['id_credit' => $this->id])->sum('sum')`
- Алгоритм идентичен `recalculateNextPaymentDate()` но использует Month вместо Payment
