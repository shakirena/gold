# Story #23: Восстановить выбор даты следующей оплаты при ежемесячном платеже

## 📋 Analyst Notes

**Feature:** Add date picker to monthly payment form so users can manually override next payment date.

**Stories:**
- #24 — Frontend: add date input to `views/credit/view_credit.php`
- #25 — Backend+JS: pass date param through JS → `actionPaymentMonth` → `Credit::date_constribution`

**Key ACs:**
1. Date input shown, pre-filled with `date_constribution + 1 month`
2. User can change date manually
3. Custom date saved to `date_constribution` after payment
4. If no custom date — auto-calculate as before
5. Validation: date cannot be in the past

**Spec:** `docs/specs/feature-23-next-payment-date.md`

## 🏗️ Architect Notes

**No DB schema changes needed.**

**Files to change:**
- `views/credit/view_credit.php` — add `<input type="date" id="next_payment_date">` row in `.table-play`, pre-filled via PHP with `date('Y-m-d', strtotime('+1 MONTH', strtotime($model->date_constribution)))`
- `web/js/main.js` — `receivedCredit(id)`: add `date_constribution: $("#next_payment_date").val()` to `payment-month` AJAX params
- `controllers/CreditController.php` — `actionPaymentMonth`: add optional `$date_constribution = null` param; if provided and valid, set `$model->date_constribution` directly and call `$model->save(false)` instead of `recalculateNextPaymentDateFromMonth()`

**ADR:** Manual date takes precedence over auto-calc. Auto-calc runs only when no valid date provided.

**Arch doc:** `docs/arch/feature-23-next-payment-date.md`
