# Feature #21: Подтверждение удаления Month-платежа без DatePicker

## Описание

Модальное окно `show_date.php` с DatePicker устарело (дата рассчитывается на бэкенде с #16).
Нужно заменить на нативный `confirm()` диалог.

## Functional Requirements

- FR-1: Кнопка «Silmek» вызывает `confirm('Silmek isteyirsiniz?')` без модального окна
- FR-2: Подтверждение → переход на `delete-month?id=...`
- FR-3: Отмена → никаких действий
- FR-4: `show_date.php` и `actionShowDate` удалены

## Acceptance Criteria

### AC-1: confirm-диалог вместо модала
**Given** страница view_credit с Month-платежами
**When** пользователь нажимает «Silmek» на строке
**Then** появляется `confirm('Silmek isteyirsiniz?')`

### AC-2: Подтверждение → удаление
**Given** confirm-диалог открыт
**When** пользователь нажимает OK
**Then** браузер переходит на `delete-month?id=...`

### AC-3: Отмена → без действий
**Given** confirm-диалог открыт
**When** пользователь нажимает Отмена
**Then** ничего не происходит, страница не меняется

### AC-4: Устаревший код удалён
**Given** изменения применены
**When** код проверяется
**Then** `show_date.php` не существует, `actionShowDate` удалён

### AC-5: Регрессия
**Given** изменения в JS и PHP
**When** `php vendor/bin/codecept run unit`
**Then** OK (≥ 54 tests)

## Out of Scope

- Изменение `actionDeleteMonth` (бэкенд без изменений)
- Bootstrap-modal для подтверждения (нативный confirm достаточен)
