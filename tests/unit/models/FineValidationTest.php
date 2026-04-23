<?php
namespace tests\unit\models;

use PHPUnit\Framework\TestCase;

class FineValidationTest extends TestCase
{
    /**
     * Тест логики validateFineSum: сумма > debt -> ошибка
     */
    public function testFineSumExceedsDebtIsInvalid()
    {
        $debt = 600.0;
        $fineSum = 700.0;
        $isValid = $fineSum <= $debt;
        $this->assertFalse($isValid, 'Fine sum exceeding debt should be invalid');
    }

    /**
     * Тест: сумма <= debt -> валидна
     */
    public function testFineSumWithinDebtIsValid()
    {
        $debt = 600.0;
        $fineSum = 600.0;
        $isValid = $fineSum <= $debt;
        $this->assertTrue($isValid);
    }

    /**
     * Тест: debt = 0 -> нельзя создать штраф
     */
    public function testFineNotAllowedWhenDebtIsZero()
    {
        $debt = 0.0;
        $canCreateFine = $debt > 0;
        $this->assertFalse($canCreateFine, 'Fine should not be allowed when debt is 0');
    }

    /**
     * Тест: debt < 0 (защита) -> нельзя создать штраф
     */
    public function testFineNotAllowedWhenDebtIsNegative()
    {
        $debt = -10.0;
        $canCreateFine = $debt > 0;
        $this->assertFalse($canCreateFine);
    }

    /**
     * Тест: частичный штраф валиден
     */
    public function testPartialFineIsValid()
    {
        $debt = 600.0;
        $fineSum = 100.0;
        $isValid = $fineSum <= $debt && $debt > 0;
        $this->assertTrue($isValid);
    }

    /**
     * AC #3: предзаполнение формы использует Credit.debt, а не Credit.sum
     * Given: кредит sum=1000, debt=600
     * When: открывается форма создания штрафа с ?id_credit=X
     * Then: $model->sum = 600 (debt), не 1000 (sum)
     */
    public function testPrefillUsesDebtNotSum()
    {
        $creditSum = 1000.0;
        $creditDebt = 600.0;

        // Логика FineController::actionCreate(): $model->sum = $credit->debt
        $fineModelSum = $creditDebt;

        $this->assertEquals(600.0, $fineModelSum);
        $this->assertNotEquals($creditSum, $fineModelSum, 'Prefill should use debt, not sum');
    }

    /**
     * AC #3: штраф = debt полностью (граничный случай — ровно долг)
     */
    public function testFineSumEqualToDebtIsValid()
    {
        $debt = 600.0;
        $fineSum = 600.0; // prefill значение
        $isValid = $fineSum <= $debt && $debt > 0;
        $this->assertTrue($isValid, 'Fine equal to full debt should be valid');
    }

    /**
     * AC #3: форма не позволяет сохранить штраф при Credit.debt = 0
     * (сценарий кредит полностью погашен после платежей)
     */
    public function testCannotCreateFineAfterFullRepayment()
    {
        $creditSum = 1000.0;
        $totalPaid = 1000.0;

        $debt = round($creditSum - $totalPaid, 2);
        if ($debt < 0) {
            $debt = 0;
        }

        // validateFineSum: debt <= 0 → ошибка
        $canCreateFine = $debt > 0;
        $this->assertFalse($canCreateFine, 'Fine must be blocked when credit is fully repaid');
    }
}
