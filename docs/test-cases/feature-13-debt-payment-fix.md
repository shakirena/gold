# Test Cases: Feature #13 — Оплата долга: не сдвигать дату, пересчитывать верно

**Issues:** #13 (parent), #14, #15
**Spec:** [docs/specs/feature-13-debt-payment-fix.md](../../specs/feature-13-debt-payment-fix.md)
**Arch:** [docs/arch/feature-13-debt-payment-fix.md](../../arch/feature-13-debt-payment-fix.md)
**Date:** 2026-04-20
**Status:** QA Passed ✅

---

## Test Case Summary

| TC ID | Description | Priority | Type | Story | Status |
|-------|-------------|----------|------|-------|--------|
| TC-14-01 | afterSave не вызывает recalculateNextPaymentDate | Critical | Unit | #14 | ✅ Pass |
| TC-14-02 | afterDelete не вызывает recalculateNextPaymentDate | Critical | Unit | #14 | ✅ Pass |
| TC-14-03 | date_constribution не изменяется — static diff | Critical | Static | #14 | ✅ Pass |
| TC-15-01 | month_payment без intval: round(debt*percant/100,2) | Critical | Unit | #15 | ✅ Pass |
| TC-15-02 | debt не вычитается дважды | Critical | Unit | #15 | ✅ Pass |
| TC-15-03 | Regression: 40/40 тестов зелёные | Critical | Unit | #14+#15 | ✅ Pass |

---

## Detailed Test Cases

### TC-14-01: afterSave не вызывает recalculateNextPaymentDate

**Priority:** Critical
**Type:** Unit
**AC Reference:** AC-1

#### Preconditions
- `models/Payment.php` после коммита `9025b6f`

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `CreditDebtTest::testAfterSaveDoesNotCallRecalculateNextPaymentDate` | PASS |
| 2 | Mock проверяет: `recalculateDebt` вызван 1 раз, `recalculateNextPaymentDate` — 0 раз | ✅ |

#### Actual Result
```
CreditDebtTest: After save does not call recalculate next payment date ✅ (0.00s)
```

---

### TC-14-02: afterDelete не вызывает recalculateNextPaymentDate

**Priority:** Critical
**Type:** Unit
**AC Reference:** AC-1, AC-4

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `CreditDebtTest::testAfterDeleteDoesNotCallRecalculateNextPaymentDate` | PASS |

#### Actual Result
```
CreditDebtTest: After delete does not call recalculate next payment date ✅ (0.00s)
```

---

### TC-14-03: Строка recalculateNextPaymentDate удалена из Payment.php

**Priority:** Critical
**Type:** Static
**AC Reference:** AC-1, AC-4

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `grep "recalculateNextPaymentDate" models/Payment.php` | 0 совпадений |

#### Actual Result
```
grep: 0 matches ✅
```

---

### TC-15-01: month_payment рассчитывается через round без intval

**Priority:** Critical
**Type:** Unit
**AC Reference:** AC-3

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `CreditDebtTest::testMonthPaymentRoundingWithoutIntval` | PASS |
| 2 | `debt=1999.99, percant=5` → `round(1999.99*5/100,2) = 100.00` | ✅ |
| 3 | `intval` версия даёт другой результат | ✅ assertNotEquals |

#### Actual Result
```
CreditDebtTest: Month payment rounding without intval ✅ (0.00s)
```

---

### TC-15-02: Debt не вычитается дважды после afterSave

**Priority:** Critical
**Type:** Unit
**AC Reference:** AC-2

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `CreditDebtTest::testDebtNotDoubleSubtracted` | PASS |
| 2 | `sum=5000, paid=2000, newPayment=500` → `debt=2500` (не 1500) | ✅ |

#### Actual Result
```
CreditDebtTest: Debt not double subtracted ✅ (0.00s)
```

---

### TC-15-03: Regression suite — 40/40

**Priority:** Critical
**Type:** Unit Regression
**AC Reference:** AC-5

#### Steps

| # | Action | Expected Result |
|---|--------|----------------|
| 1 | `php vendor/codecept run unit` | OK (40 tests, 65 assertions) |

#### Actual Result
```
Time: 653 ms, Memory: 18.00MB
OK (40 tests, 65 assertions) ✅
```

---

## Files Changed

| Файл | Изменение |
|------|-----------|
| `models/Payment.php` | Удалён `recalculateNextPaymentDate()` из afterSave/afterDelete |
| `controllers/CreditController.php` | Убрано двойное вычитание debt; `intval` → `round` |
| `tests/unit/models/CreditDebtTest.php` | +4 теста |
