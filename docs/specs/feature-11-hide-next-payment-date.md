# Feature #11: Убрать инпут даты следующей оплаты при ежемесячном платеже

## Описание

В форме `views/credit/view_credit.php` в строке "Ayliq faiz məbləği ödənişi" (ежемесячный платёж) отображается DatePicker "Növbəti ödəniş" (Следующая оплата). Поле уже `disabled` — пользователь не может его редактировать. Дата пересчитывается автоматически в `Payment::afterSave()`. Нужно убрать этот лишний элемент из UI.

---

## Functional Requirements

- FR-1: DatePicker "Növbəti ödəniş" удалён из строки ежемесячного платежа в `view_credit.php`
- FR-2: Дата следующей оплаты продолжает автоматически пересчитываться при сохранении/удалении платежа (уже реализовано в `Payment::afterSave`)

## Non-Functional Requirements

- NFR-1: Существующие unit-тесты (CreditDebtTest, FineValidationTest) продолжают проходить
- NFR-2: Страница `view-credit` открывается без ошибок PHP и JS
- NFR-3: Форма платежа отправляется корректно после изменения

---

## Acceptance Criteria

### AC-1: Инпут скрыт из формы

**Given** менеджер открывает страницу `view-credit?id={N}` для любого кредита  
**When** страница загружается  
**Then** DatePicker "Növbəti ödəniş" не отображается в строке ежемесячного платежа

### AC-2: Дата пересчитывается автоматически

**Given** кредит имеет `month_payment > 0`  
**When** менеджер вводит ежемесячный платёж и нажимает "ok"  
**Then** `credit.date_constribution` автоматически сдвигается на +1 месяц без участия пользователя

### AC-3: Логика сохранения не нарушена

**Given** существующие платежи в БД  
**When** запускаются unit-тесты `php vendor/bin/codecept run unit`  
**Then** все тесты проходят (0 failures)

### AC-4: Нет визуальных артефактов

**Given** менеджер открывает `view-credit`  
**When** смотрит на таблицу платежей  
**Then** строка ежемесячного платежа содержит только поле ввода суммы без пустых блоков или сломанной вёрстки

---

## Out of Scope

- Изменение логики расчёта `date_constribution` в модели `Credit`
- Добавление JS-логики динамического скрытия
- Изменения в других формах (payment.php, _form.php)
- Изменение схемы БД

---

## Technical Notes

- Файл: `views/credit/view_credit.php`, строки 169–183 — удалить `<td>` с DatePicker
- `Payment::afterSave()` и `Payment::afterDelete()` уже вызывают `credit->recalculateNextPaymentDate()` — AC-2 выполнен
- Стек: Yii2 Basic, PHP 7.1, Bootstrap 3, Kartik DatePicker
- Затронутые тесты: нет прямых тестов для view, unit-тесты модели не изменяются
