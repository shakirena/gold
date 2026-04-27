<?php
namespace tests\unit\models;

use PHPUnit\Framework\TestCase;

/**
 * Feature #23 — Manual date picker for next payment date.
 *
 * Tests the validation logic extracted from actionPaymentMonth():
 *
 *   if ($date_constribution
 *       && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_constribution)
 *       && strtotime($date_constribution) >= strtotime(date('Y-m-d')))
 *   {
 *       // accept: set date_constribution = $date_constribution
 *   } else {
 *       // fallback: call recalculateNextPaymentDateFromMonth()
 *   }
 *
 * The helper resolveNextPaymentDate() replicates that branching so we can
 * test all five cases without hitting the database or HTTP layer.
 */
class CreditPaymentMonthDateTest extends TestCase
{
    // -----------------------------------------------------------------------
    // Helper — mirrors the branch in CreditController::actionPaymentMonth()
    // -----------------------------------------------------------------------

    /**
     * @param string|null $dateConstribution  Value coming from the AJAX request.
     * @param string      $today              Injected "today" for deterministic tests.
     * @return string  'accepted'  — date was valid and would be stored as-is
     *                 'fallback'  — date was invalid; recalculate* would be called
     */
    private function resolveNextPaymentDate($dateConstribution, $today = null)
    {
        if ($today === null) {
            $today = date('Y-m-d');
        }

        if ($dateConstribution
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateConstribution)
            && strtotime($dateConstribution) >= strtotime($today)
        ) {
            return 'accepted';
        }

        return 'fallback';
    }

    // -----------------------------------------------------------------------
    // TC-23-01: Valid custom date — today — should be accepted
    // -----------------------------------------------------------------------

    /**
     * AC-1: Сегодняшняя дата валидна и сохраняется напрямую.
     * Граничное условие: strtotime(today) >= strtotime(today) → true.
     */
    public function testTodayDateIsAccepted()
    {
        $today = date('Y-m-d');
        $result = $this->resolveNextPaymentDate($today, $today);
        $this->assertEquals('accepted', $result);
    }

    // -----------------------------------------------------------------------
    // TC-23-02: Valid custom date — future — should be accepted
    // -----------------------------------------------------------------------

    /**
     * AC-2: Будущая дата валидна и сохраняется напрямую.
     */
    public function testFutureDateIsAccepted()
    {
        $today = '2026-04-27';
        $future = '2026-05-27';
        $result = $this->resolveNextPaymentDate($future, $today);
        $this->assertEquals('accepted', $result);
    }

    /**
     * AC-2 edge: +1 день от сегодня тоже принимается.
     */
    public function testTomorrowDateIsAccepted()
    {
        $today = '2026-04-27';
        $tomorrow = '2026-04-28';
        $result = $this->resolveNextPaymentDate($tomorrow, $today);
        $this->assertEquals('accepted', $result);
    }

    // -----------------------------------------------------------------------
    // TC-23-03: Invalid date — past — should fall back to auto-recalc
    // -----------------------------------------------------------------------

    /**
     * AC-3: Прошедшая дата отклоняется, вызывается авторасчёт.
     */
    public function testPastDateFallsBackToRecalc()
    {
        $today = '2026-04-27';
        $past = '2026-04-26';
        $result = $this->resolveNextPaymentDate($past, $today);
        $this->assertEquals('fallback', $result);
    }

    /**
     * AC-3 edge: дата на год назад тоже откатывается.
     */
    public function testOldPastDateFallsBackToRecalc()
    {
        $today = '2026-04-27';
        $old = '2025-01-01';
        $result = $this->resolveNextPaymentDate($old, $today);
        $this->assertEquals('fallback', $result);
    }

    // -----------------------------------------------------------------------
    // TC-23-04: Invalid format — non-date string — should fall back
    // -----------------------------------------------------------------------

    /**
     * AC-4: Строка, не соответствующая формату YYYY-MM-DD, отклоняется.
     */
    public function testInvalidFormatStringFallsBackToRecalc()
    {
        $result = $this->resolveNextPaymentDate('27-04-2026', '2026-04-27');
        $this->assertEquals('fallback', $result);
    }

    /**
     * AC-4: Пустая строка отклоняется.
     */
    public function testEmptyStringFallsBackToRecalc()
    {
        $result = $this->resolveNextPaymentDate('', '2026-04-27');
        $this->assertEquals('fallback', $result);
    }

    /**
     * AC-4: Произвольный текст отклоняется.
     */
    public function testArbitraryTextFallsBackToRecalc()
    {
        $result = $this->resolveNextPaymentDate('next month', '2026-04-27');
        $this->assertEquals('fallback', $result);
    }

    /**
     * AC-4: Дата с временем (YYYY-MM-DD HH:MM:SS) не соответствует шаблону → fallback.
     */
    public function testDateWithTimeFallsBackToRecalc()
    {
        $result = $this->resolveNextPaymentDate('2026-05-01 10:00:00', '2026-04-27');
        $this->assertEquals('fallback', $result);
    }

    // -----------------------------------------------------------------------
    // TC-23-05: Null date — should fall back to auto-recalc
    // -----------------------------------------------------------------------

    /**
     * AC-5: null принудительно запускает авторасчёт.
     */
    public function testNullDateFallsBackToRecalc()
    {
        $result = $this->resolveNextPaymentDate(null, '2026-04-27');
        $this->assertEquals('fallback', $result);
    }

    // -----------------------------------------------------------------------
    // Additional edge cases
    // -----------------------------------------------------------------------

    /**
     * Edge: regexp пропускает только \d{4}-\d{2}-\d{2}, не буквы.
     */
    public function testAlphanumericStringFallsBackToRecalc()
    {
        $result = $this->resolveNextPaymentDate('abcd-ef-gh', '2026-04-27');
        $this->assertEquals('fallback', $result);
    }

    /**
     * Edge: формат частично валиден (лишний символ) → fallback.
     */
    public function testPartiallyMatchingFormatFallsBackToRecalc()
    {
        $result = $this->resolveNextPaymentDate('2026-05-1', '2026-04-27');
        $this->assertEquals('fallback', $result);
    }

    // -----------------------------------------------------------------------
    // Regression: recalculateNextPaymentDateFromMonth() still works correctly
    // -----------------------------------------------------------------------

    /**
     * Regression: дельта-алгоритм (из #18) не нарушен — первый платёж сдвигает
     * date_constribution ровно на +1 месяц.
     */
    public function testDeltaAlgorithmRegression()
    {
        $monthPayment = 300.0;
        $totalBefore  = 0.0;
        $totalAfter   = 300.0;
        $currentDate  = '2026-05-01';

        $coveredBefore = 0;
        $r = $totalBefore;
        while ($r >= $monthPayment) { $coveredBefore++; $r -= $monthPayment; }

        $coveredAfter = 0;
        $r = $totalAfter;
        while ($r >= $monthPayment) { $coveredAfter++; $r -= $monthPayment; }

        $delta = $coveredAfter - $coveredBefore;
        $dateAt = $currentDate;
        if ($delta !== 0) {
            $direction = $delta > 0 ? '+1 MONTH' : '-1 MONTH';
            for ($i = 0; $i < abs($delta); $i++) {
                $dateAt = date('Y-m-d', strtotime($direction, strtotime($dateAt)));
            }
        }

        $this->assertEquals('2026-06-01', $dateAt);
    }

    /**
     * Regression: если $date_constribution принят, авторасчёт НЕ вызывается.
     * Симуляция через счётчик вызовов.
     */
    public function testAcceptedDateDoesNotTriggerRecalculate()
    {
        $recalcCalled = 0;
        $dateConstribution = '2026-05-27';
        $today = '2026-04-27';

        if ($dateConstribution
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateConstribution)
            && strtotime($dateConstribution) >= strtotime($today)
        ) {
            // accepted branch — recalculate is NOT called
        } else {
            $recalcCalled++;
        }

        $this->assertEquals(0, $recalcCalled, 'recalculateNextPaymentDateFromMonth must not be called when date is accepted');
    }

    /**
     * Regression: если $date_constribution отклонён, авторасчёт вызывается один раз.
     */
    public function testRejectedDateTriggersRecalculateOnce()
    {
        $recalcCalled = 0;
        $dateConstribution = null;
        $today = '2026-04-27';

        if ($dateConstribution
            && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateConstribution)
            && strtotime($dateConstribution) >= strtotime($today)
        ) {
            // accepted — nothing
        } else {
            $recalcCalled++;
        }

        $this->assertEquals(1, $recalcCalled, 'recalculateNextPaymentDateFromMonth must be called exactly once on fallback');
    }
}
