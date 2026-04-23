---
name: Story #11: Убрать инпут даты следующей оплаты
description: Analyst + architect notes for feature #11 — remove next payment date DatePicker from view_credit.php
type: project
---

# Story #11: Убрать инпут даты следующей оплаты при ежемесячном платеже

## 📋 Задача (analyst)
**Цель:** Удалить disabled DatePicker "Növbəti ödəniş" из строки ежемесячного платежа в view_credit.php
**Role:** менеджер
**Value:** Упрощает UX, убирает нередактируемый элемент, исключает путаницу

### User Stories Created
- #12: Story: Убрать DatePicker следующей оплаты из формы view-credit

### Key ACs
1. DatePicker "Növbəti ödəniş" не отображается в строке ежемесячного платежа
2. Дата продолжает автоматически пересчитываться через Payment::afterSave()

### Affected Modules
- views/credit/view_credit.php (строки 169–183)

### Dependencies
none

---

## 🏗️ Архитектура (architect)
**DB Changes:** нет
**New Files:** нет
**Modified Files:**
- views/credit/view_credit.php — удалить `<td>` с DatePicker (строки 169–183)

**Key Decisions:**
- ADR-1: Просто удалить disabled DatePicker из HTML — JS-логика не нужна
- Дата уже пересчитывается в Payment::afterSave() → Credit::recalculateNextPaymentDate()

**Security Notes:**
- Нет новых endpoint'ов, нет изменений в логике доступа
- Нет новых user inputs

---

## 🔒 Security Review (security-reviewer)
**Result:** PASS ✅
**Critical:** 0
**High:** 0
**Medium:** 0
**Notes:** Чисто UI-удаление disabled виджета, никаких уязвимостей не введено

---

## 👨‍💻 Development
**Commit:** 6fc9f6f
**Branch:** update-1
**Changed:** views/credit/view_credit.php (-18 lines)
**Tests:** 36/36 green
