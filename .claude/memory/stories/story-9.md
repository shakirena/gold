---
name: Story #9 — Убрать ручной ввод date_constribution из формы кредита
description: Анализ и архитектура фичи удаления DatePicker для date_constribution
type: project
---

# Story #9 — Убрать ручной ввод date_constribution из формы кредита

## Статус
**Phase:** ready-for-dev
**Date:** 2026-04-16

---

## 📋 Задача (analyst)

**User Story:** Убрать DatePicker для `date_constribution` из `_form.php`, заменить на DatePicker для `date_constribution_start`. Дата следующего платежа (`date_constribution`) теперь auto-calculated.

**AC:**
- [ ] AC-1: DatePicker для `date_constribution` отсутствует в форме создания кредита
- [ ] AC-2: После создания `date_constribution` = `date_constribution_start` (из формы)
- [ ] AC-3: Read-only отображение в других views не изменилось
- [ ] AC-4: 36/36 unit-тестов зелёные

**Вне scope:** Исторические кредиты, упрощение getDateConstribution(), изменения БД

---

## User Stories Created

- #10: Story: Заменить DatePicker date_constribution на date_constribution_start в форме кредита

---

## 🏗️ Архитектура (architect)

**DB Changes:** нет

**Modified Files:**
- `views/credit/_form.php` — заменить DatePicker `date_constribution` → `date_constribution_start` (строки 143, 147-159)
- `controllers/CreditController.php::actionCreate()` — установить `$model->date_constribution = $model->date_constribution_start` до `save()`; убрать `$model->date_constribution_start = $model->date_constribution` (строка 527)

**Key Decisions:**
- ADR-1: Переименовать поле в форме (не удалять) — пользователь должен задать начальную дату
- ADR-2: Не менять `rules()` — set `date_constribution` в контроллере до validation

**Dependencies:** Blocked by: #7 (recalculateNextPaymentDate реализован)

**Spec:** `docs/specs/feature-9-remove-date-manual-input.md`
**Arch:** `docs/arch/feature-9-remove-date-manual-input.md`
