# Test Cases: Feature #23 — Manual Date Picker for Next Payment Date

## Overview

Feature #23 adds an `<input type="date" id="next_payment_date">` to the monthly payment form.
The value is pre-filled with `+1 MONTH` from `$model->date_constribution` and sent as
`date_constribution` in the AJAX call to `actionPaymentMonth()`.

The controller validates the incoming value with three conditions:

1. Not empty/null  
2. Matches `^\d{4}-\d{2}-\d{2}$`  
3. Is >= today  

If all three pass the date is stored directly; otherwise `recalculateNextPaymentDateFromMonth()` is called.

---

## TC-23-01: Valid custom date — today — accepted

**AC:** AC-1  
**Type:** Unit  
**File:** `tests/unit/models/CreditPaymentMonthDateTest::testTodayDateIsAccepted`

**Given** `date_constribution` = today's date (YYYY-MM-DD)  
**When** `actionPaymentMonth()` validates the parameter  
**Then** the date passes all three conditions → stored directly; `recalculateNextPaymentDateFromMonth()` is NOT called  
**Result:** PASS ✅

---

## TC-23-02: Valid custom date — future — accepted

**AC:** AC-2  
**Type:** Unit  
**Files:**
- `CreditPaymentMonthDateTest::testFutureDateIsAccepted`
- `CreditPaymentMonthDateTest::testTomorrowDateIsAccepted`

**Given** `date_constribution` = a future date (e.g. `2026-05-27` when today is `2026-04-27`)  
**When** `actionPaymentMonth()` validates the parameter  
**Then** the date passes all conditions → stored directly  
**Result:** PASS ✅

---

## TC-23-03: Invalid date — past — fallback to auto-recalc

**AC:** AC-3  
**Type:** Unit  
**Files:**
- `CreditPaymentMonthDateTest::testPastDateFallsBackToRecalc`
- `CreditPaymentMonthDateTest::testOldPastDateFallsBackToRecalc`

**Given** `date_constribution` is a past date (e.g. `2026-04-26` when today is `2026-04-27`)  
**When** `actionPaymentMonth()` validates the parameter  
**Then** condition `strtotime(date) >= strtotime(today)` fails → `recalculateNextPaymentDateFromMonth()` is called  
**Result:** PASS ✅

---

## TC-23-04: Invalid format — non-date string — fallback to auto-recalc

**AC:** AC-4  
**Type:** Unit  
**Files:**
- `CreditPaymentMonthDateTest::testInvalidFormatStringFallsBackToRecalc` — `27-04-2026`
- `CreditPaymentMonthDateTest::testEmptyStringFallsBackToRecalc` — `""`
- `CreditPaymentMonthDateTest::testArbitraryTextFallsBackToRecalc` — `"next month"`
- `CreditPaymentMonthDateTest::testDateWithTimeFallsBackToRecalc` — `"2026-05-01 10:00:00"`
- `CreditPaymentMonthDateTest::testAlphanumericStringFallsBackToRecalc` — `"abcd-ef-gh"`
- `CreditPaymentMonthDateTest::testPartiallyMatchingFormatFallsBackToRecalc` — `"2026-05-1"`

**Given** `date_constribution` does not match `^\d{4}-\d{2}-\d{2}$`  
**When** `actionPaymentMonth()` validates the parameter  
**Then** regexp fails → fallback  
**Result:** PASS ✅

---

## TC-23-05: Null date — fallback to auto-recalc

**AC:** AC-5  
**Type:** Unit  
**File:** `CreditPaymentMonthDateTest::testNullDateFallsBackToRecalc`

**Given** `date_constribution` = `null` (parameter omitted from AJAX call)  
**When** `actionPaymentMonth()` validates the parameter  
**Then** truthiness check fails immediately → `recalculateNextPaymentDateFromMonth()` is called  
**Result:** PASS ✅

---

## TC-23-06: Regression — accepted date does not call recalculate

**AC:** AC-1 / Regression  
**Type:** Unit  
**File:** `CreditPaymentMonthDateTest::testAcceptedDateDoesNotTriggerRecalculate`

**Given** a valid future date is submitted  
**When** validation passes  
**Then** `recalculateNextPaymentDateFromMonth()` call count = 0  
**Result:** PASS ✅

---

## TC-23-07: Regression — rejected date calls recalculate exactly once

**AC:** AC-3/AC-5 / Regression  
**Type:** Unit  
**File:** `CreditPaymentMonthDateTest::testRejectedDateTriggersRecalculateOnce`

**Given** `date_constribution` = `null`  
**When** validation fails  
**Then** `recalculateNextPaymentDateFromMonth()` call count = 1  
**Result:** PASS ✅

---

## TC-23-08: Regression — delta algorithm unaffected

**AC:** NFR  
**Type:** Unit  
**File:** `CreditPaymentMonthDateTest::testDeltaAlgorithmRegression`

**Given** totalBefore=0, totalAfter=300, month_payment=300, currentDate='2026-05-01'  
**When** delta-based calculation runs  
**Then** date = '2026-06-01' (delta=1 → +1 MONTH)  
**Result:** PASS ✅

---

## TC-23-09: Full unit suite regression

**AC:** NFR  
**Type:** Regression  
**Command:** `"D:/OSPanel/modules/php/PHP_7.1-x64/php.exe" vendor/codeception/base/codecept run unit`  
**Expected:** All prior tests green + 13 new tests (1 file)  
**Result:** PASS ✅
