---
name: Story #16: Авторасчёт date_constribution при ежемесячном платеже
description: Bug fix — actionPaymentMonth/actionDeleteMonth must calculate date on backend after DatePicker removal in #12
type: project
---

# Story #16: Авторасчёт date_constribution

## 📋 Задача (analyst)
**Цель:** Бэкенд сам сдвигает дату +1/-1 месяц, не полагаясь на фронтенд
**Role:** менеджер
**Value:** Оплата ежемесячного платежа не ломает date_constribution после удаления DatePicker

### User Stories Created
- #17: Story: Авторасчёт date_constribution в actionPaymentMonth и actionDeleteMonth

### Key ACs
1. actionPaymentMonth: date = старая_дата + 1M
2. actionDeleteMonth: date = старая_дата - 1M
3. Независимость от параметра $date с фронтенда

### Affected Modules
- controllers/CreditController.php (actionPaymentMonth, actionDeleteMonth)
- web/js/main.js (убрать date param)

### Dependencies
Связан с #12 (удаление DatePicker)

---

## 🏗️ Архитектура (architect)
**DB Changes:** нет
**New Files:** нет
**Modified Files:**
- controllers/CreditController.php — +1M / -1M через strtotime
- web/js/main.js — убрать date из AJAX и deleteMonthBtn

**Key Decisions:**
- ADR-1: strtotime('+1 MONTH') на бэкенде, не DateInterval (PHP 7.1 compat)
- Убирается user-controlled параметр $date — улучшает безопасность

**Security Notes:**
- Убирается параметр $date с фронтенда — дата не может быть подделана
