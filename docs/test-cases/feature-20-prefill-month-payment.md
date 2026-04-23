# Test Cases: Bug #20 — Предзаполнение month_payment

## TC-20-01: Инпут предзаполнен model->month_payment

**AC:** AC-1
**Type:** Static/Manual
**File:** views/credit/view_credit.php:166

**Проверка:** `Html::input("text",'month_payment',$model->month_payment,[...])`
**Result:** PASS ✅ (подтверждено grep)

---

## TC-20-02: Регрессия unit-тестов

**AC:** AC-4
**Type:** Regression
**Command:** `php vendor/bin/codecept run unit`
**Expected:** 53 tests, 81 assertions
**Result:** PASS ✅

---

## TC-20-03: Оплата 1×mp → дата +1 месяц (логика #18)

**AC:** AC-2
**Type:** Unit (covered by CreditDateTest::testSmartRecalcOneFullMonthShiftsOnce)
**Result:** PASS ✅

---

## TC-20-04: Оплата 2×mp → дата +2 месяца (корректное поведение)

**AC:** AC-3
**Type:** Unit (covered by CreditDateTest::testSmartRecalcTwoFullMonthsShiftsTwice)
**Result:** PASS ✅
