# Test Cases: Feature #7 — recalculateNextPaymentDate() + Payment hooks

**Issue:** #7 Story: Добавить recalculateNextPaymentDate() и автообновление Credit.date_constribution при платежах  
**Parent:** #6  
**Date:** 2026-04-16  
**Status:** QA Passed ✅

---

## Test Case Summary

| TC ID | Description | Priority | Type | Status |
|-------|-------------|----------|------|--------|
| TC-7-01 | 1 платёж = month_payment → дата +1 месяц | Critical | Unit | ✅ Pass |
| TC-7-02 | Платёж 2×month_payment → дата +2 месяца | High | Unit | ✅ Pass |
| TC-7-03 | Частичный платёж → дата не меняется | Critical | Unit | ✅ Pass |
| TC-7-04 | Нет платежей → дата = start | Critical | Unit | ✅ Pass |
| TC-7-05 | Два частичных платежа покрывают 1 месяц → +1 мес | High | Unit | ✅ Pass |
| TC-7-06 | month_payment = 0 → защита от деления на ноль | Medium | Unit | ✅ Pass |
| TC-7-07 | startDate = null → без паники | Medium | Unit | ✅ Pass |
| TC-7-08 | Точное покрытие 3 месяцев → +3 месяца | High | Unit | ✅ Pass |
| TC-7-09 | Переплата → только полные месяцы считаются | High | Unit | ✅ Pass |

---

## Detailed Test Cases

### TC-7-01: Один платёж покрывает один месяц

**AC:** AC-1 из #6 (платёж 300 при month_payment=300 → +1 месяц)

**Given:** credit с `date_constribution_start = '2026-05-01'`, `month_payment = 300`  
**When:** Сохранён платёж `sum = 300`  
**Then:** `date_constribution = '2026-06-01'`

**Covered by:** `CreditDateTest::testOneFullPaymentShiftsDateByOneMonth`

---

### TC-7-02: Платёж покрывает два месяца

**AC:** AC-3 из #6 (платёж 600 при month_payment=300 → +2 месяца)

**Given:** credit с `date_constribution_start = '2026-05-01'`, `month_payment = 300`  
**When:** Сохранён платёж `sum = 600`  
**Then:** `date_constribution = '2026-07-01'`

**Covered by:** `CreditDateTest::testDoublePaymentShiftsDateByTwoMonths`

---

### TC-7-03: Частичный платёж — дата не меняется

**AC:** AC-4 из #6 (частичный платёж → без сдвига)

**Given:** credit с `date_constribution_start = '2026-05-01'`, `month_payment = 300`  
**When:** Сохранён платёж `sum = 200`  
**Then:** `date_constribution = '2026-05-01'` (не изменилась)

**Covered by:** `CreditDateTest::testPartialPaymentDoesNotShiftDate`

---

### TC-7-04: Нет платежей — дата = start

**AC:** AC-2 из #6 (удаление платежа → откат к start)

**Given:** credit с `date_constribution_start = '2026-05-01'`, `month_payment = 300`  
**When:** `totalPaid = 0` (платежей нет / все удалены)  
**Then:** `date_constribution = '2026-05-01'`

**Covered by:** `CreditDateTest::testNoPaymentsReturnsStartDate`

---

### TC-7-05: Два частичных платежа покрывают один месяц

**AC:** AC-5 из #6 (SUM=400 > 300 → +1 месяц)

**Given:** credit с `date_constribution_start = '2026-05-01'`, `month_payment = 300`  
**When:** Два платежа по 200, итого `totalPaid = 400`  
**Then:** `date_constribution = '2026-06-01'`

**Covered by:** `CreditDateTest::testTwoPartialPaymentsCoveringOneMonth`

---

### TC-7-06: month_payment = 0 — защита

**AC:** Edge case (деление на ноль защищено)

**Given:** credit с `month_payment = 0`  
**When:** `recalculateNextPaymentDate()` вызван  
**Then:** Метод возвращается досрочно, `date_constribution` не меняется

**Covered by:** `CreditDateTest::testZeroMonthPaymentIsHandledSafely`

---

### TC-7-07: startDate = null — защита

**AC:** Edge case (null дата не вызывает panic)

**Given:** credit с `date_constribution_start = null`  
**When:** `recalculateNextPaymentDate()` вызван  
**Then:** Метод возвращается досрочно, без исключения

**Covered by:** `CreditDateTest::testNullStartDateIsHandledSafely`

---

### TC-7-08: Точное покрытие 3 месяцев

**AC:** AC-1 граничный случай (остаток ровно 0 после покрытия)

**Given:** credit с `date_constribution_start = '2026-01-15'`, `month_payment = 500`  
**When:** `totalPaid = 1500`  
**Then:** `date_constribution = '2026-04-15'` (+3 месяца)

**Covered by:** `CreditDateTest::testExactPaymentCoverageShiftsExactMonths`

---

### TC-7-09: Переплата — только полные месяцы

**AC:** AC-3 расширенный (переплата выше 2 месяцев, но не достигает 3-го)

**Given:** credit с `date_constribution_start = '2026-05-01'`, `month_payment = 300`  
**When:** `totalPaid = 700` (2×300=600 + 100 остаток)  
**Then:** `date_constribution = '2026-07-01'` (+2 месяца, не +3)

**Covered by:** `CreditDateTest::testOverpaymentClampedToFullMonths`

---

## Implementation Files Tested

| File | Role |
|------|------|
| `models/Credit.php::recalculateNextPaymentDate()` | Основной алгоритм |
| `models/Payment.php::afterSave()` | Вызов после сохранения платежа |
| `models/Payment.php::afterDelete()` | Вызов после удаления платежа |
| `controllers/CreditController.php::actionCreate()` | Заполнение date_constribution_start |

---

## Test Execution

```
php vendor/codecept/base/codecept run unit models/CreditDateTest.php
OK (9 tests, 9 assertions)

php vendor/codecept/base/codecept run unit
OK (36 tests, 59 assertions) — no regressions
```
