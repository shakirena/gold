# Architecture: Feature #16 — Авторасчёт date_constribution

## ADR-1: Дата рассчитывается на бэкенде через strtotime

**Context:** После удаления DatePicker фронтенд передаёт `date=""`. Нужно сделать бэкенд автономным.

**Decision:** В `actionPaymentMonth` и `actionDeleteMonth` использовать `strtotime('+1 MONTH')` / `strtotime('-1 MONTH')` на текущем значении `$model->date_constribution`. Параметр `$date` из запроса игнорируется.

**Consequences:** Дата всегда ровно +1/-1 месяц. Нет зависимости от фронтенда. JS параметр `date` становится избыточным.

---

## ERD

Изменений в схеме БД нет.

---

## Затронутые файлы

| Файл | Изменение |
|------|-----------|
| `controllers/CreditController.php` | `actionPaymentMonth`: дата = +1M; `actionDeleteMonth`: дата = -1M |
| `web/js/main.js` | Убрать `date: $("#date").val()` из AJAX; убрать `&date=...` из deleteMonthBtn |

---

## Diff-план

### CreditController.php — actionPaymentMonth
```php
// ЗАМЕНИТЬ:
$model->date_constribution = $date;
// НА:
$model->date_constribution = date('Y-m-d', strtotime('+1 MONTH', strtotime($model->date_constribution)));
```

### CreditController.php — actionDeleteMonth
```php
// ЗАМЕНИТЬ:
$model->date_constribution = $date;
// НА:
$model->date_constribution = date('Y-m-d', strtotime('-1 MONTH', strtotime($model->date_constribution)));
```

### web/js/main.js — строка 171
```js
// УБРАТЬ: date:$("#date").val()
$.get('payment-month', {sum:..., note:..., id_credit:id}, function(data){...});
```

### web/js/main.js — строка 42 (deleteMonthBtn)
```js
// ЗАМЕНИТЬ:
window.location.href = 'delete-month?id='+id+'&date='+$("#date").val();
// НА:
window.location.href = 'delete-month?id='+id;
```

---

## Security Considerations

- Нет новых endpoint'ов
- Убирается user-controlled параметр `$date` — **улучшает** безопасность (дата не может быть подделана с фронтенда)
- CSRF без изменений
