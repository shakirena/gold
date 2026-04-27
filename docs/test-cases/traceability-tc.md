# TC Traceability Matrix

| TC ID | Feature | Story | AC | Priority | Unit Test |
|-------|---------|-------|-----|---------|----------|
| TC-1-01 | #1 | #1 | AC-1 (partial payment → debt decreases) | Critical | CreditDebtTest::testRecalculateDebtPartialPayment |
| TC-1-02 | #1 | #1 | AC-1 (multiple payments) | High | CreditDebtTest::testRecalculateDebtMultiplePayments |
| TC-1-03 | #1 | #1 | AC-1 (overpayment clamp) | Critical | CreditDebtTest::testRecalculateDebtOverpayment |
| TC-1-04 | #1 | #1 | AC-2 (form prefill = debt not sum) | Critical | FineValidationTest::testPrefillUsesDebtNotSum |
| TC-1-05 | #1 | #1 | AC-2 (fine blocked when debt=0) | Critical | FineValidationTest::testFineNotAllowedWhenDebtIsZero |
| TC-1-06 | #1 | #1 | AC-3 (payment history reflects debt) | High | CreditDebtTest (multiple payments) |
| TC-1-07 | #1 | #1 | AC-4 (full repayment edge case) | Critical | CreditDebtTest::testRecalculateDebtFullPayment |
| TC-1-08 | #1 | #1 | AC-4 (zero debt blocks fine) | Critical | FineValidationTest::testFineNotAllowedWhenDebtIsZero |
| TC-2-01 | #1 | #2 | AC-1 (partial payment) | Critical | tests/unit/models/CreditDebtTest.php::testRecalculateDebtPartialPayment |
| TC-2-02 | #1 | #2 | AC-1 (full payment) | Critical | tests/unit/models/CreditDebtTest.php::testRecalculateDebtFullPayment |
| TC-2-03 | #1 | #2 | AC-1 (overpayment) | Critical | tests/unit/models/CreditDebtTest.php::testRecalculateDebtOverpayment |
| TC-2-04 | #1 | #2 | AC-1 (multiple payments) | High | tests/unit/models/CreditDebtTest.php::testRecalculateDebtMultiplePayments |
| TC-2-05 | #1 | #2 | AC-1 (afterDelete) | High | tests/unit/models/CreditDebtTest.php::testAfterDeleteTriggersRecalculate |
| TC-2-06 | #1 | #2 | AC-1 (rounding) | Medium | tests/unit/models/CreditDebtTest.php::testRecalculateDebtRounding |
| TC-2-07 | #1 | #2 | AC-1 (RBAC) | Critical | — (functional test) |
| TC-3-01 | #1 | #3 | AC-1 (prefill: debt not sum) | Critical | tests/unit/models/FineValidationTest.php::testPrefillUsesDebtNotSum |
| TC-3-02 | #1 | #3 | AC-1 (fine = full debt) | High | tests/unit/models/FineValidationTest.php::testFineSumEqualToDebtIsValid |
| TC-3-03 | #1 | #3 | AC-1 (fine > debt → error) | Critical | tests/unit/models/FineValidationTest.php::testFineSumExceedsDebtIsInvalid |
| TC-3-04 | #1 | #3 | AC-2 (debt = 0 → blocked) | Critical | tests/unit/models/FineValidationTest.php::testFineNotAllowedWhenDebtIsZero |
| TC-3-05 | #1 | #3 | AC-2 (debt < 0 → blocked) | High | tests/unit/models/FineValidationTest.php::testFineNotAllowedWhenDebtIsNegative |
| TC-3-06 | #1 | #3 | AC-1 (partial fine valid) | Medium | tests/unit/models/FineValidationTest.php::testPartialFineIsValid |
| TC-3-07 | #1 | #3 | AC-2 (full repayment → no fine) | Critical | tests/unit/models/FineValidationTest.php::testCannotCreateFineAfterFullRepayment |
| TC-3-08 | #1 | #3 | AC-1 (fine within debt valid) | High | tests/unit/models/FineValidationTest.php::testFineSumWithinDebtIsValid |
| TC-5-01 | #5 | #5 | AC-1 (unit suite без DB-ошибок) | Critical | manual: OK (24 tests, 47 assertions) |
| TC-5-02 | #5 | #5 | AC-1 (LoginFormTest passes) | Critical | LoginFormTest::testLoginCorrect |
| TC-5-03 | #5 | #5 | AC-1 (ContactFormTest / Yii2 bootstrap) | High | ContactFormTest::testEmailIsSentOnContact |
| TC-5-04 | #5 | #5 | AC-1 (setup-test-db.sh воспроизводимо) | High | manual smoke |
| TC-5-05 | #5 | #5 | AC-1 (regression: FineValidationTest + CreditDebtTest) | Critical | all 16 tests green |
| TC-4-01 | #1 | #4 | AC-1 (partial payment → debt=600) | Critical | CreditDebtTest::testRecalculateDebtPartialPayment |
| TC-4-02 | #1 | #4 | AC-2 (full payment → debt=0) | Critical | CreditDebtTest::testRecalculateDebtFullPayment |
| TC-4-03 | #1 | #4 | AC-3 (overpayment → debt=0, not negative) | Critical | CreditDebtTest::testRecalculateDebtOverpayment |
| TC-4-04 | #1 | #4 | AC-1 (multiple payments sum) | High | CreditDebtTest::testRecalculateDebtMultiplePayments |
| TC-4-05 | #1 | #4 | AC-1 (rounding to 2 decimals) | Medium | CreditDebtTest::testRecalculateDebtRounding |
| TC-4-06 | #1 | #4 | AC-1 (afterSave triggers recalculate) | High | CreditDebtTest::testAfterSaveTriggersRecalculate |
| TC-4-07 | #1 | #4 | AC-1 (afterDelete triggers recalculate) | High | CreditDebtTest::testAfterDeleteTriggersRecalculate |
| TC-4-08 | #1 | #4 | AC-1 (null credit safe) | Medium | CreditDebtTest::testAfterSaveNullCreditIsHandled |
| TC-4-09 | #1 | #4 | AC-4 (getPayment = sum - debt, partial) | Critical | CreditDebtTest::testGetPaymentReturnsAmountPaid |
| TC-4-10 | #1 | #4 | AC-4 (getPayment after full repayment) | High | CreditDebtTest::testGetPaymentAfterFullRepayment |
| TC-4-11 | #1 | #4 | AC-4 (getPayment after overpayment) | High | CreditDebtTest::testGetPaymentAfterOverpayment |
| TC-7-01 | #6 | #7 | AC-1 (1 платёж = +1 месяц) | Critical | CreditDateTest::testOneFullPaymentShiftsDateByOneMonth |
| TC-7-02 | #6 | #7 | AC-3 (2×month_payment → +2 месяца) | High | CreditDateTest::testDoublePaymentShiftsDateByTwoMonths |
| TC-7-03 | #6 | #7 | AC-4 (частичный платёж → без сдвига) | Critical | CreditDateTest::testPartialPaymentDoesNotShiftDate |
| TC-7-04 | #6 | #7 | AC-2 (нет платежей → start) | Critical | CreditDateTest::testNoPaymentsReturnsStartDate |
| TC-7-05 | #6 | #7 | AC-5 (SUM двух платежей > month_payment → +1 мес) | High | CreditDateTest::testTwoPartialPaymentsCoveringOneMonth |
| TC-7-06 | #6 | #7 | Edge: month_payment=0 → защита | Medium | CreditDateTest::testZeroMonthPaymentIsHandledSafely |
| TC-7-07 | #6 | #7 | Edge: startDate=null → защита | Medium | CreditDateTest::testNullStartDateIsHandledSafely |
| TC-7-08 | #6 | #7 | AC-1 граница: точное покрытие 3 месяцев | High | CreditDateTest::testExactPaymentCoverageShiftsExactMonths |
| TC-7-09 | #6 | #7 | AC-3: переплата → только полные месяцы | High | CreditDateTest::testOverpaymentClampedToFullMonths |
| TC-8-01 | #6 | #8 | AC: тест-файл запускается, 9 тестов green | Critical | CreditDateTest (all 9) |
| TC-8-02 | #6 | #8 | AC: 1 платёж = +1 месяц | Critical | CreditDateTest::testOneFullPaymentShiftsDateByOneMonth |
| TC-8-03 | #6 | #8 | AC: платёж 2×mp = +2 месяца | High | CreditDateTest::testDoublePaymentShiftsDateByTwoMonths |
| TC-8-04 | #6 | #8 | AC: частичный платёж = без сдвига | Critical | CreditDateTest::testPartialPaymentDoesNotShiftDate |
| TC-8-05 | #6 | #8 | AC: нет платежей = start (откат) | Critical | CreditDateTest::testNoPaymentsReturnsStartDate |
| TC-8-06 | #6 | #8 | Regression: 36 тестов, 59 assertions, no regressions | Critical | Full suite |
| TC-10-01 | #9 | #10 | AC-1: DatePicker date_constribution убран из _form.php | Critical | Static: grep _form.php |
| TC-10-02 | #9 | #10 | AC-1: DatePicker date_constribution_start присутствует | Critical | Static: grep _form.php |
| TC-10-03 | #9 | #10 | AC-2: date_constribution = date_constribution_start в actionCreate | Critical | Static: grep CreditController.php |
| TC-10-04 | #9 | #10 | AC-3: read-only views не изменились | High | Static: файлы не менялись |
| TC-10-05 | #9 | #10 | AC-4: 36/36 unit-тестов зелёные | Critical | Full suite |
| TC-2-01 | #1 | #2 | AC-1: partial payment 400 → debt=600 | Critical | Feature1DebtCest::partialPaymentDecreasesDebt — E2E ✅ |
| TC-2-02 | #1 | #2 | AC-1: full payment → debt=0 | Critical | Feature1DebtCest::fullPaymentSetsDebtToZero — E2E ✅ |
| TC-2-03 | #1 | #2 | AC-1: overpayment → debt≥0 | Critical | Feature1DebtCest::overpaymentClampsDebtToZero — E2E ✅ |
| TC-2-04 | #1 | #2 | AC-1: multiple payments accumulate | High | Feature1DebtCest::multiplePaymentsAccumulate — E2E ✅ |
| TC-2-05 | #1 | #2 | AC-1: delete payment restores debt | High | Feature1DebtCest::deletingPaymentRestoresDebt — E2E ✅ |
| TC-2-07 | #1 | #2 | AC-1: auth user accesses payment/create | High | Feature1DebtCest::authenticatedUserCanAccessPaymentCreate — E2E ✅ (RBAC gap noted) |
| TC-3-01 | #1 | #3 | AC-1: fine form prefilled with debt not sum | Critical | Feature1DebtCest::fineFormPrefillsWithDebtNotSum — E2E ✅ |
| TC-3-03 | #1 | #3 | AC-1: fine sum > debt → validation error | Critical | Feature1DebtCest::fineSumExceedingDebtIsRejected — E2E ✅ |
| TC-3-04 | #1 | #3 | AC-2: fine blocked when debt=0 | Critical | Feature1DebtCest::fineBlockedWhenDebtIsZero — E2E ✅ |
| TC-12-01 | #11 | #12 | AC-1: DatePicker «Növbəti ödəniş» отсутствует в view_credit.php | Critical | Static: grep view_credit.php |
| TC-14-01 | #13 | #14 | AC-1: afterSave не вызывает recalculateNextPaymentDate | Critical | CreditDebtTest::testAfterSaveDoesNotCallRecalculateNextPaymentDate |
| TC-14-02 | #13 | #14 | AC-1/AC-4: afterDelete не вызывает recalculateNextPaymentDate | Critical | CreditDebtTest::testAfterDeleteDoesNotCallRecalculateNextPaymentDate |
| TC-14-03 | #13 | #14 | AC-1: recalculateNextPaymentDate удалён из Payment.php | Critical | Static: grep models/Payment.php |
| TC-15-01 | #13 | #15 | AC-3: month_payment = round(debt*percant/100,2) без intval | Critical | CreditDebtTest::testMonthPaymentRoundingWithoutIntval |
| TC-15-02 | #13 | #15 | AC-2: debt не вычитается дважды | Critical | CreditDebtTest::testDebtNotDoubleSubtracted |
| TC-15-03 | #13 | #14+#15 | AC-5: regression 40/40 тестов | Critical | Full suite |
| TC-12-02 | #11 | #12 | AC-4: неиспользуемый import DatePicker удалён | High | Static: grep view_credit.php |
| TC-12-03 | #11 | #12 | AC-2: авторасчёт date_constribution работает | Critical | CreditDateTest (9 tests) |
| TC-12-04 | #11 | #12 | AC-3: регрессия — 36/36 unit-тестов зелёные | Critical | Full suite |
| TC-16-01 | #16 | #17 | AC-1: actionPaymentMonth сдвигает дату +1 месяц | Critical | CreditDateTest::testPaymentMonthShiftsDatePlusOneMonth |
| TC-16-02 | #16 | #17 | AC-2: actionDeleteMonth откатывает дату -1 месяц | Critical | CreditDateTest::testDeleteMonthShiftsDateMinusOneMonth |
| TC-16-03 | #16 | #17 | AC-3: пустой $date с фронтенда не влияет | Critical | CreditDateTest::testPaymentMonthIgnoresFrontendDateParam |
| TC-16-04 | #16 | #17 | NFR: конец месяца — валидная дата | Medium | CreditDateTest::testPaymentMonthEndOfMonth |
| TC-18-01 | #18 | #19 | AC-1: partial payment < month_payment → дата = start | Critical | CreditDateTest::testSmartRecalcPartialPaymentNoDateChange |
| TC-18-02 | #18 | #19 | AC-1: нет платежей → дата = start | Critical | CreditDateTest::testSmartRecalcZeroTotalNoDateChange |
| TC-18-03 | #18 | #19 | AC-2: 1 полный месяц → start+1M | Critical | CreditDateTest::testSmartRecalcOneFullMonthShiftsOnce |
| TC-18-04 | #18 | #19 | AC-3: 2 полных месяца → start+2M | Critical | CreditDateTest::testSmartRecalcTwoFullMonthsShiftsTwice |
| TC-18-05 | #18 | #19 | AC-3: переплата → clamp к полным месяцам | High | CreditDateTest::testSmartRecalcOverpaymentClampedToFullMonths |
| TC-18-06 | #18 | #19 | AC-4: 3 накопленных платежа → start+3M | High | CreditDateTest::testSmartRecalcThreeMonthsAccumulated |
| TC-18-07 | #18 | #19 | AC-5: после удаления платежа корректный пересчёт | Critical | CreditDateTest::testSmartRecalcAfterDeleteCorrectlyRecomputes |
| TC-18-08 | #18 | #19 | NFR: month_payment=0 безопасно | Medium | CreditDateTest::testSmartRecalcZeroMonthPaymentSafe |
| TC-18-09 | #18 | #19 | NFR: startDate=null безопасно | Medium | CreditDateTest::testSmartRecalcNullStartDateSafe |
| TC-18-10 | #18 | #19 | AC-6: регрессия 53/53 тестов | Critical | Full suite |
| TC-20-01 | #20 | #20 | AC-1: #month_payment предзаполнен model->month_payment | Critical | Static: view_credit.php:166 |
| TC-20-02 | #20 | #20 | AC-4: регрессия 53/53 тестов | Critical | Full suite |
| TC-20-03 | #20 | #20 | AC-2: 1×mp → дата +1 (логика #18) | Critical | CreditDateTest::testSmartRecalcOneFullMonthShiftsOnce |
| TC-20-04 | #20 | #20 | AC-3: 2×mp → дата +2 (корректное поведение) | High | CreditDateTest::testSmartRecalcTwoFullMonthsShiftsTwice |
| TC-23-01 | #23 | #23 | AC-1: today's date accepted as-is | Critical | CreditPaymentMonthDateTest::testTodayDateIsAccepted |
| TC-23-02 | #23 | #23 | AC-2: future date accepted as-is | Critical | CreditPaymentMonthDateTest::testFutureDateIsAccepted |
| TC-23-02b | #23 | #23 | AC-2: tomorrow (+1 day) accepted | High | CreditPaymentMonthDateTest::testTomorrowDateIsAccepted |
| TC-23-03 | #23 | #23 | AC-3: past date → fallback to recalc | Critical | CreditPaymentMonthDateTest::testPastDateFallsBackToRecalc |
| TC-23-03b | #23 | #23 | AC-3: old past date → fallback | High | CreditPaymentMonthDateTest::testOldPastDateFallsBackToRecalc |
| TC-23-04 | #23 | #23 | AC-4: wrong format (DD-MM-YYYY) → fallback | Critical | CreditPaymentMonthDateTest::testInvalidFormatStringFallsBackToRecalc |
| TC-23-04b | #23 | #23 | AC-4: empty string → fallback | Critical | CreditPaymentMonthDateTest::testEmptyStringFallsBackToRecalc |
| TC-23-04c | #23 | #23 | AC-4: arbitrary text → fallback | High | CreditPaymentMonthDateTest::testArbitraryTextFallsBackToRecalc |
| TC-23-04d | #23 | #23 | AC-4: datetime string → fallback | High | CreditPaymentMonthDateTest::testDateWithTimeFallsBackToRecalc |
| TC-23-04e | #23 | #23 | AC-4: alpha chars → fallback | Medium | CreditPaymentMonthDateTest::testAlphanumericStringFallsBackToRecalc |
| TC-23-04f | #23 | #23 | AC-4: partial format (YYYY-MM-D) → fallback | Medium | CreditPaymentMonthDateTest::testPartiallyMatchingFormatFallsBackToRecalc |
| TC-23-05 | #23 | #23 | AC-5: null → fallback to recalc | Critical | CreditPaymentMonthDateTest::testNullDateFallsBackToRecalc |
| TC-23-06 | #23 | #23 | Regression: accepted date does not call recalculate | Critical | CreditPaymentMonthDateTest::testAcceptedDateDoesNotTriggerRecalculate |
| TC-23-07 | #23 | #23 | Regression: rejected date calls recalculate once | Critical | CreditPaymentMonthDateTest::testRejectedDateTriggersRecalculateOnce |
| TC-23-08 | #23 | #23 | NFR: delta algorithm unaffected by feature #23 | High | CreditPaymentMonthDateTest::testDeltaAlgorithmRegression |
| TC-23-09 | #23 | #23 | NFR: full suite regression — all tests green | Critical | Full suite |
| TC-27-01 | #27 | #28 | AC-1: date row hidden on page load | Critical | Frontend (visual) |
| TC-27-02 | #27 | #28 | AC-2: date row appears when #month_payment > 0 | Critical | Frontend (interaction) |
| TC-27-03 | #27 | #28 | AC-3: date row hides when #month_payment cleared | Critical | Frontend (interaction) |
| TC-27-04 | #27 | #28 | AC-4: #month_payment defaults to 0 | High | Frontend (visual) |
| TC-27-05 | #27 | #28 | AC-5: date pre-filled +1 month when row visible | Medium | Frontend (visual) |
| TC-27-06 | #27 | #28 | Regression: debt/fine payments unaffected | High | Frontend (regression) |
| TC-27-07 | #27 | #28 | NFR: all 69 unit tests remain green | Critical | Full suite |
