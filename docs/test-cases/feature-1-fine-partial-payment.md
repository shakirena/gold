# Test Cases: Feature #1 — Исправить расчёт штрафов при частичном погашении

**Issue:** #1 (Parent)
**Type:** type:bug
**Date:** 2026-04-16
**QA Agent:** qa-lead

---

## Acceptance Criteria

**AC-1:** При частичном погашении долга остаток для расчёта штрафа уменьшается на сумму платежа.
**AC-2:** Штраф начисляется только на фактическую сумму просроченного остатка (`Credit.debt`).
**AC-3:** История платежей корректно отражает уменьшение штрафной базы.
**AC-4:** Покрыто unit-тестами (Codeception) для граничных случаев: полное погашение, несколько частичных платежей, нулевой остаток.

---

## Coverage Map: AC → Child Stories → Tests

| AC | Реализовано в | TC документ | Тесты |
|----|--------------|-------------|-------|
| AC-1 | Story #2 | feature-2-auto-debt-update.md | CreditDebtTest (8 тестов) |
| AC-2 | Story #3 | feature-3-fine-debt-prefill.md | FineValidationTest (8 тестов) |
| AC-3 | Story #2 | feature-2-auto-debt-update.md | CreditDebtTest::testRecalculateDebtMultiplePayments |
| AC-4 | Story #4 | feature-4-unit-tests-debt.md | CreditDebtTest + FineValidationTest (19 тестов) |

---

## Сводные Test Cases

### TC-1-01: AC-1 — Частичный платёж уменьшает штрафную базу

**Given** кредит с `debt = 1000`, платёж на сумму `400`
**When** `Payment::afterSave()` выполняется → `Credit::recalculateDebt()`
**Then** `Credit.debt = 600`

**Покрыто:** TC-2-01, TC-4-01 | `CreditDebtTest::testRecalculateDebtPartialPayment` ✅

---

### TC-1-02: AC-1 — Несколько платежей суммируются

**Given** кредит с `debt = 1000`, платежи [200, 150, 50]
**When** каждый `Payment` сохраняется
**Then** `Credit.debt = 600`

**Покрыто:** TC-4-04 | `CreditDebtTest::testRecalculateDebtMultiplePayments` ✅

---

### TC-1-03: AC-1 — Переплата не делает debt отрицательным

**Given** кредит с `debt = 100`, платёж на `150`
**When** `recalculateDebt()` выполняется
**Then** `Credit.debt = 0` (не -50)

**Покрыто:** TC-4-03 | `CreditDebtTest::testRecalculateDebtOverpayment` ✅

---

### TC-1-04: AC-2 — Форма штрафа предзаполнена текущим долгом

**Given** кредит с `sum = 1000`, `debt = 600`
**When** менеджер открывает форму создания штрафа (`?id_credit=X`)
**Then** поле суммы предзаполнено `600` (не `1000`)

**Покрыто:** TC-3-01 | `FineValidationTest::testPrefillUsesDebtNotSum` ✅

---

### TC-1-05: AC-2 — Штраф заблокирован при debt = 0

**Given** кредит с `debt = 0` (полностью погашен)
**When** менеджер пытается создать штраф
**Then** валидация возвращает ошибку "Нельзя начислить штраф: остаток долга равен нулю."

**Покрыто:** TC-3-04, TC-3-07 | `FineValidationTest::testFineNotAllowedWhenDebtIsZero` ✅

---

### TC-1-06: AC-3 — История платежей отражает актуальный debt

**Given** кредит с `debt = 1000`, два платежа: 300 и 200
**When** оба платежа сохранены
**Then** `Credit.debt = 500` (пересчитан через SUM payments)

**Покрыто:** TC-2-01..TC-2-06, TC-4-04 | `CreditDebtTest` (множественные платежи) ✅

---

### TC-1-07: AC-4 — Граничный случай: полное погашение

**Given** кредит с `debt = 1000`, платёж на `1000`
**When** `recalculateDebt()` выполняется
**Then** `Credit.debt = 0`

**Покрыто:** TC-4-02 | `CreditDebtTest::testRecalculateDebtFullPayment` ✅

---

### TC-1-08: AC-4 — Граничный случай: нулевой остаток блокирует штраф

**Given** `Credit.debt = 0` (после полного погашения)
**When** запускается `php vendor/bin/codecept run unit`
**Then** тест `testFineNotAllowedWhenDebtIsZero` зелёный

**Покрыто:** TC-3-04 | `FineValidationTest::testFineNotAllowedWhenDebtIsZero` ✅

---

## G5 Coverage Summary

| AC | TCs | Child Story | Статус |
|----|-----|-------------|--------|
| AC-1 (debt уменьшается) | TC-1-01, TC-1-02, TC-1-03 | #2, #4 | ✅ PASS |
| AC-2 (штраф от debt) | TC-1-04, TC-1-05 | #3 | ✅ PASS |
| AC-3 (история платежей) | TC-1-06 | #2 | ✅ PASS |
| AC-4 (unit tests) | TC-1-07, TC-1-08 | #4 | ✅ PASS |
| **Total** | **8 TCs** | **27 unit tests** | **✅ PASS** |
