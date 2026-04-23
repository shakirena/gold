# Test Cases: Feature #11 — Убрать инпут даты следующей оплаты

**Issue:** #11 (Story: #12)
**Spec:** [docs/specs/feature-11-hide-next-payment-date.md](../../specs/feature-11-hide-next-payment-date.md)
**Arch:** [docs/arch/feature-11-hide-next-payment-date.md](../../arch/feature-11-hide-next-payment-date.md)
**Date:** 2026-04-17
**Status:** QA Passed ✅

---

## Test Case Summary

| TC ID | Description | Priority | Type | Status |
|-------|-------------|----------|------|--------|
| TC-12-01 | DatePicker «Növbəti ödəniş» отсутствует в view_credit.php | Critical | Static | ✅ Pass |
| TC-12-02 | Неиспользуемый `use kartik\date\DatePicker` удалён | High | Static | ✅ Pass |
| TC-12-03 | Авторасчёт date_constribution работает (CreditDateTest) | Critical | Unit | ✅ Pass |
| TC-12-04 | Регрессия: 36/36 unit-тестов зелёные | Critical | Unit | ✅ Pass |

---

## Detailed Test Cases

### TC-12-01: DatePicker «Növbəti ödəniş» убран из view_credit.php

**Priority:** Critical
**Type:** Static
**AC Reference:** AC-1, AC-4

#### Preconditions
- Файл `views/credit/view_credit.php` после коммита `6fc9f6f`

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `grep "DatePicker\|check_issue_date\|Növbəti" views/credit/view_credit.php` | 0 совпадений |
| 2 | Открыть `views/credit/view_credit.php`, найти `<table class='table-play'>` | Строка «Ayliq faiz» содержит только input и пустой `<td></td>` |

#### Expected Result
DatePicker и текст «Növbəti ödəniş» отсутствуют. Строка ежемесячного платежа содержит `<td></td>` без вложенного контента.

#### Actual Result
```
grep: 0 matches — DatePicker removed ✅
```

---

### TC-12-02: Неиспользуемый import DatePicker удалён

**Priority:** High
**Type:** Static
**AC Reference:** AC-4 (нет артефактов)

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `grep "use kartik\\\\date\\\\DatePicker" views/credit/view_credit.php` | 0 совпадений |

#### Actual Result
Строка `use kartik\date\DatePicker;` отсутствует ✅

---

### TC-12-03: Авторасчёт date_constribution не сломан

**Priority:** Critical
**Type:** Unit
**AC Reference:** AC-2, AC-3

#### Preconditions
- Модели Credit, Payment без изменений

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `php vendor/codecept run unit models/CreditDateTest.php` | 9/9 tests pass |
| 2 | Payment::afterSave() вызывает credit->recalculateNextPaymentDate() | date_constribution сдвигается на +1 месяц |

#### Mapped Unit Tests
- `CreditDateTest::testOneFullPaymentShiftsDateByOneMonth`
- `CreditDateTest::testDoublePaymentShiftsDateByTwoMonths`
- `CreditDateTest::testPartialPaymentDoesNotShiftDate`
- `CreditDateTest::testNoPaymentsReturnsStartDate`

---

### TC-12-04: Regression suite

**Priority:** Critical
**Type:** Unit Regression
**AC Reference:** AC-3

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `php vendor/codecept run unit` | OK (36 tests, 59 assertions) |

#### Actual Result
```
Time: 2.17 seconds, Memory: 18.00MB
OK (36 tests, 59 assertions) ✅
```

---

## Files Changed

| Файл | Изменение |
|------|-----------|
| `views/credit/view_credit.php` | Удалён `<td>` с DatePicker «Növbəti ödəniş», удалён `use kartik\date\DatePicker` |

## Related TCs

| TC | Feature | Связь |
|----|---------|-------|
| TC-7-01..09 | Feature #6 | CreditDateTest покрывает AC-2 данной фичи |
| TC-10-01 | Feature #9 | Аналогичное удаление DatePicker из _form.php |
