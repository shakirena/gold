# Test Cases: Story #2 — Автоматически уменьшать Credit.debt при сохранении платежа

**Feature:** #2 (Parent: #1)
**Spec:** docs/specs/feature-1-fine-partial-payment.md
**Arch:** docs/arch/feature-1-fine-partial-payment.md
**Created:** 2026-04-15

---

## TC-2-01: Частичный платёж уменьшает Credit.debt — Happy Path

**Priority:** Critical
**Type:** Functional
**AC Reference:** AC-1

### Preconditions
- Кредит #X с `sum = 1000`, `debt = 1000`, без платежей
- Пользователь авторизован как cashier/manager

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Открыть /payment/create с id_credit=X | Форма создания платежа |
| 2 | Заполнить sum = 400, datetime = now | Поля заполнены |
| 3 | Нажать "Сохранить" | Платёж сохранён, redirect на /payment/view |
| 4 | Открыть /credit/view-credit?id=X | Страница кредита |
| 5 | Проверить поле debt | debt = 600 |

### Expected Result
`Credit.debt` = 600 (1000 - 400). Изменение произошло автоматически без ручного ввода.

### Test Data
- credit.sum = 1000, credit.debt = 1000
- payment.sum = 400

---

## TC-2-02: Полное погашение — debt становится 0

**Priority:** Critical
**Type:** Functional
**AC Reference:** AC-1

### Preconditions
- Кредит с `sum = 1000`, `debt = 1000`

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Создать платёж sum = 1000 | Платёж сохранён |
| 2 | Проверить debt кредита | debt = 0 |

### Expected Result
`Credit.debt` = 0.

### Test Data
- payment.sum = 1000 (= debt)

---

## TC-2-03: Переплата — debt не становится отрицательным

**Priority:** Critical
**Type:** Edge Case / Negative
**AC Reference:** AC-1

### Preconditions
- Кредит с `sum = 1000`, `debt = 300` (уже частично оплачен)

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Создать платёж sum = 500 (> debt=300) | Платёж сохранён |
| 2 | Проверить debt кредита | debt = 0 (не -200) |

### Expected Result
`Credit.debt` = 0, не отрицательное.

---

## TC-2-04: Несколько платежей накапливаются корректно

**Priority:** High
**Type:** Functional
**AC Reference:** AC-1

### Preconditions
- Кредит с `sum = 1000`, `debt = 1000`

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Создать платёж sum = 200 | debt = 800 |
| 2 | Создать платёж sum = 150 | debt = 650 |
| 3 | Создать платёж sum = 50 | debt = 600 |

### Expected Result
После каждого платежа `debt` пересчитывается от `sum - SUM(all payments)`.

---

## TC-2-05: Удаление платежа восстанавливает debt

**Priority:** High
**Type:** Functional
**AC Reference:** AC-1 (afterDelete)

### Preconditions
- Кредит с `sum = 1000`, один платёж `400` → `debt = 600`

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Удалить платёж 400 | Redirect на /payment/index |
| 2 | Проверить debt кредита | debt = 1000 (восстановлен) |

### Expected Result
`Credit.debt` возвращается к исходному значению.

---

## TC-2-06: Точность округления (2 знака)

**Priority:** Medium
**Type:** Functional
**AC Reference:** AC-1

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Создать платёж sum = 333.333 при debt=1000 | debt = 666.67 |

### Expected Result
`Credit.debt` = 666.67 (округлено до 2 знаков).

---

## TC-2-07: RBAC — viewer не может создать платёж

**Priority:** Critical
**Type:** Security / Access Control
**AC Reference:** AC-1

### Preconditions
- Пользователь авторизован как viewer

### Steps
| # | Action | Expected Result |
|---|--------|----------------|
| 1 | Открыть /payment/create | HTTP 403 или redirect |

### Expected Result
Доступ запрещён. `Credit.debt` не изменяется.

---

## Related TCs
| TC | Feature | Связь |
|----|---------|-------|
| TC-1-* | Feature #1 (parent) | AC этой story входят в AC родительского issue |
| TC-3-* | Story #3 (Fine base) | Зависит от корректного debt из этой story |
