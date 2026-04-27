# Feature #27: Conditional Date Picker — Show/Hide Next Payment Date Row

## Description

In `views/credit/view_credit.php`, the `.table-play` form always shows the "Növbəti ödəniş tarixi" (next payment date) row regardless of whether the user intends to make a monthly payment. This creates visual noise and potential confusion: a user who enters 0 in `#month_payment` still sees (and may interact with) the date field, even though the backend `payment-month` AJAX call is only triggered when `#month_payment > 0`.

Additionally, `#month_payment` is currently pre-filled with `$model->month_payment`, which was the fix for Bug #20. Feature #27 reverts the default to `0` so the date row is hidden on page load, and shows the date row only when the user actively enters a positive monthly payment amount.

---

## Functional Requirements

- **FR-1:** The `#month_payment` input default value is `0` (not `$model->month_payment`) on page load.
- **FR-2:** The `<tr>` containing `#next_payment_date` is hidden by default (`style="display:none"`) when the page loads.
- **FR-3:** The date row becomes visible (`display:""` / `show()`) when the value of `#month_payment` is greater than `0`.
- **FR-4:** The date row is hidden again (`display:none` / `hide()`) when `#month_payment` is equal to `0` or empty.

---

## Non-Functional Requirements

- **NFR-1:** The show/hide behavior must be implemented in `web/js/main.js` using jQuery `input` event on `#month_payment`; no inline `onchange` attributes in the view.
- **NFR-2:** The backend (`CreditController`, `Payment` model, `receivedCredit()` JS function) must not be modified; only `view_credit.php` and `main.js` are changed.

---

## Acceptance Criteria

### AC-1: Date row hidden on page load

**Given** a manager opens `view-credit?id={N}` for any credit  
**When** the page finishes loading  
**Then** the `#month_payment` input shows `0` and the "Növbəti ödəniş tarixi" row is not visible

### AC-2: Date row appears when monthly payment is entered

**Given** `#month_payment` currently shows `0` and the date row is hidden  
**When** the manager types a positive number (e.g. `500`) into `#month_payment`  
**Then** the "Növbəti ödəniş tarixi" row becomes visible with the pre-filled date (+1 month from `$model->date_constribution`)

### AC-3: Date row hides when monthly payment is cleared

**Given** the manager has entered a positive value in `#month_payment` and the date row is visible  
**When** the manager clears the field (empty string) or sets it back to `0`  
**Then** the "Növbəti ödəniş tarixi" row is hidden again

### AC-4: Payment-month AJAX call still passes the date

**Given** the manager enters a positive `#month_payment` value and the date row is visible  
**When** the manager clicks "ok"  
**Then** `receivedCredit()` reads `#next_payment_date` and sends it to `payment-month` unchanged (no regression in JS function)

### AC-5: No regression in existing unit tests

**Given** only `view_credit.php` and `main.js` are modified  
**When** `php vendor/bin/codecept run unit` is executed  
**Then** all unit tests pass (0 failures)

---

## Out of Scope

- Changes to `CreditController`, `PaymentController`, or any other controller
- Changes to the `Payment`, `Credit`, or `Fine` ActiveRecord models
- Changes to the `receivedCredit()` JS function logic
- Server-side validation of `month_payment`
- Modifying the pre-filled value of `#next_payment_date` (still +1 month from `$model->date_constribution`)
- Any other rows in `.table-play` (`#fine`, `#sum`, `#note`)
- Mobile / responsive layout changes

---

## Technical Notes

- **File 1:** `views/credit/view_credit.php`, line 166  
  Change: `$model->month_payment` → `0`  
  ```php
  // Before
  <?=Html::input("text",'month_payment',$model->month_payment,['size'=>'10','id'=>'month_payment'])?>
  // After
  <?=Html::input("text",'month_payment',0,['size'=>'10','id'=>'month_payment'])?>
  ```

- **File 2:** `views/credit/view_credit.php`, line 169  
  Add `style="display:none"` to the date row `<tr>` and an `id` for reliable JS targeting:  
  ```html
  <tr id="next-payment-date-row" style="display:none">
  ```

- **File 3:** `web/js/main.js`  
  Add an `input` event listener after DOM-ready (or at the bottom of the file) using jQuery:  
  ```js
  $(document).on('input', '#month_payment', function () {
      var val = parseFloat($(this).val());
      if (val > 0) {
          $('#next-payment-date-row').show();
      } else {
          $('#next-payment-date-row').hide();
      }
  });
  ```

- **Stack:** Yii2 Basic, PHP 7.1, Bootstrap 3, jQuery (already loaded via Yii2 asset pipeline)
- **Related features:** Feature #11 (removed date picker row entirely — reversed here), Bug #20 (pre-fill with `$model->month_payment` — superseded by this feature's FR-1)
- **Related JS:** `receivedCredit()` in `main.js` (line ~162–168) already guards the `payment-month` call with `if ($("#month_payment").val() > 0)` and reads `#next_payment_date` — no changes needed there.
