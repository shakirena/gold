# Test Cases: Feature #16 — Авторасчёт date_constribution

## TC-16-01: actionPaymentMonth сдвигает дату +1 месяц

**AC:** AC-1
**Type:** Unit
**File:** tests/unit/models/CreditDateTest.php::testPaymentMonthShiftsDatePlusOneMonth

**Steps:**
1. Задать date_constribution = '2026-05-01'
2. Вычислить: `date('Y-m-d', strtotime('+1 MONTH', strtotime('2026-05-01')))`

**Expected:** '2026-06-01'
**Result:** PASS ✅

---

## TC-16-02: actionDeleteMonth откатывает дату -1 месяц

**AC:** AC-2
**Type:** Unit
**File:** tests/unit/models/CreditDateTest.php::testDeleteMonthShiftsDateMinusOneMonth

**Steps:**
1. Задать date_constribution = '2026-06-01'
2. Вычислить: `date('Y-m-d', strtotime('-1 MONTH', strtotime('2026-06-01')))`

**Expected:** '2026-05-01'
**Result:** PASS ✅

---

## TC-16-03: Дата не зависит от параметра $date с фронтенда

**AC:** AC-3
**Type:** Unit
**File:** tests/unit/models/CreditDateTest.php::testPaymentMonthIgnoresFrontendDateParam

**Steps:**
1. JS передаёт date="" (пустая строка)
2. Бэкенд берёт $model->date_constribution = '2026-05-01'
3. Результат = +1 MONTH от stored date

**Expected:** '2026-06-01', не пустая строка
**Result:** PASS ✅

---

## TC-16-04: Edge — конец месяца (strtotime overflow)

**AC:** NFR-1
**Type:** Unit
**File:** tests/unit/models/CreditDateTest.php::testPaymentMonthEndOfMonth

**Steps:**
1. date_constribution = '2026-01-31'
2. strtotime('+1 MONTH') → PHP overflow behaviour

**Expected:** валидная дата формата YYYY-MM-DD (не пустая)
**Result:** PASS ✅

---

## TC-16-05: Регрессия — все предыдущие тесты проходят

**AC:** AC-4
**Type:** Regression

**Command:** `php vendor/bin/codecept run unit`
**Expected:** 44 tests, 71 assertions, 0 failures
**Result:** PASS ✅
