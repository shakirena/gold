# Test Cases: Feature #3 — Использовать Credit.debt как базу при создании штрафа

**Issue:** #3  
**Story:** Как менеджер, я хочу чтобы форма создания штрафа автоматически подставляла текущий остаток долга (`Credit.debt`) в качестве базы расчёта.  
**Date:** 2026-04-16  
**QA Agent:** qa-lead

---

## Acceptance Criteria

**AC-1 (Prefill):** Given кредит с sum=1000, debt=600; When менеджер открывает форму создания штрафа с `?id_credit=X`; Then поле суммы предзаполнено значением 600 (Credit.debt), не 1000 (Credit.sum).

**AC-2 (Block on zero debt):** Given кредит с debt=0; When менеджер пытается сохранить штраф; Then форма выдаёт ошибку "Нельзя начислить штраф: остаток долга равен нулю."

---

## Test Cases

### TC-3-01: Предзаполнение формы использует Credit.debt, а не Credit.sum

| Поле | Значение |
|------|---------|
| **ID** | TC-3-01 |
| **Priority** | Critical |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** Credit.sum = 1000.0, Credit.debt = 600.0  
**When:** FineController::actionCreate() выполняет `$model->sum = $credit->debt`  
**Then:** `$model->sum == 600.0`, `$model->sum != 1000.0`

**Unit Test:** `tests/unit/models/FineValidationTest.php::testPrefillUsesDebtNotSum`

---

### TC-3-02: Штраф = долгу (граничный случай — полная сумма долга)

| Поле | Значение |
|------|---------|
| **ID** | TC-3-02 |
| **Priority** | High |
| **AC** | AC-1, AC-2 |
| **Type** | Unit |

**Given:** Credit.debt = 600.0, Fine.sum = 600.0  
**When:** validateFineSum выполняется  
**Then:** валидация проходит (sum <= debt && debt > 0)

**Unit Test:** `tests/unit/models/FineValidationTest.php::testFineSumEqualToDebtIsValid`

---

### TC-3-03: Штраф превышает долг — ошибка валидации

| Поле | Значение |
|------|---------|
| **ID** | TC-3-03 |
| **Priority** | Critical |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** Credit.debt = 600.0, Fine.sum = 700.0  
**When:** validateFineSum выполняется  
**Then:** ошибка "Сумма штрафа не может превышать остаток долга"

**Unit Test:** `tests/unit/models/FineValidationTest.php::testFineSumExceedsDebtIsInvalid`

---

### TC-3-04: Штраф невозможен при debt = 0

| Поле | Значение |
|------|---------|
| **ID** | TC-3-04 |
| **Priority** | Critical |
| **AC** | AC-2 |
| **Type** | Unit |

**Given:** Credit.debt = 0.0  
**When:** validateFineSum выполняется  
**Then:** ошибка "Нельзя начислить штраф: остаток долга равен нулю."

**Unit Test:** `tests/unit/models/FineValidationTest.php::testFineNotAllowedWhenDebtIsZero`

---

### TC-3-05: Штраф невозможен при debt < 0 (защита от переплаты)

| Поле | Значение |
|------|---------|
| **ID** | TC-3-05 |
| **Priority** | High |
| **AC** | AC-2 |
| **Type** | Unit |

**Given:** Credit.debt = -10.0  
**When:** validateFineSum выполняется  
**Then:** штраф заблокирован (debt > 0 условие не выполнено)

**Unit Test:** `tests/unit/models/FineValidationTest.php::testFineNotAllowedWhenDebtIsNegative`

---

### TC-3-06: Частичный штраф валиден

| Поле | Значение |
|------|---------|
| **ID** | TC-3-06 |
| **Priority** | Medium |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** Credit.debt = 600.0, Fine.sum = 100.0  
**When:** validateFineSum выполняется  
**Then:** валидация проходит

**Unit Test:** `tests/unit/models/FineValidationTest.php::testPartialFineIsValid`

---

### TC-3-07: Штраф невозможен после полного погашения кредита

| Поле | Значение |
|------|---------|
| **ID** | TC-3-07 |
| **Priority** | Critical |
| **AC** | AC-2 |
| **Type** | Unit |

**Given:** Credit.sum = 1000.0, суммарные платежи = 1000.0 → Credit.debt = 0.0  
**When:** менеджер пытается создать штраф  
**Then:** создание заблокировано

**Unit Test:** `tests/unit/models/FineValidationTest.php::testCannotCreateFineAfterFullRepayment`

---

### TC-3-08: Fine.sum в форме = Credit.debt (точное соответствие AC)

| Поле | Значение |
|------|---------|
| **ID** | TC-3-08 |
| **Priority** | High |
| **AC** | AC-1 |
| **Type** | Unit |

**Given:** Fine.sum = Credit.debt (prefill AC scenario)  
**When:** validateFineSum выполняется  
**Then:** валидация проходит (sum within debt)

**Unit Test:** `tests/unit/models/FineValidationTest.php::testFineSumWithinDebtIsValid`

---

## Coverage Summary

| AC | TCs | Unit Tests | Status |
|----|-----|-----------|--------|
| AC-1 (prefill) | TC-3-01, TC-3-02, TC-3-03, TC-3-06, TC-3-08 | 5 tests | ✅ PASS |
| AC-2 (block zero) | TC-3-04, TC-3-05, TC-3-07 | 3 tests | ✅ PASS |
| **Total** | **8 TCs** | **8 tests** | **✅ 100%** |
