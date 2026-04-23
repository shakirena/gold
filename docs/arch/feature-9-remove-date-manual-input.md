# Architecture: Feature #9 — Убрать ручной ввод date_constribution

**Issue:** #9  
**Date:** 2026-04-16

---

## Решение

Задача не требует изменений в модели или БД. Меняются только:
1. **View** (`_form.php`): заменить DatePicker для `date_constribution` на DatePicker для `date_constribution_start`
2. **Controller** (`CreditController::actionCreate()`): установить `date_constribution = date_constribution_start` до первого `$model->save()`

---

## ADR-1: Переименовать поле в форме, а не просто удалить

**Контекст:** пользователь должен указывать начальную дату платежа при создании кредита.  
**Решение:** заменить `date_constribution` → `date_constribution_start` в форме. Убрать `date_constribution` из формы полностью.  
**Почему:** `date_constribution` — вычисляемое поле. `date_constribution_start` — исходный параметр кредита. Пользователь задаёт параметр, а не результат.

---

## ADR-2: date_constribution убрать из required в rules() — нет

**Контекст:** `date_constribution` сейчас в `required` в `Credit::rules()`. Если убрать DatePicker, поле придёт пустым.  
**Решение:** НЕ трогать `rules()`. Вместо этого в `actionCreate()` установить `$model->date_constribution = $model->date_constribution_start` до первого `save()`, чтобы required validation прошёл.  
**Почему:** Минимальный риск — rules() не меняем.

---

## Modified Files

| Файл | Изменение |
|------|-----------|
| `views/credit/_form.php` | Убрать DatePicker `date_constribution` (строки 143, 147-159); pre-fill `$model->date_constribution_start = date('Y-m-d')` |
| `controllers/CreditController.php` | В `actionCreate()`: добавить `$model->date_constribution = $model->date_constribution_start;` перед первым `$model->save()` |

---

## Код до/после

### _form.php — ДО (строки 141-160)

```php
<div class="col-md-6">
<?php
    $model->percant = 10;
    $model->date_constribution= date('Y-m-d');
?>
    <?= $form->field($model, 'date_constribution')->widget(DatePicker::className(),[
        'name' => 'check_issue_date',
        'id' => 'date',
        'options' => ['placeholder' => 'Select issue date ...'],
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => ['format' => 'yyyy-mm-dd', 'todayHighlight' => false, 'autoclose'=>true]
    ]) ?>
```

### _form.php — ПОСЛЕ

```php
<div class="col-md-6">
<?php
    $model->percant = 10;
    $model->date_constribution_start = date('Y-m-d');
?>
    <?= $form->field($model, 'date_constribution_start')->widget(DatePicker::className(),[
        'name' => 'check_issue_date',
        'id' => 'date',
        'options' => ['placeholder' => 'Select payment date ...'],
        'type' => DatePicker::TYPE_INPUT,
        'pluginOptions' => ['format' => 'yyyy-mm-dd', 'todayHighlight' => false, 'autoclose'=>true]
    ]) ?>
```

### CreditController::actionCreate() — ДО

```php
if ($model->load(Yii::$app->request->post()) && $model->save()) {
    $model->debt = $model->sum - $model->fee;
    $model->id_user = Yii::$app->user->identity->id_user;
    $post = Yii::$app->request->post("Credit");
    $model->date_create = $post['date_create'] . " " . date("H:s:i");
    $model->date_constribution_start = $model->date_constribution;
    if ($model->save()) { ... }
}
```

### CreditController::actionCreate() — ПОСЛЕ

```php
if ($model->load(Yii::$app->request->post())) {
    $model->date_constribution = $model->date_constribution_start; // set before validation
    if ($model->save()) {
        $model->debt = $model->sum - $model->fee;
        $model->id_user = Yii::$app->user->identity->id_user;
        $post = Yii::$app->request->post("Credit");
        $model->date_create = $post['date_create'] . " " . date("H:s:i");
        // date_constribution_start уже установлен из формы
        if ($model->save()) { ... }
    }
}
```

---

## Dependencies

- Requires: #7 (`recalculateNextPaymentDate()` уже реализован)
- No DB migrations needed
- No model changes needed
