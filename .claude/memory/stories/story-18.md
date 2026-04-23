---
name: Story #18: Умный расчёт date_constribution по Month-платежам
description: Feature — recalculateNextPaymentDateFromMonth() based on accumulated Month sum from start date
type: project
---

# Story #18: Умный расчёт date_constribution

## 📋 Задача (analyst)
**Цель:** date_constribution пересчитывается по накопленной сумме Month-платежей от start date
**Role:** менеджер/заёмщик
**Value:** Корректная дата следующей оплаты, учитывающая переплаты

### User Stories Created
- #19: Story: Авторасчёт date_constribution по суммарным Month-платежам

### Key ACs
1. AC-1: totalMonth < month_payment → date = date_constribution_start
2. AC-2: totalMonth == month_payment → start + 1M
3. AC-3: totalMonth == 2×month_payment → start + 2M
4. AC-4: Все предыдущие Month-платежи учитываются
5. AC-5: После удаления Month-платежа — корректный пересчёт от start

### Affected Modules
- models/Credit.php (+recalculateNextPaymentDateFromMonth)
- controllers/CreditController.php (actionPaymentMonth, actionDeleteMonth)

### Dependencies
Связан с #16 (после которого появился простой ±1 MONTH)

---

## 🏗️ Архитектура (architect)
**DB Changes:** нет
**New Files:** нет
**Modified Files:**
- models/Credit.php — новый метод recalculateNextPaymentDateFromMonth()
- controllers/CreditController.php — вызов нового метода вместо strtotime(±1M)

**Key Decisions:**
- ADR-1: date_constribution_start — базовая точка отсчёта (не текущий date_constribution)
- Алгоритм: floor(totalMonthPaid / month_payment) месяцев от start
- save(false) — без повторной валидации

**Security Notes:**
- Нет новых входных данных от пользователя
