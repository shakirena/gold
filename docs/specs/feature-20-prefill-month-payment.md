# Bug #20: Предзаполнение поля month_payment

## Описание

Поле `#month_payment` в `view_credit.php` имеет `value=0`. Пользователь вводит сумму вручную, что приводит к ошибочному сдвигу даты на 2+ месяца, если введённая сумма ≠ `month_payment`.

## Functional Requirements

- FR-1: Поле `#month_payment` предзаполняется значением `$model->month_payment` при загрузке страницы
- FR-2: Алгоритм `recalculateNextPaymentDateFromMonth()` остаётся без изменений — сдвиг зависит от введённой суммы
- FR-3: Пользователь может изменить предзаполненное значение (инпут редактируемый)

## Acceptance Criteria

### AC-1: Предзаполнение инпута
**Given** страница view_credit открыта
**When** пользователь видит форму оплаты
**Then** поле `#month_payment` содержит `$model->month_payment`

### AC-2: Оплата 1×month_payment → дата +1 месяц
**Given** предзаполненное значение = month_payment, пользователь не меняет
**When** нажимает OK
**Then** date_constribution сдвигается на +1 месяц

### AC-3: Оплата 2×month_payment → дата +2 месяца (корректно)
**Given** пользователь вручную изменяет инпут на 2×month_payment
**When** нажимает OK
**Then** date_constribution сдвигается на +2 месяца

### AC-4: Регрессия
**Given** изменение в view_credit.php
**When** `php vendor/bin/codecept run unit`
**Then** OK (≥ 53 tests)

## Out of Scope

- Изменение алгоритма recalculateNextPaymentDateFromMonth()
- Валидация суммы на сервере
