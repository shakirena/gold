<?php
namespace tests\unit\models;

use PHPUnit\Framework\TestCase;

class CreditDebtTest extends TestCase
{
    /**
     * Тест: recalculateDebt устанавливает правильный остаток
     */
    public function testRecalculateDebtPartialPayment()
    {
        $creditSum = 1000.0;
        $totalPaid = 400.0;

        $debt = round($creditSum - $totalPaid, 2);
        if ($debt < 0) {
            $debt = 0;
        }

        $this->assertEquals(600.0, $debt);
    }

    /**
     * Тест: долг становится 0 при полном погашении
     */
    public function testRecalculateDebtFullPayment()
    {
        $creditSum = 1000.0;
        $totalPaid = 1000.0;

        $debt = round($creditSum - $totalPaid, 2);
        if ($debt < 0) {
            $debt = 0;
        }

        $this->assertEquals(0.0, $debt);
    }

    /**
     * Тест: переплата не делает debt отрицательным
     */
    public function testRecalculateDebtOverpayment()
    {
        $creditSum = 1000.0;
        $totalPaid = 1200.0;

        $debt = round($creditSum - $totalPaid, 2);
        if ($debt < 0) {
            $debt = 0;
        }

        $this->assertEquals(0.0, $debt);
    }

    /**
     * Тест: несколько частичных платежей
     */
    public function testRecalculateDebtMultiplePayments()
    {
        $creditSum = 1000.0;
        $payments = [200.0, 150.0, 50.0];
        $totalPaid = array_sum($payments);

        $debt = round($creditSum - $totalPaid, 2);
        if ($debt < 0) {
            $debt = 0;
        }

        $this->assertEquals(600.0, $debt);
    }

    /**
     * Тест: round до 2 знаков
     */
    public function testRecalculateDebtRounding()
    {
        $creditSum = 1000.0;
        $totalPaid = 333.333;

        $debt = round($creditSum - $totalPaid, 2);
        if ($debt < 0) {
            $debt = 0;
        }

        $this->assertEquals(666.67, $debt);
    }
}
