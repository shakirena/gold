# Feature #30 — Date Picker in Monthly Payment Delete Confirmation

## Overview

Replace the native `confirm()` dialog in the monthly payment delete flow with a Bootstrap 3 Modal
that includes a date input, pre-filled to one month before the current `date_constribution`.
The user may adjust the date before confirming deletion.

---

## Functional Requirements

### FR-1 — Modal replaces native confirm
The delete action for a monthly payment (Aylıq ödəniş) MUST open a Bootstrap 3 Modal instead
of calling the browser's native `confirm()` dialog.

### FR-2 — Pre-fill logic
When the modal opens, the date input MUST be pre-filled with `date_constribution − 1 month`,
where `date_constribution` is the current value on the parent Credit record at the time the
delete button is clicked.

### FR-3 — User-editable date
The user MUST be able to change the pre-filled date to any valid calendar date before confirming.
No past-date restriction is applied in the delete flow (unlike the add flow).

### FR-4 — Controller accepts optional date parameter
`CreditController::actionDeleteMonth()` MUST accept an optional `$date_constribution` query
parameter. If the parameter is present and matches the pattern `YYYY-MM-DD`, the controller MUST
set `Credit::date_constribution` to that value and save the record (without full validation).
If the parameter is absent or invalid, the controller MUST fall back to the existing
`recalculateNextPaymentDateFromMonth(0, $deletedSum)` logic.

### FR-5 — Cancel = no action
Clicking the "Ləğv et" (Cancel) button or the modal close button MUST dismiss the modal without
deleting the payment record or navigating away from the page.

---

## Non-Functional Requirements

### NFR-1 — Bootstrap 3 Modal
The modal MUST use Bootstrap 3 markup and classes (`.modal`, `.modal-dialog`, `.modal-sm`,
`.modal-content`, `.modal-header`, `.modal-body`, `.modal-footer`). No Bootstrap 4/5 attributes.

### NFR-2 — No past-date restriction
Unlike the "add payment" flow, the date picker in the delete confirmation MUST NOT set a `min`
attribute on the `<input type="date">`. Deletes may legitimately set a past contribution date.

---

## Acceptance Criteria

### AC-1 — Modal opens on delete click
**Given** a credit view page with at least one monthly payment row  
**When** the user clicks the delete button for a monthly payment  
**Then** a Bootstrap 3 modal titled "Ayliq ödənişi sil" appears and the native `confirm()` is NOT invoked

### AC-2 — Date is pre-filled correctly
**Given** the Credit record has `date_constribution = 2025-06-15`  
**When** the delete modal opens  
**Then** the date input shows `2025-05-15` (one month earlier)

### AC-3 — User can change the date and confirm
**Given** the delete modal is open with pre-filled date `2025-05-15`  
**When** the user changes the date to `2025-04-01` and clicks "Sil"  
**Then** the browser navigates to `delete-month?id=X&date_constribution=2025-04-01`  
**And** the controller sets `Credit::date_constribution = 2025-04-01` and saves

### AC-4 — Controller fallback when no date provided
**Given** a direct request to `delete-month?id=X` with no `date_constribution` param  
**When** the controller processes the request  
**Then** it calls `recalculateNextPaymentDateFromMonth(0, $deletedSum)` as before

### AC-5 — Cancel dismisses modal without deleting
**Given** the delete modal is open  
**When** the user clicks "Ləğv et" or the × close button  
**Then** the modal closes, no navigation occurs, and the payment record is not deleted

---

## Out of Scope

- Changing the delete endpoint URL or HTTP method (remains GET `delete-month`)
- Adding undo / soft-delete functionality
- Any UI changes to the add-payment flow
- Validation of the date against business rules (e.g. must not exceed loan end date)
- Mobile-specific date picker libraries

---

## Technical Notes — Files to Change

| File | Change |
|------|--------|
| `views/credit/view_credit.php` | Add `#delete-month-modal` markup; update delete button onclick to pass `date_constribution` |
| `web/js/main.js` | Replace `deletMonth(id)` with `deletMonth(id, currentDate)`; add `#delete-month-confirm` click handler |
| `controllers/CreditController.php` | Add `$date_constribution = null` param to `actionDeleteMonth`; apply conditional save logic |
