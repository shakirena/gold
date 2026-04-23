# Spec #6: Автоматически пересчитывать дату следующего платежа

**Issue:** #6
**Status:** Analysis
**Date:** 2026-04-16
**Author:** analyst-agent

---

## Описание

`Credit.date_constribution` — дата следующего платежа — устанавливается вручную при создании кредита и не обновляется автоматически. `getDateConstributionStatus()` читает это поле из БД, поэтому статус кредита (просрочен / скоро / норма) остаётся неверным после платежей.

**Корневая причина:** `Payment::afterSave()` вызывает `recalculateDebt()`, но не пересчитывает дату следующего платежа.

**Усложняющий фактор:** `getDateConstribution()` использует `date_constribution` как **исходную** точку отсчёта и динамически сдвигает её через все платежи. Если перезаписывать это поле — метод сломается. Решение: добавить `date_constribution_start` для хранения оригинальной даты.

---

## Functional Requirements

| ID | Требование |
|----|-----------|
| FR-1 | При сохранении `Payment` система вызывает `Credit::recalculateNextPaymentDate()` и сохраняет результат в `Credit.date_constribution`. |
| FR-2 | При удалении `Payment` дата пересчитывается от исходной точки с учётом оставшихся платежей. |
| FR-3 | Если сумма всех платежей покрывает N полных `month_payment`, дата сдвигается на N месяцев от `date_constribution_start`. |
| FR-4 | Если сумма платежей не достигает одного `month_payment`, дата равна `date_constribution_start` (без сдвига). |
| FR-5 | `getDateConstributionStatus()` работает корректно без изменений после автообновления `date_constribution`. |
| FR-6 | При создании кредита `date_constribution_start` = `date_constribution` (исходная дата). |

## Non-Functional Requirements

| ID | Требование |
|----|-----------|
| NFR-1 | `recalculateNextPaymentDate()` идемпотентен: результат одинаков при любом порядке вызова. |
| NFR-2 | Метод не мутирует объекты Payment (только читает суммы). |
| NFR-3 | Покрыт unit-тестами: 1 платёж = +1 месяц, 2 месяца в одном платеже, частичный без сдвига, после удаления. |
| NFR-4 | Одна миграция для добавления `date_constribution_start`, заполняется текущим значением `date_constribution`. |

---

## Acceptance Criteria

### AC-1: Платёж сдвигает дату на 1 месяц

**Given** кредит с `date_constribution_start = 2026-05-01`, `month_payment = 300`, и один платёж `sum = 300`
**When** `Payment` сохранён
**Then** `Credit.date_constribution = 2026-06-01`

### AC-2: Удаление платежа откатывает дату

**Given** кредит с `date_constribution = 2026-06-01` после одного платежа
**When** платёж удалён
**Then** `Credit.date_constribution = 2026-05-01` (= `date_constribution_start`)

### AC-3: Платёж на 2 месяца сдвигает дату на 2 месяца

**Given** кредит `month_payment = 300`, `date_constribution_start = 2026-05-01`, платёж `sum = 600`
**When** `Payment` сохранён
**Then** `Credit.date_constribution = 2026-07-01`

### AC-4: Частичный платёж не сдвигает дату

**Given** кредит `month_payment = 300`, платёж `sum = 200`
**When** `Payment` сохранён
**Then** `Credit.date_constribution = date_constribution_start` (без изменений)

### AC-5: Несколько платежей суммируются

**Given** два платежа по 200 (итого 400 > month_payment=300)
**When** оба сохранены
**Then** `Credit.date_constribution` сдвинулся на +1 месяц

### AC-6: Покрыто unit-тестами

**Given** набор сценариев AC-1..AC-5
**When** запускается `php vendor/bin/codecept run unit`
**Then** все тесты зелёные

---

## Out of Scope

- UI изменения (цветовая индикация уже работает через `getDateConstribution()`)
- Автоматическое начисление штрафов по расписанию
- Пересчёт исторических кредитов (миграция только добавляет колонку)
- Уведомления клиентам о смене даты

---

## User Stories

| # | Story | AC |
|---|-------|----|
| #7 | Добавить `date_constribution_start` + метод `recalculateNextPaymentDate()` | AC-1..AC-5 |
| #8 | Unit-тесты расчёта даты следующего платежа | AC-6 |
