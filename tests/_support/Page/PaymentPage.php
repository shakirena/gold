<?php
namespace Page;

class PaymentPage
{
    // URLs
    public static $URL        = '/payment/index';
    public static $createURL  = '/payment/create';

    // Selectors — Yii2 генерирует id как {model}-{attribute}
    public static $fieldSum      = '#payment-sum';
    public static $fieldDatetime = '#payment-datetime';
    public static $fieldIdCredit = '#payment-id_credit';
    public static $saveButton    = 'button[type="submit"]';

    // Debt display on credit page
    public static $debtCell = 'td.debt-value';
}
