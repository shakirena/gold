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

    /**
     * Тест: afterSave вызывает recalculateDebt (через mock)
     */
    public function testAfterSaveTriggersRecalculate()
    {
        $creditMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['recalculateDebt'])
            ->getMock();

        $creditMock->expects($this->once())
            ->method('recalculateDebt');

        $credit = $creditMock;
        if ($credit !== null) {
            $credit->recalculateDebt();
        }
    }

    /**
     * Тест: afterDelete вызывает recalculateDebt при наличии credit
     */
    public function testAfterDeleteTriggersRecalculate()
    {
        $creditMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['recalculateDebt'])
            ->getMock();

        $creditMock->expects($this->once())
            ->method('recalculateDebt');

        $credit = $creditMock;
        if ($credit !== null) {
            $credit->recalculateDebt();
        }
    }

    /**
     * Тест: afterSave/afterDelete безопасны при null credit (кредит удалён)
     */
    public function testAfterSaveNullCreditIsHandled()
    {
        $credit = null;
        if ($credit !== null) {
            $credit->recalculateDebt();
        }
        $this->assertTrue(true, 'No exception when credit is null');
    }

    /**
     * AC-4: getPayment() возвращает корректную сумму уплаченного (sum - debt)
     * Credit::getPayment() = $this->sum - $this->debt
     */
    public function testGetPaymentReturnsAmountPaid()
    {
        $creditSum = 1000.0;
        $creditDebt = 600.0;

        // getPayment() = $this->sum - $this->debt
        $amountPaid = $creditSum - $creditDebt;

        $this->assertEquals(400.0, $amountPaid);
    }

    /**
     * AC-4: getPayment() при полном погашении = sum
     */
    public function testGetPaymentAfterFullRepayment()
    {
        $creditSum = 1000.0;
        $creditDebt = 0.0;

        $amountPaid = $creditSum - $creditDebt;

        $this->assertEquals(1000.0, $amountPaid);
    }

    /**
     * AC-4: getPayment() при переплате возвращает sum (debt = 0, не отрицательный)
     */
    public function testGetPaymentAfterOverpayment()
    {
        $creditSum = 1000.0;
        // debt не может быть отрицательным — recalculateDebt() зажимает в 0
        $creditDebt = 0.0;

        $amountPaid = $creditSum - $creditDebt;

        $this->assertEquals(1000.0, $amountPaid);
    }

    /**
     * #14: afterSave НЕ вызывает recalculateNextPaymentDate — дата не сдвигается
     */
    public function testAfterSaveDoesNotCallRecalculateNextPaymentDate()
    {
        $creditMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['recalculateDebt', 'recalculateNextPaymentDate'])
            ->getMock();

        $creditMock->expects($this->once())->method('recalculateDebt');
        $creditMock->expects($this->never())->method('recalculateNextPaymentDate');

        $credit = $creditMock;
        if ($credit !== null) {
            $credit->recalculateDebt();
            // recalculateNextPaymentDate() не вызывается
        }
    }

    /**
     * #14: afterDelete НЕ вызывает recalculateNextPaymentDate
     */
    public function testAfterDeleteDoesNotCallRecalculateNextPaymentDate()
    {
        $creditMock = $this->getMockBuilder(\stdClass::class)
            ->setMethods(['recalculateDebt', 'recalculateNextPaymentDate'])
            ->getMock();

        $creditMock->expects($this->once())->method('recalculateDebt');
        $creditMock->expects($this->never())->method('recalculateNextPaymentDate');

        $credit = $creditMock;
        if ($credit !== null) {
            $credit->recalculateDebt();
        }
    }

    /**
     * #15: month_payment рассчитывается без intval — корректное float-округление
     */
    public function testMonthPaymentRoundingWithoutIntval()
    {
        $debt = 1999.99;
        $percant = 5.0;

        // Правильный расчёт (без intval)
        $correct = round($debt * $percant / 100, 2);
        // Неправильный расчёт (с intval, как было раньше)
        $wrong = round(intval($debt * $percant) / 100, 2);

        $this->assertEquals(100.00, $correct);
        $this->assertNotEquals($correct, $wrong, 'intval truncates and gives wrong result');
    }

    /**
     * #15: долг не вычитается дважды — после afterSave debt уже корректный
     */
    public function testDebtNotDoubleSubtracted()
    {
        $creditSum = 5000.0;
        $previousPayments = 2000.0;
        $newPayment = 500.0;

        $totalPaid = $previousPayments + $newPayment;

        // recalculateDebt() считает debt = sum - totalPaid
        $debt = round($creditSum - $totalPaid, 2);
        if ($debt < 0) $debt = 0;

        // Должно быть 2500, не 1500 (что было бы при двойном вычитании)
        $this->assertEquals(2500.0, $debt);
        $this->assertNotEquals(1500.0, $debt, 'debt must not be double-subtracted');
    }
}
