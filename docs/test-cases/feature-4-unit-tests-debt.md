# Test Cases: Feature #4 — Unit-тесты расчёта штрафной базы при частичных платежах

**Issue:** #4 (Parent: #1)
**Date:** 2026-04-16
**QA Agent:** qa-lead

---

## Acceptance Criteria

**AC-1:** после платежа 400 на кредит sum=1000 → `debt = 600`
**AC-2:** после платежа 1000 → `debt = 0` (полное погашение)
**AC-3:** после платежа 1500 → `debt = 0` (не отрицательный)
**AC-4:** `getPayment()` возвращает корректную сумму уплаченного (`sum - debt`)

---

## Test Cases

### TC-4-01: Частичный платёж уменьшает debt

| Поле | Значение |
|------|---------|
| **ID** | TC-4-01 |
| **Priority** | Critical |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, `totalPaid = 400`
**When:** `recalculateDebt()` выполняется
**Then:** `debt = 600`

**Unit Test:** `CreditDebtTest::testRecalculateDebtPartialPayment`

---

### TC-4-02: Полное погашение обнуляет debt

| Поле | Значение |
|------|---------|
| **ID** | TC-4-02 |
| **Priority** | Critical |
| **AC** | AC-2 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, `totalPaid = 1000`
**When:** `recalculateDebt()` выполняется
**Then:** `debt = 0`

**Unit Test:** `CreditDebtTest::testRecalculateDebtFullPayment`

---

### TC-4-03: Переплата не делает debt отрицательным

| Поле | Значение |
|------|---------|
| **ID** | TC-4-03 |
| **Priority** | Critical |
| **AC** | AC-3 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, `totalPaid = 1500`
**When:** `recalculateDebt()` выполняется
**Then:** `debt = 0` (не -500)

**Unit Test:** `CreditDebtTest::testRecalculateDebtOverpayment`

---

### TC-4-04: Несколько платежей суммируются корректно

| Поле | Значение |
|------|---------|
| **ID** | TC-4-04 |
| **Priority** | High |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, payments = [200, 150, 50]
**When:** `recalculateDebt()` выполняется
**Then:** `debt = 600`

**Unit Test:** `CreditDebtTest::testRecalculateDebtMultiplePayments`

---

### TC-4-05: Округление до 2 знаков

| Поле | Значение |
|------|---------|
| **ID** | TC-4-05 |
| **Priority** | Medium |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, `totalPaid = 333.333`
**When:** `recalculateDebt()` выполняется
**Then:** `debt = 666.67`

**Unit Test:** `CreditDebtTest::testRecalculateDebtRounding`

---

### TC-4-06: afterSave вызывает recalculateDebt

| Поле | Значение |
|------|---------|
| **ID** | TC-4-06 |
| **Priority** | High |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** Payment сохранён
**When:** `afterSave()` выполняется
**Then:** `recalculateDebt()` вызван ровно 1 раз

**Unit Test:** `CreditDebtTest::testAfterSaveTriggersRecalculate`

---

### TC-4-07: afterDelete вызывает recalculateDebt

| Поле | Значение |
|------|---------|
| **ID** | TC-4-07 |
| **Priority** | High |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** Payment удалён
**When:** `afterDelete()` выполняется
**Then:** `recalculateDebt()` вызван ровно 1 раз

**Unit Test:** `CreditDebtTest::testAfterDeleteTriggersRecalculate`

---

### TC-4-08: null credit не вызывает ошибку

| Поле | Значение |
|------|---------|
| **ID** | TC-4-08 |
| **Priority** | Medium |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** `credit = null` (кредит удалён из БД)
**When:** `afterSave()` выполняется
**Then:** исключение не выброшено

**Unit Test:** `CreditDebtTest::testAfterSaveNullCreditIsHandled`

---

### TC-4-09: getPayment() возвращает сумму уплаченного (частичное погашение)

| Поле | Значение |
|------|---------|
| **ID** | TC-4-09 |
| **Priority** | Critical |
| **AC** | AC-4 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, `creditDebt = 600`
**When:** `getPayment()` вычисляется как `sum - debt`
**Then:** результат = 400

**Unit Test:** `CreditDebtTest::testGetPaymentReturnsAmountPaid`

---

### TC-4-10: getPayment() при полном погашении = sum

| Поле | Значение |
|------|---------|
| **ID** | TC-4-10 |
| **Priority** | High |
| **AC** | AC-4 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, `creditDebt = 0`
**When:** `getPayment()` вычисляется
**Then:** результат = 1000

**Unit Test:** `CreditDebtTest::testGetPaymentAfterFullRepayment`

---

### TC-4-11: getPayment() при переплате = sum (debt зажат в 0)

| Поле | Значение |
|------|---------|
| **ID** | TC-4-11 |
| **Priority** | High |
| **AC** | AC-4 |
| **Type** | Unit |

**Given:** `creditSum = 1000`, `creditDebt = 0` (переплата, debt не отрицательный)
**When:** `getPayment()` вычисляется
**Then:** результат = 1000

**Unit Test:** `CreditDebtTest::testGetPaymentAfterOverpayment`

---

## Coverage Summary

| AC | TCs | Unit Tests | Status |
|----|-----|-----------|--------|
| AC-1 (debt recalculation) | TC-4-01..TC-4-08 | 8 tests | ✅ PASS |
| AC-4 (getPayment) | TC-4-09..TC-4-11 | 3 tests | ✅ PASS |
| **Total** | **11 TCs** | **11 tests** | **✅ 100%** |
