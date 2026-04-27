# Test Cases: Feature #30 — Date Picker in Delete Monthly Payment

## TC-30-01: Modal opens on delete button click

**Given** a credit has at least one monthly payment in the table
**When** the user clicks the delete button (red trash icon) on a monthly payment row
**Then** the `#delete-month-modal` Bootstrap modal opens (not a native browser confirm dialog)

**Type:** Frontend / Interaction  
**Priority:** Critical  
**AC:** AC-1

---

## TC-30-02: Date input pre-filled with date_constribution minus 1 month

**Given** the credit has `date_constribution = "2026-05-15"`
**When** the delete modal opens
**Then** `#delete-month-date` input is pre-filled with `"2026-04-15"` (one month earlier)

**Type:** Frontend / Visual  
**Priority:** Critical  
**AC:** AC-2

---

## TC-30-03: User can change the date before confirming

**Given** the delete modal is open with a pre-filled date
**When** the user changes the date input to a different value
**Then** the new value is retained and used when the user clicks 'Sil'

**Type:** Frontend / Interaction  
**Priority:** High  
**AC:** AC-3

---

## TC-30-04: Confirm sends correct date_constribution to actionDeleteMonth

**Given** the modal is open with date `"2026-03-10"`
**When** the user clicks 'Sil'
**Then** the browser navigates to `delete-month?id=X&date_constribution=2026-03-10`
**And** `date_constribution` is saved to the Credit model as `"2026-03-10"`

**Type:** Integration  
**Priority:** Critical  
**AC:** AC-4

---

## TC-30-05: Cancel closes modal without deleting

**Given** the delete modal is open
**When** the user clicks 'Ləğv et' or the × close button
**Then** the modal closes, no deletion occurs, date_constribution is unchanged

**Type:** Frontend / Interaction  
**Priority:** Critical  
**AC:** AC-5

---

## TC-30-06: Past dates accepted (no restriction in delete flow)

**Given** the modal is open
**When** the user enters a past date (e.g. `"2025-01-01"`)
**Then** the date is accepted and sent to the controller (unlike add-payment flow which rejects past dates)

**Type:** Business Rule  
**Priority:** High  
**AC:** AC-3, AC-4 — ADR-3

---

## TC-30-07: actionDeleteMonth fallback when no date provided

**Given** `actionDeleteMonth` is called without `date_constribution` param (direct URL)
**When** the action executes
**Then** `recalculateNextPaymentDateFromMonth(0, $deletedSum)` is called (auto-recalc fallback)

**Type:** Backend / Unit  
**Priority:** High  
**AC:** AC-4 (fallback path)

---

## TC-30-08: actionDeleteMonth rejects malformed date, falls back to auto-recalc

**Given** `actionDeleteMonth` is called with `date_constribution = "not-a-date"`
**When** the action executes
**Then** auto-recalc is used (preg_match fails the invalid format)

**Type:** Backend / Security  
**Priority:** High  
**AC:** AC-4

---

## TC-30-09: Unit test suite regression

**Given** the feature #30 changes are applied
**When** `php vendor/bin/codecept run unit` is executed
**Then** all 69 tests pass, 96 assertions — no regressions

**Type:** Regression  
**Priority:** Critical
