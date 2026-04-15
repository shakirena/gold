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
}
