# Test Cases: Feature #18 — Smart date_constribution recalc

## TC-18-01: Частичный платёж — дата не меняется

**AC:** AC-1
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcPartialPaymentNoDateChange

**Given** start='2026-05-01', month_payment=300, totalMonth=200
**When** recalculateNextPaymentDateFromMonth()
**Then** date_constribution = '2026-05-01'
**Result:** PASS ✅

---

## TC-18-02: Нет платежей — дата = start

**AC:** AC-1
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcZeroTotalNoDateChange

**Given** start='2026-05-01', month_payment=300, totalMonth=0
**When** recalculateNextPaymentDateFromMonth()
**Then** date_constribution = '2026-05-01'
**Result:** PASS ✅

---

## TC-18-03: Ровно один месяц → +1 месяц

**AC:** AC-2
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcOneFullMonthShiftsOnce

**Given** start='2026-05-01', month_payment=300, totalMonth=300
**When** recalculateNextPaymentDateFromMonth()
**Then** date_constribution = '2026-06-01'
**Result:** PASS ✅

---

## TC-18-04: Два полных месяца → +2 месяца

**AC:** AC-3
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcTwoFullMonthsShiftsTwice

**Given** start='2026-05-01', month_payment=300, totalMonth=600
**When** recalculateNextPaymentDateFromMonth()
**Then** date_constribution = '2026-07-01'
**Result:** PASS ✅

---

## TC-18-05: Переплата — остаток не учитывается

**AC:** AC-3
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcOverpaymentClampedToFullMonths

**Given** totalMonth=700 (2×300 + 100 остаток)
**When** recalculateNextPaymentDateFromMonth()
**Then** date_constribution = '2026-07-01' (не +3)
**Result:** PASS ✅

---

## TC-18-06: Накопление 3 платежей по 300

**AC:** AC-4
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcThreeMonthsAccumulated

**Given** 3 платежа по 300 = 900 суммарно
**When** recalculateNextPaymentDateFromMonth()
**Then** date_constribution = '2026-08-01' (+3 месяца)
**Result:** PASS ✅

---

## TC-18-07: После удаления платежа — корректный пересчёт

**AC:** AC-5
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcAfterDeleteCorrectlyRecomputes

**Given** было 900 → удалён один 300 → осталось 600
**When** recalculateNextPaymentDateFromMonth() (от start)
**Then** date_constribution = '2026-07-01' (+2, не +3)
**Result:** PASS ✅

---

## TC-18-08: Edge — month_payment=0 безопасно

**AC:** NFR
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcZeroMonthPaymentSafe
**Result:** PASS ✅

---

## TC-18-09: Edge — startDate=null безопасно

**AC:** NFR
**Type:** Unit
**File:** CreditDateTest::testSmartRecalcNullStartDateSafe
**Result:** PASS ✅

---

## TC-18-10: Регрессия — все тесты проходят

**AC:** AC-6
**Type:** Regression
**Command:** `php vendor/bin/codecept run unit`
**Expected:** 53 tests, 81 assertions
**Result:** PASS ✅
