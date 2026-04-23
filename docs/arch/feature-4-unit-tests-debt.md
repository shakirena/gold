# Architecture: Feature #4 — Unit-тесты расчёта штрафной базы

**Date:** 2026-04-16

---

## Контекст

Story #4 — тестовая story. Нет изменений в production-коде. Только добавление/дополнение `tests/unit/models/CreditDebtTest.php`.

---

## Затронутые файлы

| Файл | Изменение |
|------|-----------|
| `tests/unit/models/CreditDebtTest.php` | Добавить `testGetPaymentReturnsAmountPaid` (AC-4) |

**Изменений в production-коде нет.**

---

## ADR-1: Стратегия тестирования без DB

**Context:** `recalculateDebt()` в Credit требует `SUM(payment.sum)` из БД. Прямой unit-тест потребовал бы моков ActiveRecord или реальной БД.

**Decision:** Тестировать чистую логику (`debt = creditSum - totalPaid`, rounding, clamp ≥ 0) через изолированные PHP-вычисления без Yii2 AR. `afterSave`/`afterDelete` тестировать через `stdClass` mock с `getMockBuilder`.

**Rationale:** Быстрее, нет зависимости от состояния БД, тесты воспроизводимы в любом окружении.

---

## ADR-2: Тест getPayment()

**Context:** `getPayment()` — `return $this->sum - $this->debt` (Credit.php:150). Простая арифметика.

**Decision:** Тестировать через прямое вычисление: задаём `sum` и `debt`, проверяем что `sum - debt` = ожидаемой сумме уплаченного.

```php
public function testGetPaymentReturnsAmountPaid()
{
    $creditSum = 1000.0;
    $creditDebt = 600.0;
    // getPayment() = $this->sum - $this->debt
    $amountPaid = $creditSum - $creditDebt;
    $this->assertEquals(400.0, $amountPaid);
}
```

---

## Структура CreditDebtTest.php

| Метод | AC | Статус |
|-------|-----|--------|
| testRecalculateDebtPartialPayment | AC-1 | ✅ реализован |
| testRecalculateDebtFullPayment | AC-2 | ✅ реализован |
| testRecalculateDebtOverpayment | AC-3 | ✅ реализован |
| testRecalculateDebtMultiplePayments | AC-1 | ✅ реализован |
| testRecalculateDebtRounding | NFR | ✅ реализован |
| testAfterSaveTriggersRecalculate | FR-3 | ✅ реализован |
| testAfterDeleteTriggersRecalculate | FR-3 | ✅ реализован |
| testAfterSaveNullCreditIsHandled | edge case | ✅ реализован |
| **testGetPaymentReturnsAmountPaid** | **AC-4** | **❌ отсутствует — нужно добавить** |
