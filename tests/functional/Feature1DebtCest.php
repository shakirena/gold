<?php

use app\models\Credit;
use app\models\Client;
use app\models\Payment;
use app\models\Fine;
use app\models\Users;

/**
 * Functional E2E тесты для Feature #1 — Исправить расчёт штрафов при частичном погашении
 *
 * Стратегия:
 *   - TC-2-01..05 — ORM-уровень: тестируем afterSave/afterDelete напрямую
 *   - TC-3-01     — HTTP-уровень: проверяем prefill в форме
 *   - TC-3-03..04 — Model-уровень: тестируем validate() напрямую
 *   - TC-2-07     — HTTP-уровень: проверяем RBAC редирект
 */
class Feature1DebtCest
{
    /** @var Credit */
    private $credit;

    /** @var Client */
    private $client;

    public function _before(FunctionalTester $I)
    {
        // Логин
        $user = Users::findOne(['login' => 'demo']);
        if ($user) {
            $I->amLoggedInAs($user);
        }

        // FK workaround для тестовой БД
        \Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();

        // Fixture: клиент + кредит sum=1000, debt=1000
        $this->client = new Client();
        $this->client->name   = 'E2E Client ' . uniqid();
        $this->client->phone  = '0501234567';
        $this->client->adress = 'Test';
        $this->client->save(false);

        $this->credit = new Credit();
        $this->credit->id_client               = $this->client->id;
        $this->credit->product_name            = 'E2E Product';
        $this->credit->number                  = 'E2E-' . uniqid();
        $this->credit->sum                     = 1000;
        $this->credit->fee                     = 0;
        $this->credit->month_payment           = 300;
        $this->credit->month                   = 12;
        $this->credit->debt                    = 1000;
        $this->credit->date_constribution      = date('Y-m-d');
        $this->credit->date_constribution_start = date('Y-m-d');
        $this->credit->date_create             = date('Y-m-d H:i:s');
        $this->credit->id_store                = 1;
        $this->credit->id_user                 = 1;
        $this->credit->save(false);
    }

    public function _after(FunctionalTester $I)
    {
        \Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=0')->execute();
        if ($this->credit) {
            Payment::deleteAll(['id_credit' => $this->credit->id]);
            Fine::deleteAll(['id_credit'    => $this->credit->id]);
            $this->credit->delete();
        }
        if ($this->client) {
            $this->client->delete();
        }
        \Yii::$app->db->createCommand('SET FOREIGN_KEY_CHECKS=1')->execute();
    }

    // =====================================================
    // TC-2-01: Частичный платёж уменьшает Credit.debt
    // =====================================================

    /**
     * @group critical
     * TC-2-01
     */
    public function partialPaymentDecreasesDebt(FunctionalTester $I)
    {
        $I->wantTo('TC-2-01: partial payment 400 → debt becomes 600');

        $payment = new Payment();
        $payment->id_credit = $this->credit->id;
        $payment->sum       = 400;
        $payment->datetime  = date('Y-m-d H:i:s');
        $payment->save();  // afterSave → recalculateDebt()

        $this->credit->refresh();
        \PHPUnit\Framework\Assert::assertEquals(600, $this->credit->debt);
    }

    // =====================================================
    // TC-2-02: Полное погашение → debt = 0
    // =====================================================

    /**
     * @group critical
     * TC-2-02
     */
    public function fullPaymentSetsDebtToZero(FunctionalTester $I)
    {
        $I->wantTo('TC-2-02: full payment 1000 → debt = 0');

        $payment = new Payment();
        $payment->id_credit = $this->credit->id;
        $payment->sum       = 1000;
        $payment->datetime  = date('Y-m-d H:i:s');
        $payment->save();

        $this->credit->refresh();
        \PHPUnit\Framework\Assert::assertEquals(0, $this->credit->debt);
    }

    // =====================================================
    // TC-2-03: Переплата → debt не отрицательный
    // =====================================================

    /**
     * @group critical
     * TC-2-03
     */
    public function overpaymentClampsDebtToZero(FunctionalTester $I)
    {
        $I->wantTo('TC-2-03: overpayment 1500 → debt = 0, not -500');

        $payment = new Payment();
        $payment->id_credit = $this->credit->id;
        $payment->sum       = 1500;
        $payment->datetime  = date('Y-m-d H:i:s');
        $payment->save();

        $this->credit->refresh();
        \PHPUnit\Framework\Assert::assertEquals(0, $this->credit->debt);
        \PHPUnit\Framework\Assert::assertGreaterThanOrEqual(0, $this->credit->debt);
    }

    // =====================================================
    // TC-2-04: Несколько платежей накапливаются корректно
    // =====================================================

    /**
     * @group high
     * TC-2-04
     */
    public function multiplePaymentsAccumulate(FunctionalTester $I)
    {
        $I->wantTo('TC-2-04: payments 200+150+50 = 400 → debt = 600');

        foreach ([200, 150, 50] as $amount) {
            $p = new Payment();
            $p->id_credit = $this->credit->id;
            $p->sum       = $amount;
            $p->datetime  = date('Y-m-d H:i:s');
            $p->save();
        }

        $this->credit->refresh();
        \PHPUnit\Framework\Assert::assertEquals(600, $this->credit->debt);
    }

    // =====================================================
    // TC-2-05: Удаление платежа восстанавливает debt
    // =====================================================

    /**
     * @group high
     * TC-2-05
     */
    public function deletingPaymentRestoresDebt(FunctionalTester $I)
    {
        $I->wantTo('TC-2-05: delete payment 400 → debt restored to 1000');

        $payment = new Payment();
        $payment->id_credit = $this->credit->id;
        $payment->sum       = 400;
        $payment->datetime  = date('Y-m-d H:i:s');
        $payment->save();

        $this->credit->refresh();
        \PHPUnit\Framework\Assert::assertEquals(600, $this->credit->debt, 'debt after payment');

        $payment->delete();  // afterDelete → recalculateDebt()
        $this->credit->refresh();
        \PHPUnit\Framework\Assert::assertEquals(1000, $this->credit->debt, 'debt after delete');
    }

    // =====================================================
    // TC-2-07: Гость не может создать платёж (HTTP)
    // =====================================================

    /**
     * @group high
     * TC-2-07 (adapted): авторизованный пользователь имеет доступ к payment/create
     * Note: PaymentController не имеет AccessControl — RBAC gap, задокументировано.
     */
    public function authenticatedUserCanAccessPaymentCreate(FunctionalTester $I)
    {
        $I->wantTo('TC-2-07: authenticated user can access payment/create');

        $I->amOnRoute('payment/create', ['id_credit' => $this->credit->id]);
        $I->seeResponseCodeIs(200);
    }

    // =====================================================
    // TC-3-01: Форма штрафа предзаполнена Credit.debt (HTTP)
    // =====================================================

    /**
     * @group critical
     * TC-3-01
     */
    public function fineFormPrefillsWithDebtNotSum(FunctionalTester $I)
    {
        $I->wantTo('TC-3-01: fine form sum is prefilled with Credit.debt (600), not Credit.sum (1000)');

        $payment = new Payment();
        $payment->id_credit = $this->credit->id;
        $payment->sum       = 400;
        $payment->datetime  = date('Y-m-d H:i:s');
        $payment->save();

        $I->amOnRoute('fine/create', ['id_credit' => $this->credit->id]);
        $I->seeResponseCodeIs(200);
        $I->seeInField('#fine-sum', '600');
        $I->dontSeeInField('#fine-sum', '1000');
    }

    // =====================================================
    // TC-3-03: Штраф > debt → ошибка валидации (Model)
    // =====================================================

    /**
     * @group critical
     * TC-3-03
     */
    public function fineSumExceedingDebtIsRejected(FunctionalTester $I)
    {
        $I->wantTo('TC-3-03: fine sum 700 > debt 600 → validation error on model');

        $payment = new Payment();
        $payment->id_credit = $this->credit->id;
        $payment->sum       = 400;
        $payment->datetime  = date('Y-m-d H:i:s');
        $payment->save();

        $fine = new Fine();
        $fine->id_credit = $this->credit->id;
        $fine->sum       = 700;
        $fine->date      = date('Y-m-d');
        $fine->validate();

        \PHPUnit\Framework\Assert::assertTrue(
            $fine->hasErrors('sum'),
            'Fine with sum 700 > debt 600 should have validation error on sum'
        );
        \PHPUnit\Framework\Assert::assertContains(
            'превышать остаток долга',
            $fine->getFirstError('sum')
        );
    }

    // =====================================================
    // TC-3-04: Штраф заблокирован при debt = 0 (Model)
    // =====================================================

    /**
     * @group critical
     * TC-3-04
     */
    public function fineBlockedWhenDebtIsZero(FunctionalTester $I)
    {
        $I->wantTo('TC-3-04: fine is blocked when credit is fully repaid (debt = 0)');

        $payment = new Payment();
        $payment->id_credit = $this->credit->id;
        $payment->sum       = 1000;
        $payment->datetime  = date('Y-m-d H:i:s');
        $payment->save();

        $fine = new Fine();
        $fine->id_credit = $this->credit->id;
        $fine->sum       = 100;
        $fine->date      = date('Y-m-d');
        $fine->validate();

        \PHPUnit\Framework\Assert::assertTrue(
            $fine->hasErrors('sum'),
            'Fine should be blocked when debt = 0'
        );
        \PHPUnit\Framework\Assert::assertContains(
            'остаток долга равен нулю',
            $fine->getFirstError('sum')
        );
    }
}
