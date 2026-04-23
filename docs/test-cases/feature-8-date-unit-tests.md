# Test Cases: Feature #8 — Unit-тесты расчёта даты следующего платежа

**Issue:** #8 Story: Unit-тесты расчёта даты следующего платежа  
**Parent:** #6  
**Blocked by:** #7  
**Date:** 2026-04-16  
**Status:** QA Passed ✅

---

## Test Case Summary

| TC ID | Description | Priority | Type | Status |
|-------|-------------|----------|------|--------|
| TC-8-01 | Тест-файл CreditDateTest.php существует и запускается | Critical | Meta | ✅ Pass |
| TC-8-02 | testOneFullPaymentShiftsDateByOneMonth проходит | Critical | Unit | ✅ Pass |
| TC-8-03 | testDoublePaymentShiftsDateByTwoMonths проходит | High | Unit | ✅ Pass |
| TC-8-04 | testPartialPaymentDoesNotShiftDate проходит | Critical | Unit | ✅ Pass |
| TC-8-05 | testNoPaymentsReturnsStartDate проходит | Critical | Unit | ✅ Pass |
| TC-8-06 | testTwoPartialPaymentsCoveringOneMonth проходит | High | Unit | ✅ Pass |
| TC-8-07 | testZeroMonthPaymentIsHandledSafely проходит | Medium | Unit | ✅ Pass |
| TC-8-08 | testNullStartDateIsHandledSafely проходит | Medium | Unit | ✅ Pass |
| TC-8-09 | testExactPaymentCoverageShiftsExactMonths проходит | High | Unit | ✅ Pass |
| TC-8-10 | testOverpaymentClampedToFullMonths проходит | High | Unit | ✅ Pass |
| TC-8-11 | Полный suite не регрессирует (36 тестов) | Critical | Suite | ✅ Pass |

---

## Detailed Test Cases

### TC-8-01: Файл существует и запускается

**Given:** `tests/unit/models/CreditDateTest.php` создан  
**When:** `php vendor/codecept/base/codecept run unit models/CreditDateTest.php`  
**Then:** Codeception запускает 9 тестов без ошибок парсинга

---

### TC-8-02 — TC-8-10: Индивидуальные тесты

Каждый тест верифицирует один сценарий AC из story #7/#6.  
Детальное описание логики — в `feature-7-auto-date-recalculation.md` (TC-7-01 … TC-7-09).

| TC | Method | Input | Expected |
|----|--------|-------|----------|
| TC-8-02 | testOneFullPaymentShiftsDateByOneMonth | start='2026-05-01', mp=300, paid=300 | '2026-06-01' |
| TC-8-03 | testDoublePaymentShiftsDateByTwoMonths | start='2026-05-01', mp=300, paid=600 | '2026-07-01' |
| TC-8-04 | testPartialPaymentDoesNotShiftDate | start='2026-05-01', mp=300, paid=200 | '2026-05-01' |
| TC-8-05 | testNoPaymentsReturnsStartDate | start='2026-05-01', mp=300, paid=0 | '2026-05-01' |
| TC-8-06 | testTwoPartialPaymentsCoveringOneMonth | start='2026-05-01', mp=300, paid=400 | '2026-06-01' |
| TC-8-07 | testZeroMonthPaymentIsHandledSafely | start='2026-05-01', mp=0, paid=500 | '2026-05-01' |
| TC-8-08 | testNullStartDateIsHandledSafely | start=null, mp=300, paid=300 | null |
| TC-8-09 | testExactPaymentCoverageShiftsExactMonths | start='2026-01-15', mp=500, paid=1500 | '2026-04-15' |
| TC-8-10 | testOverpaymentClampedToFullMonths | start='2026-05-01', mp=300, paid=700 | '2026-07-01' |

---

### TC-8-11: Regression suite

**Given:** CreditDateTest.php добавлен в suite  
**When:** `php vendor/codecept/base/codecept run unit`  
**Then:** 36 тестов, 59 assertions — всё зелёное, предыдущие тесты не сломаны

---

## AC Coverage

| AC (из issue #8) | Covered by |
|-----------------|------------|
| 1 платёж = +1 месяц | TC-8-02 |
| Платёж на 2 месяца = +2 месяца | TC-8-03 |
| Частичный платёж = без сдвига | TC-8-04 |
| После удаления = откат к start | TC-8-05 |

---

## Test Execution

```
php vendor/codecept/base/codecept run unit models/CreditDateTest.php
Unit Tests (9)
OK (9 tests, 9 assertions)

php vendor/codecept/base/codecept run unit
OK (36 tests, 59 assertions)
```
