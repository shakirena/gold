# Story #6 — Автоматически пересчитывать дату следующего платежа

## Статус
**Phase:** Analysis complete → ready-for-dev
**Date:** 2026-04-16

---

## 📋 Задача (analyst)

**User Story:** Как менеджер, я хочу чтобы дата следующего платежа автоматически обновлялась при внесении платежа, чтобы статус кредита (просрочен/скоро/норма) всегда был актуальным.

**AC:**
- [ ] AC-1: платёж 300 при month_payment=300 → date_constribution сдвигается +1 месяц
- [ ] AC-2: удаление платежа → дата откатывается к date_constribution_start
- [ ] AC-3: платёж 600 при month_payment=300 → сдвиг +2 месяца
- [ ] AC-4: частичный платёж (sum < month_payment) → дата не меняется
- [ ] AC-5: два платежа по 200 при month_payment=300 → сдвиг +1 месяц (SUM=400>300)
- [ ] AC-6: покрыто unit-тестами

**Вне scope:** UI, уведомления, упрощение getDateConstribution(), исторические кредиты

---

## User Stories Created

- #7: Добавить recalculateNextPaymentDate() + миграция + Payment hooks
- #8: Unit-тесты расчёта даты

---

## 🏗️ Архитектура (architect)

**DB Changes:** Новый столбец `credit.date_constribution_start DATE` (миграция)

**Modified Files:**
- `console/migrations/m260416_000001_add_date_constribution_start.php` — новый
- `models/Credit.php` — метод `recalculateNextPaymentDate()`
- `models/Payment.php` — вызов в `afterSave()`/`afterDelete()`
- `controllers/CreditController.php` — заполнять `date_constribution_start` при создании
- `tests/unit/models/CreditDateTest.php` — новый

**Key Decisions:**
- ADR-1: Добавить `date_constribution_start` (исходная дата) — позволяет идемпотентный пересчёт
- ADR-2: `recalculateNextPaymentDate()` — SUM(payments) → кол-во месяцев → сдвиг от start
- ADR-3: Вызывать рядом с `recalculateDebt()` в Payment hooks
- ADR-4: `getDateConstribution()` упрощается позже, не в scope

**Dependencies:** #2 (recalculateDebt паттерн) → #6 → #7 → #8

**Spec:** `docs/specs/feature-6-auto-date-recalculation.md`
**Arch:** `docs/arch/feature-6-auto-date-recalculation.md`
