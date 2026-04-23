# Story #4 — Unit-тесты расчёта штрафной базы при частичных платежах

## 📋 Задача

**User Story:** Как разработчик, я хочу набор unit-тестов (Codeception), покрывающих граничные случаи обновления `Credit.debt` при платежах, чтобы регрессии в логике расчёта штрафной базы обнаруживались автоматически.

**AC:**
- [x] AC-1: `debt = 600` после платежа 400 на кредит sum=1000
- [x] AC-2: `debt = 0` после платежа 1000 (полное погашение)
- [x] AC-3: `debt = 0` после платежа 1500 (переплата, не отрицательный)
- [ ] AC-4: `getPayment()` возвращает корректную сумму уплаченного

**Вне scope:** functional/acceptance тесты, тесты контроллеров, нагрузочное тестирование

---

## 🏗️ Архитектура

**Изменения в БД:** нет
**Новые классы:** нет (дополнить `tests/unit/models/CreditDebtTest.php`)
**API:** нет

**Gap:** AC-4 (`getPayment()`) не покрыт — нужно добавить `testGetPaymentReturnsAmountPaid`

---

## Зависимости

- Depends on: #2 (recalculateDebt реализован), #3 (validateFineSum реализован)
- Оба в `kanban:ready-to-deploy` → не блокируют

---

## Spec / Arch

- `docs/specs/feature-4-unit-tests-debt.md`
- `docs/arch/feature-4-unit-tests-debt.md`
