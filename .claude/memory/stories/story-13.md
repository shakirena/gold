---
name: Story #13: Оплата долга — не сдвигать дату, пересчитывать верно
description: Analyst + architect notes for bug #13 — Payment::afterSave wrongly shifts date_constribution; double-subtract debt; intval truncation
type: project
---

# Story #13: Оплата долга — не сдвигать дату, пересчитывать month_payment верно

## 📋 Задача (analyst)
**Цель:** Исправить 3 бага при оплате основного долга: сдвиг даты, двойное вычитание, неверное округление
**Role:** менеджер
**Value:** Точные финансовые расчёты, верная дата ежемесячного платежа в отчётах

### User Stories Created
- #14: Story: Убрать recalculateNextPaymentDate из Payment::afterSave/afterDelete
- #15: Story: Убрать двойное вычитание debt и исправить intval в actionReceivedCredit

### Key ACs
1. date_constribution не меняется после actionReceivedCredit
2. debt = credit.sum - totalPaid (без двойного вычитания)
3. month_payment = round(debt * percant / 100, 2) (без intval)
4. actionPaymentMonth по-прежнему обновляет дату

### Affected Modules
- models/Payment.php (afterSave, afterDelete)
- controllers/CreditController.php (actionReceivedCredit, actionDeletePayment)
- tests/unit/models/CreditDebtTest.php (новые тесты)

### Dependencies
- #14 и #15 независимы, можно делать параллельно

---

## 🏗️ Архитектура (architect)
**DB Changes:** нет
**New Files:** нет
**Modified Files:**
- models/Payment.php — убрать recalculateNextPaymentDate() из afterSave + afterDelete
- controllers/CreditController.php — убрать двойное вычитание; intval → round
- tests/unit/models/CreditDebtTest.php — добавить regression тесты

**Key Decisions:**
- ADR-1: Дата управляется только через actionPaymentMonth/actionDeleteMonth, не через afterSave
- ADR-2: recalculateDebt() в afterSave/afterDelete — единственный источник истины для debt
- ADR-3: round(debt * percant / 100, 2) — корректное float-округление

**Security Notes:**
- Нет новых endpoint'ов, нет изменений RBAC
- Исправление финансового округления
