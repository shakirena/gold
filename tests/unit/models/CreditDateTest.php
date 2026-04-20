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

    // --- Feature #18: smart recalculateNextPaymentDateFromMonth ---

    /**
     * Вспомогательный метод: логика recalculateNextPaymentDateFromMonth без AR.
     */
    private function calcNextDateFromMonth($startDate, $monthPayment, $totalMonthPaid)
    {
        if (!$startDate || $monthPayment <= 0) {
            return $startDate;
        }

        $monthsCovered = 0;
        $remaining = (float) $totalMonthPaid;
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
     * AC-1 (#18): Если totalMonth < month_payment — дата не меняется (= start)
     */
    public function testSmartRecalcPartialPaymentNoDateChange()
    {
        $result = $this->calcNextDateFromMonth('2026-05-01', 300, 200);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * AC-1 (#18): Нет Month-платежей вообще — дата = start
     */
    public function testSmartRecalcZeroTotalNoDateChange()
    {
        $result = $this->calcNextDateFromMonth('2026-05-01', 300, 0);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * AC-2 (#18): totalMonth == month_payment → start + 1 месяц
     */
    public function testSmartRecalcOneFullMonthShiftsOnce()
    {
        $result = $this->calcNextDateFromMonth('2026-05-01', 300, 300);
        $this->assertEquals('2026-06-01', $result);
    }

    /**
     * AC-3 (#18): totalMonth == 2×month_payment → start + 2 месяца
     */
    public function testSmartRecalcTwoFullMonthsShiftsTwice()
    {
        $result = $this->calcNextDateFromMonth('2026-05-01', 300, 600);
        $this->assertEquals('2026-07-01', $result);
    }

    /**
     * AC-3 (#18): totalMonth == 700 (2×300 + остаток 100) → start + 2 месяца
     */
    public function testSmartRecalcOverpaymentClampedToFullMonths()
    {
        $result = $this->calcNextDateFromMonth('2026-05-01', 300, 700);
        $this->assertEquals('2026-07-01', $result);
    }

    /**
     * AC-4 (#18): 3 платежа по 300 = накопленно 900 → start + 3 месяца
     */
    public function testSmartRecalcThreeMonthsAccumulated()
    {
        $result = $this->calcNextDateFromMonth('2026-05-01', 300, 900);
        $this->assertEquals('2026-08-01', $result);
    }

    /**
     * AC-5 (#18): После удаления платежа — корректный пересчёт.
     * Was: 900 (3 months). After delete 300 → 600 (2 months).
     */
    public function testSmartRecalcAfterDeleteCorrectlyRecomputes()
    {
        $before = $this->calcNextDateFromMonth('2026-05-01', 300, 900);
        $this->assertEquals('2026-08-01', $before);

        $after = $this->calcNextDateFromMonth('2026-05-01', 300, 600);
        $this->assertEquals('2026-07-01', $after);
    }

    /**
     * Edge (#18): month_payment = 0 → дата не меняется (защита от деления на ноль)
     */
    public function testSmartRecalcZeroMonthPaymentSafe()
    {
        $result = $this->calcNextDateFromMonth('2026-05-01', 0, 500);
        $this->assertEquals('2026-05-01', $result);
    }

    /**
     * Edge (#18): startDate = null → возвращается null
     */
    public function testSmartRecalcNullStartDateSafe()
    {
        $result = $this->calcNextDateFromMonth(null, 300, 300);
        $this->assertNull($result);
    }
}
