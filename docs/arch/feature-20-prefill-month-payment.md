# Architecture: Bug #20 — Предзаполнение month_payment

## ADR-1: Изменить value инпута с 0 на $model->month_payment

**Context:** Поле `#month_payment` имеет value=0, пользователь вводит сумму вручную и ошибается.

**Decision:** Изменить одну строку в view_credit.php — передать `$model->month_payment` как значение по умолчанию.

**Consequences:** Пользователь видит правильную сумму по умолчанию. Может изменить вручную для частичной/двойной оплаты.

---

## ERD

Без изменений.

---

## Diff-план

**Файл:** `views/credit/view_credit.php`, строка 166

```php
// БЫЛО:
<?=Html::input("text",'month_payment',0,[ 'size' => '10','id'=>'month_payment'])?>
// СТАТЬ:
<?=Html::input("text",'month_payment',$model->month_payment,[ 'size' => '10','id'=>'month_payment'])?>
```

---

## Security

- Нет изменений в серверной логике
- Значение по умолчанию читается из модели (не из user input)
