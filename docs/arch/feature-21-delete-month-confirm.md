# Architecture: Feature #21 — Подтверждение удаления Month-платежа

## Затронутые компоненты

| Файл | Изменение |
|------|-----------|
| `web/js/main.js` | `deletMonth(id, date)` → `deletMonth(id)` с `confirm()` |
| `views/credit/view_credit.php` | убрать `date` из вызова `deletMonth(...)` |
| `views/credit/show_date.php` | удалить файл |
| `controllers/CreditController.php` | удалить `actionShowDate` |

## До / После

### JS (main.js)

**До:**
```js
function deletMonth(id, date) {
    $('#object-create').load(baseUrl + 'credit/show-date?id=' + id + '&date=' + date);
    $('#create').modal('show');
}
```

**После:**
```js
function deletMonth(id) {
    if (confirm('Silmek isteyirsiniz?')) {
        window.location.href = baseUrl + 'credit/delete-month?id=' + id;
    }
}
```

### View (view_credit.php)

**До:**
```php
'onclick' => "deletMonth($mn->id,'$model->date_constribution')"
```

**После:**
```php
'onclick' => "deletMonth($mn->id)"
```

## Что НЕ меняется

- `actionDeleteMonth` — бэкенд без изменений
- `deleteMonthBtn(id)` в main.js — не вызывается после изменения (можно оставить)
- `recalculateNextPaymentDateFromMonth` — без изменений

## Зависимости

Нет. Изменения изолированы в трёх файлах.
