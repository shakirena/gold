# Architecture: Feature #27 — Conditional Date Picker for "Növbəti ödəniş tarixi"

## Контекст

Поле **"Növbəti ödəniş tarixi"** (`#next_payment_date`) отображается всегда, но имеет смысл только
когда пользователь вводит ненулевое значение в поле `#month_payment`.  
Одновременно, предыдущий фикс #20 установил значение по умолчанию `$model->month_payment`, что
означало, что дата была видна при открытии страницы даже без намерения делать платёж.  
Feature #27 возвращает `0` как дефолт для `#month_payment` и скрывает строку с датой до тех пор,
пока пользователь не введёт значение > 0.

---

## ADR-1: Вернуть дефолтное значение #month_payment к 0

**Context:** Bug #20 установил значение по умолчанию `$model->month_payment` для подсказки
пользователю. Это приводит к тому, что строка с датой должна быть сразу видна, что нарушает
целевое поведение Feature #27.

**Decision:** Вернуть `0` как дефолт, чтобы строка с датой стартовала скрытой. Пользователь
вводит сумму вручную → строка появляется → дата доступна.

**Consequences:** Пользователь снова вводит сумму вручную. Это приемлемо, так как скрытие даты
при пустом поле снижает риск случайного проведения платежа с неверной датой.

---

## ADR-2: Скрыть строку даты через inline style, показывать через jQuery `input` event

**Context:** Строка с датой — обычный `<tr>` внутри `.table-play`. Нет существующей
CSS-утилиты для show/hide, вся интерактивность строится на jQuery в `main.js`.

**Decision:** Добавить `style="display:none"` на `<tr>` строки с датой. В `main.js` добавить
обработчик события `input` на `#month_payment`: показывать строку при значении > 0, скрывать
при значении <= 0 или пустом.

**Alternatives considered:**
- CSS класс `.hidden` (Bootstrap 3) — требует дополнительного класса на элементе; `display:none`
  достаточно и явно.
- `change` event вместо `input` — не срабатывает при каждом нажатии клавиши, хуже UX.

**Consequences:** Нет изменений в бэкенде. Логика `receivedCredit()` уже проверяет
`$("#month_payment").val() > 0` перед отправкой — дополнительных изменений не требуется.

---

## Затронутые компоненты

| Файл | Изменение |
|------|-----------|
| `views/credit/view_credit.php` | Дефолт `#month_payment`: `$model->month_payment` → `0`; добавить `style="display:none"` на `<tr>` строки даты |
| `web/js/main.js` | Добавить `input` event handler на `#month_payment` для show/hide строки даты |

Бэкенд, миграции, модели — **без изменений**.

---

## Diff-план

### `views/credit/view_credit.php`

```php
// БЫЛО (строка 166):
<?=Html::input("text",'month_payment',$model->month_payment,[ 'size' => '10','id'=>'month_payment'])?>

// СТАТЬ:
<?=Html::input("text",'month_payment',0,[ 'size' => '10','id'=>'month_payment'])?>
```

```php
// БЫЛО (строка 170):
<tr>
    <td><b>Növbəti ödəniş tarixi: </b></td>
    <td><?=Html::input("date",'next_payment_date', date('Y-m-d', strtotime('+1 MONTH', strtotime($model->date_constribution))), ['id'=>'next_payment_date'])?></td>
    <td></td>
</tr>

// СТАТЬ:
<tr id="next-payment-date-row" style="display:none">
    <td><b>Növbəti ödəniş tarixi: </b></td>
    <td><?=Html::input("date",'next_payment_date', date('Y-m-d', strtotime('+1 MONTH', strtotime($model->date_constribution))), ['id'=>'next_payment_date'])?></td>
    <td></td>
</tr>
```

### `web/js/main.js`

```js
// Добавить в document.ready или сразу после определения функции receivedCredit():
$(document).on('input', '#month_payment', function () {
    var val = parseFloat($(this).val());
    if (val > 0) {
        $('#next-payment-date-row').show();
    } else {
        $('#next-payment-date-row').hide();
    }
});
```

---

## Что НЕ меняется

- `receivedCredit()` в `main.js` — уже содержит проверку `> 0`, дополнительных изменений не требует
- Бэкенд-экшены `CreditController` — без изменений
- Модель `Credit` — без изменений
- Таблица БД — без изменений

---

## Зависимости

Нет внешних зависимостей. Изменения изолированы в двух файлах фронтенда.

---

## Security

- Нет изменений в серверной логике и валидации
- Скрытие строки — только UX; `receivedCredit()` всё равно проверяет значение перед отправкой
- Пользователь не может обойти проверку через DevTools: серверная валидация суммы не меняется
