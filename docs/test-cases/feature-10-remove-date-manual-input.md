# Test Cases: Feature #10 — Заменить DatePicker date_constribution на date_constribution_start

**Issue:** #10  
**Parent:** #9  
**Date:** 2026-04-17  
**Status:** QA Passed ✅

---

## Test Case Summary

| TC ID | Description | Priority | Type | Status |
|-------|-------------|----------|------|--------|
| TC-10-01 | DatePicker date_constribution отсутствует в _form.php | Critical | Static | ✅ Pass |
| TC-10-02 | DatePicker date_constribution_start присутствует в _form.php | Critical | Static | ✅ Pass |
| TC-10-03 | actionCreate устанавливает date_constribution = date_constribution_start | Critical | Static | ✅ Pass |
| TC-10-04 | Read-only views не изменились (index, payment, report, view_credit) | High | Static | ✅ Pass |
| TC-10-05 | Все 36 unit-тестов зелёные (нет регрессий) | Critical | Unit | ✅ Pass |

---

## Detailed Test Cases

### TC-10-01: DatePicker date_constribution убран из формы

**Given** views/credit/_form.php после изменений  
**When** поиск по `date_constribution` (не `date_constribution_start`)  
**Then** поле `date_constribution` не встречается в форме как input

```
grep "date_constribution" views/credit/_form.php
→ Только date_constribution_start (строки 143, 147)
```

---

### TC-10-02: DatePicker date_constribution_start присутствует

**Given** views/credit/_form.php  
**When** открыть форму /credit/create  
**Then** DatePicker для `date_constribution_start` присутствует (строка 147)

---

### TC-10-03: Контроллер устанавливает дату автоматически

**Given** controllers/CreditController.php::actionCreate()  
**When** POST с date_constribution_start = '2026-06-01'  
**Then** `$model->date_constribution = $model->date_constribution_start` выполняется до save() (строка 523)

---

### TC-10-04: Read-only views не изменились

**Given** index.php, payment.php, report.php, view_credit.php  
**When** проверка на наличие date_constribution  
**Then** все отображают date_constribution read-only как прежде (не изменялись)

---

### TC-10-05: Regression suite

**Given** изменения внесены в _form.php и CreditController.php  
**When** `php vendor/codecept/base/codecept run unit`  
**Then** 36 tests, 59 assertions — OK, регрессий нет

```
Time: 805 ms, Memory: 18.00MB
OK (36 tests, 59 assertions)
```

---

## Files Changed

| Файл | Изменение |
|------|-----------|
| `views/credit/_form.php` | DatePicker `date_constribution` → `date_constribution_start` |
| `controllers/CreditController.php` | `date_constribution = date_constribution_start` перед save() |
