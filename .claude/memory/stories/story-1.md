# Story #1: Исправить расчёт штрафов при частичном погашении

## Статус
**Phase:** Analysis complete  
**Date:** 2026-04-15

---

## Задача (analyst)

**Цель:** Обеспечить автоматический пересчёт остатка долга при платежах и использование актуального `Credit.debt` как базы штрафа  
**Role:** менеджер / кассир  
**Value:** корректное начисление штрафов клиентам без ручного ввода суммы базы

---

## User Stories Created

- #2: Автоматически уменьшать Credit.debt при сохранении платежа
- #3: Использовать Credit.debt как базу при создании штрафа
- #4: Unit-тесты расчёта штрафной базы при частичных платежах

---

## Key ACs

1. `Credit.debt` уменьшается на `Payment.sum` при каждом сохранении `Payment` (атомарно, в транзакции)
2. `Credit.debt` не может стать отрицательным — минимум `0`
3. Форма создания `Fine` предзаполняет базу значением `Credit.debt`, а не `Credit.sum`
4. Сохранение `Fine` блокируется при `Credit.debt = 0`
5. Покрыто unit-тестами: частичный платёж, полное погашение, переплата

---

## Affected Modules

- `models/Payment.php` — добавить `afterSave()` с обновлением `Credit.debt`
- `models/Credit.php` — без изменений логики; `getPayment()` корректен
- `models/Fine.php` — возможный scenario для валидации базы
- `controllers/PaymentController.php` — проверить транзакционность
- `controllers/FineController.php` — передавать `$credit->debt` в view при `actionCreate()`
- `views/fine/create.php` — предзаполнение поля суммы
- `tests/unit/models/CreditDebtTest.php` — новый файл тестов

---

## Dependencies

- #3 зависит от #2 (нужен корректный `Credit.debt` перед предзаполнением штрафа)
- #4 зависит от #2 и #3 (тесты проверяют уже реализованную логику)
- Нет внешних зависимостей (сторонних сервисов, других features)

---

## Spec Document

`docs/specs/feature-1-fine-partial-payment.md`

---

## 🏗️ Архитектура (architect)

**DB Changes:** нет (схема не меняется, все необходимые поля уже существуют)

**Modified Files:**
- `models/Payment.php` — добавить `afterSave()` и `afterDelete()` для автоматического обновления `Credit.debt` после каждого платежа
- `models/Credit.php` — добавить метод `recalculateDebt()` для пересчёта остатка: `debt = sum - SUM(payments.sum)`
- `models/Fine.php` — добавить кастомный валидатор `validateFineSum()` и правило в `rules()` для проверки что сумма штрафа не превышает остаток долга

**Key Decisions:**
- ADR-1: Обновлять `Credit.debt` в `Payment::afterSave()`/`afterDelete()` через `Credit::recalculateDebt()`. Модельный подход (не контроллерный) гарантирует консистентность при любом способе создания платежа. Пересчёт через `SUM(payments.sum)` вместо инкрементального `debt -= payment.sum` для устойчивости к ошибкам данных.
- ADR-2: Валидация суммы штрафа через inline-валидатор `Fine::validateFineSum()`. Проверка `sum <= Credit.debt` и `sum > 0`. Стандартный паттерн Yii2.
- ADR-3: Пересчёт `date_constribution` отложен на Phase 2 (требует уточнения бизнес-логики).

**Security Notes:**
- Decimal precision: использовать `round($value, 2)` при финансовых расчётах, в будущем перейти на `bcmath`
- Нет raw SQL — только ActiveRecord (`Payment::find()->sum('sum')`)
- Race conditions при одновременных платежах — для production нужен `SELECT ... FOR UPDATE`
- Проверка `debt >= 0` после пересчёта для защиты от переплат

**Arch Doc:** `docs/arch/feature-1-fine-partial-payment.md`
**Code Stubs:** `docs/arch/stubs/Payment_afterSave.php`, `docs/arch/stubs/Fine_validation.php`
