# Spec #4: Unit-тесты расчёта штрафной базы при частичных платежах

**Issue:** #4 (Parent: #1)
**Status:** Ready for Dev
**Date:** 2026-04-16
**Author:** analysis-lead (retroactive)

---

## Описание

Story покрывает автоматическую проверку корректности расчёта `Credit.debt` при сохранении и удалении платежей. Цель — обнаруживать регрессии в логике `recalculateDebt()` при каждом деплое.

---

## Functional Requirements

| ID | Требование |
|----|-----------|
| FR-1 | `CreditDebtTest.php` содержит тесты всех граничных случаев изменения `Credit.debt`. |
| FR-2 | Тест проверяет: частичный платёж, полное погашение, переплата (debt не отрицательный). |
| FR-3 | Тест проверяет что `afterSave()` и `afterDelete()` в `Payment` вызывают `recalculateDebt()`. |
| FR-4 | Тест проверяет что `getPayment()` возвращает сумму уплаченного (`sum - debt`). |
| FR-5 | Тест проверяет rounding до 2 знаков после запятой. |

---

## Non-Functional Requirements

| ID | Требование |
|----|-----------|
| NFR-1 | Тесты — только unit (PHPUnit / Codeception Unit), без DB-запросов в бизнес-логике. |
| NFR-2 | Каждый тест независим, не зависит от порядка выполнения. |
| NFR-3 | `php vendor/bin/codecept run unit models/CreditDebtTest.php` — все green. |

---

## Acceptance Criteria

**Given** `Credit.sum = 1000`, набор сценариев оплаты
**When** запускается `php vendor/bin/codecept run unit models/CreditDebtTest.php`
**Then:**
- AC-1: после платежа 400 → `debt = 600`
- AC-2: после платежа 1000 → `debt = 0`
- AC-3: после платежа 1500 → `debt = 0` (не отрицательный)
- AC-4: `getPayment()` возвращает корректную сумму уплаченного (`sum - debt`)

---

## Out of Scope

- Functional/acceptance тесты (UI-уровень)
- Тесты `PaymentController`, `FineController`
- Нагрузочное тестирование
- Race conditions

---

## Technical Notes

| Файл | Изменение |
|------|-----------|
| `tests/unit/models/CreditDebtTest.php` | Основной файл тестов; AC-1..AC-3 уже реализованы в рамках #2. Добавить тест для AC-4 (`getPayment()`). |

### Gap: AC-4 не покрыт

`getPayment()` в `Credit` возвращает `$this->sum - $this->debt`. Существующий `CreditDebtTest.php` проверяет `debt` через `recalculateDebt()`, но не вызывает `getPayment()` напрямую. Нужен отдельный тест.

### Зависимости

```
#2 (recalculateDebt) → #3 (Fine prefill) → #4 (unit tests)
```
#2 и #3 в `kanban:ready-to-deploy` — зависимости разрешены.
