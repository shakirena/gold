# Story #27: UX — hide next-payment date picker unless monthly payment entered

## 📋 Analyst Notes

**Feature:** Conditionally show/hide date row; reset #month_payment default to 0.

**Stories:**
- #28 — Frontend only: hide date row by default, show on #month_payment > 0

**Key ACs:**
1. Date row hidden at page load
2. Date row appears when #month_payment > 0
3. Date row hides when #month_payment cleared to 0
4. #month_payment default value = 0 (not $model->month_payment)
5. AJAX (receivedCredit) unchanged

**Spec:** `docs/specs/feature-27-conditional-date-picker.md`

## 🏗️ Architect Notes

**No backend changes. No DB changes.**

**Files to change:**
- `views/credit/view_credit.php`:
  - Change `$model->month_payment` → `0` in `#month_payment` input default
  - Add `style="display:none"` to the date `<tr>` row
- `web/js/main.js`:
  - Add jQuery `input` handler on `#month_payment`: toggle visibility of `#next-payment-date-row` (or the `<tr>` containing `#next_payment_date`)

**Arch doc:** `docs/arch/feature-27-conditional-date-picker.md`
