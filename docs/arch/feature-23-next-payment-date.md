# Architecture: Feature #23 — Восстановить выбор даты следующей оплаты при ежемесячном платеже

## ADR (Architecture Decision Records)

### ADR-1: Передача даты следующей оплаты через AJAX-параметр

**Context:**
При ежемесячном платеже (`payment-month`) дата следующей оплаты (`date_constribution`) вычисляется автоматически в методе `recalculateNextPaymentDateFromMonth()`. Пользователь не может вручную скорректировать эту дату, что создаёт проблемы при нестандартных графиках платежей.

**Decision:**
Добавить в форму ежемесячного платежа поле `<input type="date" id="next_payment_date">`, предзаполненное ожидаемой следующей датой (текущий `date_constribution` + 1 месяц, вычисляется на стороне PHP при рендеринге вью). Поле передаётся как необязательный параметр `date_constribution` в AJAX-запросе. В `actionPaymentMonth` — если параметр передан, валиден (формат `Y-m-d`, не в прошлом) — использовать его напрямую, иначе выполнять автоматический пересчёт.

**Consequences:**
- Поведение по умолчанию (авторасчёт) сохраняется при пустом или невалидном значении поля.
- Никаких изменений схемы БД не требуется.
- Минимальный риск регрессий: существующая логика `recalculateNextPaymentDateFromMonth()` остаётся без изменений.
- Валидация на стороне сервера защищает от некорректных значений (прошлые даты, неверный формат).

---

### ADR-2: Предзаполнение поля датой из PHP-модели

**Context:**
Пользователю удобно видеть ожидаемую следующую дату как значение по умолчанию, а не вводить её вручную с нуля.

**Decision:**
В вью `views/credit/view_credit.php` вычислять предлагаемую дату как `date_constribution + 1 месяц` через PHP (`date('Y-m-d', strtotime('+1 month', strtotime($model->date_constribution)))`). Это значение устанавливается атрибутом `value` у input-поля.

**Consequences:**
- Логика предзаполнения остаётся в PHP-слое, не в JS.
- Если `date_constribution` у модели пустое — поле остаётся незаполненным, пользователь вводит дату самостоятельно.

---

## ERD (изменения схемы БД)

Изменений схемы БД не требуется. Поле `date_constribution` уже существует в таблице `credits`.

---

## API Contracts

| Method | URL | Controller::Action | Params | Description |
|--------|-----|--------------------|--------|-------------|
| GET | `/credit/payment-month` | `CreditController::actionPaymentMonth` | `sum` (float, required), `note` (string, optional), `id_credit` (int, required), `date_constribution` (string Y-m-d, optional) | Записывает ежемесячный платёж. Если `date_constribution` передан и валиден — устанавливает его как следующую дату оплаты напрямую. Иначе — запускает авторасчёт через `recalculateNextPaymentDateFromMonth()`. Возвращает `id` записи платежа. |

### Валидация параметра `date_constribution`

- Формат: `Y-m-d` (например, `2026-05-15`)
- Дата не должна быть в прошлом относительно `date('Y-m-d')`
- Если формат некорректен или дата в прошлом — параметр игнорируется, используется авторасчёт

---

## Module Structure

### Файлы, требующие изменений

| Файл | Тип изменения | Описание |
|------|---------------|----------|
| `views/credit/view_credit.php` | Изменение | Добавить строку с `<input type="date" id="next_payment_date">` в таблицу формы ежемесячного платежа; предзаполнить значением `date_constribution + 1 месяц` из PHP |
| `web/js/main.js` | Изменение | В функции `receivedCredit(id)` добавить параметр `date_constribution` из поля `#next_payment_date` в AJAX-запрос к `payment-month` |
| `controllers/CreditController.php` | Изменение | В `actionPaymentMonth` добавить необязательный параметр `$date_constribution = null`; добавить валидацию и условную логику применения даты |

### Файлы, изменений не требующие

| Файл | Причина |
|------|---------|
| `models/Credit.php` | Метод `recalculateNextPaymentDateFromMonth()` остаётся без изменений; используется как fallback |
| `models/Month.php` | Модель платежа не меняется |
| Миграции | Схема БД не изменяется |

---

## Code Stubs

### 1. `views/credit/view_credit.php` — добавить строку в форму

```php
<?php
// Предлагаемая следующая дата: текущий date_constribution + 1 месяц
$nextPaymentDefault = '';
if (!empty($model->date_constribution)) {
    $nextPaymentDefault = date('Y-m-d', strtotime('+1 month', strtotime($model->date_constribution)));
}
?>

<!-- Добавить после строки с полем "Gecikməyə görə ödəniş" -->
<tr>
    <td><b>Növbəti ödəniş tarixi: </b></td>
    <td>
        <?= Html::input('date', 'next_payment_date', $nextPaymentDefault, [
            'id'  => 'next_payment_date',
            'min' => date('Y-m-d'),
        ]) ?>
    </td>
</tr>
```

---

### 2. `web/js/main.js` — передать дату в AJAX-запрос

```js
// До изменения:
if ($("#month_payment").val() > 0) {
    $.get('payment-month', {sum:$("#month_payment").val(), note:$("#note").val(), id_credit:id}, function(data) {
        month = data;
    });
}

// После изменения:
if ($("#month_payment").val() > 0) {
    $.get('payment-month', {
        sum: $("#month_payment").val(),
        note: $("#note").val(),
        id_credit: id,
        date_constribution: $("#next_payment_date").val()
    }, function(data) {
        month = data;
    });
}
```

---

### 3. `controllers/CreditController.php` — принять и применить дату

```php
// До изменения:
public function actionPaymentMonth($sum, $note, $id_credit)
{
    $payment = new Month();
    $payment->id_credit = $id_credit;
    $payment->sum       = $sum;
    $payment->note      = $note;
    $payment->date      = date("Y:m:d H:i:s");
    $payment->save();

    $model = Credit::find()->where(["id" => $id_credit])->one();
    $model->recalculateNextPaymentDateFromMonth((float)$sum, 0);
    return $payment->id;
}

// После изменения:
public function actionPaymentMonth($sum, $note, $id_credit, $date_constribution = null)
{
    $payment = new Month();
    $payment->id_credit = $id_credit;
    $payment->sum       = $sum;
    $payment->note      = $note;
    $payment->date      = date("Y:m:d H:i:s");
    $payment->save();

    $model = Credit::find()->where(["id" => $id_credit])->one();

    // Валидация переданной даты
    $useManualDate = false;
    if (!empty($date_constribution)) {
        $parsed = \DateTime::createFromFormat('Y-m-d', $date_constribution);
        $isValidFormat = $parsed && $parsed->format('Y-m-d') === $date_constribution;
        $isNotPast = $date_constribution >= date('Y-m-d');
        if ($isValidFormat && $isNotPast) {
            $useManualDate = true;
        }
    }

    if ($useManualDate) {
        // Установить дату напрямую, без авторасчёта
        $model->date_constribution = $date_constribution;
        $model->save(false, ['date_constribution']);
    } else {
        // Fallback: авторасчёт на основе суммы платежа
        $model->recalculateNextPaymentDateFromMonth((float)$sum, 0);
    }

    return $payment->id;
}
```
