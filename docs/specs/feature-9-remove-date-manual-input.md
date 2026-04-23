# Spec: Feature #9 — Убрать ручной ввод date_constribution

**Issue:** #9  
**Type:** tech-debt  
**Priority:** medium  
**Date:** 2026-04-16

---

## Контекст

После реализации #7 (`recalculateNextPaymentDate()`) дата следующего платежа `date_constribution` пересчитывается автоматически при каждом сохранении/удалении платежа.

В форме создания кредита (`views/credit/_form.php`) по-прежнему присутствует DatePicker, позволяющий менеджеру вручную задать `date_constribution`. Это создаёт риск рассинхронизации: выставленная вручную дата будет перезаписана при первом же платеже.

---

## Functional Requirements

| FR | Описание |
|----|----------|
| FR-1 | DatePicker для `date_constribution` убран из `_form.php` |
| FR-2 | Вместо него — DatePicker для `date_constribution_start` (исходная дата платежа, задаётся один раз) |
| FR-3 | В `CreditController::actionCreate()` устанавливается `date_constribution = date_constribution_start` до первого `save()` |
| FR-4 | Отображение `date_constribution` read-only в index/payment/report/view_credit не меняется |
| FR-5 | Все unit-тесты проходят после изменений |

---

## Non-Functional Requirements

| NFR | Описание |
|-----|----------|
| NFR-1 | Изменения только в view и controller — модель и БД не меняются |
| NFR-2 | Нет регрессий в существующих 36 unit-тестах |

---

## User Stories

### Story #10: Заменить DatePicker date_constribution на date_constribution_start в форме кредита

**Given** менеджер открывает форму создания кредита  
**When** заполняет форму и сохраняет  
**Then** поле `date_constribution_start` сохранено из ввода, `date_constribution` = `date_constribution_start`, DatePicker для `date_constribution` в форме отсутствует

---

## Вне scope

- Форма редактирования (`actionUpdate`) — `_form.php` используется и там; убираем DatePicker из формы целиком (при редактировании дата тоже не должна меняться вручную)
- Исторические кредиты — данные в БД не пересчитываются
- `getDateConstribution()` — упрощение вынесено в отдельную задачу (ADR-4 из #6)

---

## Acceptance Criteria (Given/When/Then)

### AC-1: DatePicker отсутствует в форме

**Given** менеджер открывает `/credit/create`  
**When** смотрит на форму  
**Then** поля DatePicker для `date_constribution` нет; есть DatePicker для `date_constribution_start`

### AC-2: date_constribution устанавливается автоматически

**Given** менеджер заполнил форму, указал `date_constribution_start = '2026-06-01'`  
**When** форма отправлена и сохранена  
**Then** `credit.date_constribution = '2026-06-01'` (равно start)

### AC-3: Read-only отображение не изменилось

**Given** кредит существует  
**When** открыть index / payment / report / view_credit  
**Then** `date_constribution` отображается как прежде

### AC-4: Тесты зелёные

**Given** изменения внесены  
**When** `php vendor/bin/codecept run unit`  
**Then** 36 тестов, 59 assertions — OK
