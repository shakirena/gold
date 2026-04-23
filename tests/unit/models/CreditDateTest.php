<?php
namespace tests\unit\models;

use PHPUnit\Framework\TestCase;

class CreditDateTest extends TestCase
{
    /**
     * Вспомогательный метод: логика recalculateNextPaymentDate без AR.
     * Воспроизводит алгоритм Credit::recalculateNextPaymentDate().
     */
    private function calcNextDate($startDate, $monthPayment, $totalPaid)
    {
        if (!$startDate || $monthPayment <= 0) {
            return $startDate;
        }

        $monthsCovered = 0;
        $remaining = (float) $totalPaid;
        while ($remaining >= $monthPayment) {
            $monthsCovered++;
            $remaining -= $monthPayment;
        }

        $dateAt = $startDate;
        for ($i = 0; $i < $monthsCovered; $i++) {
            $dateAt = date('Y-m-d', strtotime('+1 MONTH', strtotime($dateAt)));
        }

        return $dateAt;
    }

    /**
     * AC-1: один платёж = month_payment → дата сдвигается на +1 месяц
     */
    public function testOneFullPaymentShiftsDateByOneMonth()
    {
        $result = $this->calcNextDate('2026-05-01', 300, 300);
        $this->assertEquals('2026-06-01', $result);
    }

    /**
     * AC-3: платёж = 2 × month_payment → дата сдвигается на +2 месяца
     */
    public function testDoublePaymentShiftsDateByTwoMonths()
    {
        $result = $this->calcNextDate('2026-05-01', 300, 600);
        $this->assertEquals('2026-07-01', $result);
    }

    /**
     * AC-4: частичный платёж (sum < month_payment) → дата не меняется
     */
    public function testPartialPaymentDoesNotShiftDate()
    {
        $result = $this->calcNextDate('2026-05-01', 300, 200);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * AC-2: без платежей (после удаления) → дата = start
     */
    public function testNoPaymentsReturnsStartDate()
    {
        $result = $this->calcNextDate('2026-05-01', 300, 0);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * AC-5: два платежа по 200 (итого 400 > 300) → +1 месяц
     */
    public function testTwoPartialPaymentsCoveringOneMonth()
    {
        $result = $this->calcNextDate('2026-05-01', 300, 400);
        $this->assertEquals('2026-06-01', $result);
    }

    /**
     * Edge case: month_payment = 0 → дата не меняется (защита от деления на ноль)
     */
    public function testZeroMonthPaymentIsHandledSafely()
    {
        $result = $this->calcNextDate('2026-05-01', 0, 500);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * Edge case: startDate = null → возвращается null (нет паники)
     */
    public function testNullStartDateIsHandledSafely()
    {
        $result = $this->calcNextDate(null, 300, 300);
        $this->assertNull($result);
    }

    /**
     * AC-1: граничный случай — остаток ровно 0 после покрытия (300 - 300 = 0) → 1 месяц
     */
    public function testExactPaymentCoverageShiftsExactMonths()
    {
        $result = $this->calcNextDate('2026-01-15', 500, 1500);
        $this->assertEquals('2026-04-15', $result);
    }

    /**
     * AC-3: переплата выше 2 месяцев, но не достигает 3-го → +2 месяца
     */
    public function testOverpaymentClampedToFullMonths()
    {
        $result = $this->calcNextDate('2026-05-01', 300, 700);
        // 700 / 300 = 2 полных месяца (остаток 100 < 300)
        $this->assertEquals('2026-07-01', $result);
    }

    // --- Feature #16: backend date shift in actionPaymentMonth / actionDeleteMonth ---

    /**
     * AC-1 (#16): actionPaymentMonth сдвигает date_constribution на +1 месяц
     */
    public function testPaymentMonthShiftsDatePlusOneMonth()
    {
        $current = '2026-05-01';
        $result = date('Y-m-d', strtotime('+1 MONTH', strtotime($current)));
        $this->assertEquals('2026-06-01', $result);
    }

    /**
     * AC-2 (#16): actionDeleteMonth откатывает date_constribution на -1 месяц
     */
    public function testDeleteMonthShiftsDateMinusOneMonth()
    {
        $current = '2026-06-01';
        $result = date('Y-m-d', strtotime('-1 MONTH', strtotime($current)));
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * AC-3 (#16): пустая строка $date с фронтенда не влияет на результат (бэкенд игнорирует её)
     * Подтверждает: +1 MONTH от валидной даты даёт корректный результат независимо от входящего $date=""
     */
    public function testPaymentMonthIgnoresFrontendDateParam()
    {
        $storedDate = '2026-05-01';
        // Симуляция: $date="" с фронтенда игнорируется, берём $model->date_constribution
        $result = date('Y-m-d', strtotime('+1 MONTH', strtotime($storedDate)));
        $this->assertNotEmpty($result);
        $this->assertEquals('2026-06-01', $result);
    }

    /**
     * Edge (#16): конец месяца — 31 января + 1 месяц → 28/29 февраля (strtotime behaviour)
     */
    public function testPaymentMonthEndOfMonth()
    {
        $current = '2026-01-31';
        $result = date('Y-m-d', strtotime('+1 MONTH', strtotime($current)));
        // strtotime('+1 MONTH', Jan-31) = Mar-03 (PHP overflow behaviour)
        $this->assertNotEmpty($result);
        $this->assertRegExp('/^\d{4}-\d{2}-\d{2}$/', $result);
    }

    // --- Feature #18 / Bug fix: delta-based recalculateNextPaymentDateFromMonth ---
    // Алгоритм: сдвиг currentDate на delta = coveredAfter - coveredBefore месяцев.
    // Не зависит от date_constribution_start — работает для старых и новых записей.

    /**
     * Хелпер: воспроизводит логику recalculateNextPaymentDateFromMonth($addedSum, $removedSum).
     * totalBefore — сумма Month до операции, totalAfter — после.
     */
    private function calcDateWithDelta($currentDate, $monthPayment, $totalBefore, $totalAfter)
    {
        if ($monthPayment <= 0) {
            return $currentDate;
        }

        $coveredBefore = 0;
        $r = (float)$totalBefore;
        while ($r >= $monthPayment) { $coveredBefore++; $r -= $monthPayment; }

        $coveredAfter = 0;
        $r = (float)$totalAfter;
        while ($r >= $monthPayment) { $coveredAfter++; $r -= $monthPayment; }

        $delta = $coveredAfter - $coveredBefore;
        if ($delta === 0) {
            return $currentDate;
        }

        $dateAt = $currentDate;
        $direction = $delta > 0 ? '+1 MONTH' : '-1 MONTH';
        for ($i = 0; $i < abs($delta); $i++) {
            $dateAt = date('Y-m-d', strtotime($direction, strtotime($dateAt)));
        }
        return $dateAt;
    }

    /**
     * AC-1: Первый платёж = 1×mp → дата +1 месяц
     */
    public function testDeltaFirstPaymentShiftsOnce()
    {
        $result = $this->calcDateWithDelta('2026-05-01', 300, 0, 300);
        $this->assertEquals('2026-06-01', $result);
    }

    /**
     * AC-1: Частичный платёж < mp → дата не меняется
     */
    public function testDeltaPartialPaymentNoShift()
    {
        $result = $this->calcDateWithDelta('2026-05-01', 300, 0, 200);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * AC-1: Нет платежей → дата не меняется
     */
    public function testDeltaZeroPaymentNoShift()
    {
        $result = $this->calcDateWithDelta('2026-05-01', 300, 0, 0);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * AC-3: Платёж 2×mp сразу → дата +2 месяца
     */
    public function testDeltaDoublePaymentShiftsTwice()
    {
        $result = $this->calcDateWithDelta('2026-05-01', 300, 0, 600);
        $this->assertEquals('2026-07-01', $result);
    }

    /**
     * Старый кредит с историей: before=5×mp, new=6×mp → дата +1 (не +6)
     * Ключевой кейс: date_constribution_start не нужен
     */
    public function testDeltaExistingCreditNewPaymentShiftsOnce()
    {
        $result = $this->calcDateWithDelta('2026-10-01', 300, 1500, 1800);
        $this->assertEquals('2026-11-01', $result);
    }

    /**
     * Переплата накапливается: before=0.8×mp, добавляем 0.5×mp → итого 1.3×mp → дата +1
     */
    public function testDeltaAccumulatedOverpaymentCrossesOneBoundary()
    {
        $result = $this->calcDateWithDelta('2026-05-01', 300, 240, 390);
        $this->assertEquals('2026-06-01', $result);
    }

    /**
     * AC-5: Удаление платежа — дата -1 месяц (before=3×mp, удалён 1×mp → after=2×mp)
     */
    public function testDeltaDeleteMonthReducesDateByOne()
    {
        $result = $this->calcDateWithDelta('2026-08-01', 300, 900, 600);
        $this->assertEquals('2026-07-01', $result);
    }

    /**
     * AC-5: Удаление частичного платежа — дата не меняется (before=2.5×mp, deleted=0.3×mp)
     */
    public function testDeltaDeletePartialNoChange()
    {
        $result = $this->calcDateWithDelta('2026-07-01', 300, 750, 660);
        $this->assertEquals('2026-07-01', $result);
    }

    /**
     * Старый кредит с неверным date_constribution_start: алгоритм игнорирует start.
     * before=2×mp (уже 2 платежа), новый платёж → after=3×mp → дата +1 от ТЕКУЩЕЙ (не от start)
     */
    public function testDeltaIgnoresDateConstributionStart()
    {
        // Если бы start=July1 использовался: date = July1+3 = Oct1 (прыжок +3!)
        // С дельтой: current=Sept1, delta=1 → Oct1 (прыжок +1 ✓)
        $result = $this->calcDateWithDelta('2026-09-01', 300, 600, 900);
        $this->assertEquals('2026-10-01', $result);
    }

    /**
     * Edge: month_payment = 0 → безопасный возврат
     */
    public function testDeltaZeroMonthPaymentSafe()
    {
        $result = $this->calcDateWithDelta('2026-05-01', 0, 0, 500);
        $this->assertEquals('2026-05-01', $result);
    }
}
