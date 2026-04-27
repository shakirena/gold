# Story #30: Date picker in monthly payment delete confirmation

## 📋 Analyst Notes

**Feature:** Replace native confirm() in deletMonth() with Bootstrap 3 modal + date input.

**Stories:**
- #31 — Full implementation: view modal, JS handler, controller param

**Key ACs:**
1. Click 'Silmek' → Bootstrap modal (not confirm())
2. Date input pre-filled: date_constribution - 1 month
3. User can change date
4. On confirm → date_constribution = chosen date
5. Cancel → no action

**No past-date restriction** for delete (unlike add payment) — deleting old payments may require setting past dates.

**Spec:** `docs/specs/feature-30-delete-month-date-picker.md`

## 🏗️ Architect Notes

**Files to change:**
- `views/credit/view_credit.php`:
  - Add `#delete-month-modal` Bootstrap modal with date input and Sil/Ləğv et buttons
  - Change delete button onclick: `deletMonth($mn->id, '$model->date_constribution')`
- `web/js/main.js`:
  - Replace `deletMonth(id)` with `deletMonth(id, currentDate)` — calc prefill (-1M), show modal
  - Add `#delete-month-confirm` click handler → redirect with `date_constribution` param
  - Remove `deleteMonthBtn(id)` (dead code — only used by old flow)
- `controllers/CreditController.php`:
  - `actionDeleteMonth($id, $date_constribution = null)` — if valid Y-m-d provided, use it; else auto-recalc

**Arch doc:** `docs/arch/feature-30-delete-month-date-picker.md`
