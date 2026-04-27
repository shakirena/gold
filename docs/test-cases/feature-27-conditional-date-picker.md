# Test Cases: Feature #27 — Conditional Date Picker Visibility

## TC-27-01: Date row hidden on page load

**Given** the credit detail page loads with any credit
**When** the page is rendered
**Then** the element `#next-payment-date-row` has `display:none` (not visible to user)

**Type:** Frontend / Visual  
**Priority:** Critical  
**AC:** AC-1

---

## TC-27-02: Date row appears when monthly payment > 0

**Given** the monthly payment input `#month_payment` has value 0
**When** the user types a positive number (e.g. 150)
**Then** `#next-payment-date-row` becomes visible (`display` changes from `none`)

**Type:** Frontend / Interaction  
**Priority:** Critical  
**AC:** AC-2

---

## TC-27-03: Date row hides when monthly payment cleared to 0

**Given** `#month_payment` has a positive value and the date row is visible
**When** the user clears the field (empty) or sets it to 0
**Then** `#next-payment-date-row` hides again

**Type:** Frontend / Interaction  
**Priority:** Critical  
**AC:** AC-3

---

## TC-27-04: Monthly payment field defaults to 0 on page load

**Given** the credit detail page loads
**When** the form is rendered
**Then** `#month_payment` input has value `0` (not the model's month_payment value)

**Type:** Frontend / Visual  
**Priority:** High  
**AC:** AC-4

---

## TC-27-05: Date input pre-filled with +1 month when row appears

**Given** the date row is visible (user entered monthly payment > 0)
**When** the user views the date field
**Then** `#next_payment_date` is pre-filled with the date = current `date_constribution` + 1 month

**Type:** Frontend / Visual  
**Priority:** Medium  
**AC:** AC-5

---

## TC-27-06: Debt/fine payments unaffected (regression)

**Given** the user enters only debt or fine amounts (not monthly payment)
**When** the user does NOT enter a value in `#month_payment`
**Then** the date row remains hidden and the AJAX call does not send `date_constribution`

**Type:** Regression  
**Priority:** High  
**AC:** AC-1, AC-2

---

## TC-27-07: Unit tests remain green after change

**Given** the codebase with feature #27 changes applied
**When** `php vendor/bin/codecept run unit` is executed
**Then** all 69 tests pass, 96 assertions

**Type:** Regression  
**Priority:** Critical
