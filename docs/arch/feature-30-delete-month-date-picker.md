# Architecture — Feature #30: Date Picker in Monthly Payment Delete Confirmation

## Context

The existing delete flow for monthly payments uses a native browser `confirm()` dialog, which
cannot embed a form field. Feature #30 replaces this with a Bootstrap 3 Modal that includes a
date input, allowing the operator to set (or adjust) the next contribution date before the
deletion is finalised.

---

## Architecture Decision Records

### ADR-1 — Bootstrap 3 Modal instead of native confirm()

**Status:** Accepted

**Context:**  
The native `confirm()` API is a plain text dialog. It cannot render HTML form controls, so there
is no way to embed a date picker inside it.

**Decision:**  
Use a Bootstrap 3 Modal (already available in the project via `AppAsset`) to present the
confirmation UI. The modal renders a standard `<input type="date">` inside `.modal-body`.

**Consequences:**
- Requires adding one `<div class="modal fade">` block to `view_credit.php`.
- The existing `deletMonth` JS function must be rewritten; the `#delete-month-confirm` button
  triggers navigation instead of confirming inline.
- No new CSS/JS libraries needed; Bootstrap 3 and jQuery are already loaded.

---

### ADR-2 — Pass date_constribution via data-attribute / onclick argument; calculate −1M in JS

**Status:** Accepted

**Context:**  
The pre-fill value (`date_constribution − 1 month`) is derived from the Credit model's
`date_constribution` field, which is available in the Yii2 view at render time.

**Decision:**  
Pass `date_constribution` as a second argument to `deletMonth(id, currentDate)` directly in the
`onclick` attribute rendered by the PHP view. The JavaScript function constructs a `Date` object,
subtracts one month via `setMonth(d.getMonth() - 1)`, and writes the result (`YYYY-MM-DD`) into
the modal's date input before showing the modal.

**Consequences:**
- Minimal server round-trip: the date calculation is pure client-side.
- Edge cases (e.g. March 31 → Feb 28) are handled naturally by the JS `Date` API.
- The Credit model's current `date_constribution` is used as the reference point, which is the
  semantically correct value (the next scheduled payment date before this deletion).

---

### ADR-3 — No past-date restriction in the delete flow

**Status:** Accepted

**Context:**  
The add-payment modal sets `min="today"` on the date input to prevent scheduling a new payment
in the past. The delete flow has different semantics: deleting a payment often moves the
contribution date *backwards* (e.g. reinstating a previous month).

**Decision:**  
The `<input type="date">` in `#delete-month-modal` MUST NOT carry a `min` attribute. Any
calendar date is acceptable.

**Consequences:**
- Operators have full flexibility to set historical dates when correcting data entry errors.
- No client-side guard against obviously wrong dates (e.g. year 1900); this is considered
  acceptable given the internal-tool context.

---

## Module Structure

| File | Role | Change type |
|------|------|-------------|
| `views/credit/view_credit.php` | View — renders Credit detail page and monthly payment table | Add modal HTML; update delete button onclick signature |
| `web/js/main.js` | Global JS — shared across all pages | Replace `deletMonth` function; add confirm-button handler |
| `controllers/CreditController.php` | Controller — handles all Credit actions | Add optional `$date_constribution` param to `actionDeleteMonth` |

---

## Code Stubs

### views/credit/view_credit.php

Add the following modal block **after the existing modal** (or before the closing `</div>` of
the main container). It is a single instance shared by all delete buttons on the page; the
month ID is stored in the modal's jQuery `.data()` store at open time.

```php
<!-- Delete Month Modal -->
<div class="modal fade" id="delete-month-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Ayliq ödənişi sil</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Növbəti ödəniş tarixi:</label>
                    <input type="date" id="delete-month-date" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Ləğv et</button>
                <button type="button" class="btn btn-danger" id="delete-month-confirm">Sil</button>
            </div>
        </div>
    </div>
</div>
```

Update the delete button for each monthly payment row — pass both `$mn->id` and the Credit
model's `date_constribution`:

```php
<?= Html::button('<span class="glyphicon glyphicon-trash"></span>', [
    'onclick' => "deletMonth($mn->id, '{$model->date_constribution}')",
    'class'   => 'btn btn-danger btn-xs',
    'title'   => 'Sil',
]) ?>
```

---

### web/js/main.js

Replace the old `deletMonth` function (lines 26–31) with the new implementation and add the
confirm-button handler:

```js
function deletMonth(id, currentDate) {
    var d = new Date(currentDate);
    d.setMonth(d.getMonth() - 1);
    var prefill = d.toISOString().slice(0, 10);
    $('#delete-month-date').val(prefill);
    $('#delete-month-modal').data('month-id', id).modal('show');
}

$('#delete-month-confirm').on('click', function () {
    var id   = $('#delete-month-modal').data('month-id');
    var date = $('#delete-month-date').val();
    $('#delete-month-modal').modal('hide');
    window.location.href = 'delete-month?id=' + id +
        '&date_constribution=' + encodeURIComponent(date);
});
```

> Note: The `$('#delete-month-confirm').on('click', ...)` handler must be registered inside
> `$(document).ready(...)` (or equivalent) to ensure the DOM is available.

---

### controllers/CreditController.php

Modify `actionDeleteMonth` to accept the optional date parameter:

```php
public function actionDeleteMonth($id, $date_constribution = null)
{
    $payment    = Month::find()->where(['id' => $id])->one();
    $deletedSum = (float)$payment->sum;
    $payment->delete();

    $model = Credit::find()->where(['id' => $payment->id_credit])->one();

    if ($date_constribution &&
        preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_constribution)) {
        $model->date_constribution = $date_constribution;
        $model->save(false);
    } else {
        $model->recalculateNextPaymentDateFromMonth(0, $deletedSum);
    }

    return $this->redirect(['view-credit', 'id' => $payment->id_credit]);
}
```

**Key points:**
- `save(false)` skips Yii2 model validation — only `date_constribution` is being updated
  programmatically, so full rule validation is unnecessary and could block the save.
- The regex `^\d{4}-\d{2}-\d{2}$` is a format guard only; it does not validate whether the
  date is a real calendar date. This is intentional and consistent with the rest of the codebase.
- The fallback branch is identical to the pre-Feature-#30 behaviour, ensuring backward
  compatibility for any bookmarked or scripted calls that omit the parameter.

---

## Data Flow

```
[Delete button click]
        |
        v
deletMonth(id, date_constribution)          ← view passes both values
        |
        v
JS: d = date_constribution - 1 month
JS: prefill input, store id in modal data
JS: modal('show')
        |
    [User adjusts date, clicks Sil]
        |
        v
window.location.href = delete-month?id=X&date_constribution=YYYY-MM-DD
        |
        v
actionDeleteMonth($id, $date_constribution)
        |
   [date valid?]
   /           \
 yes            no
  |              |
save directly   recalculateNextPaymentDateFromMonth(0, $deletedSum)
        |
        v
redirect → view-credit?id=...
```
