<?php
namespace Page;

class FinePage
{
    // URLs
    public static $URL       = '/fine/index';
    public static $createURL = '/fine/create';

    // Selectors
    public static $fieldSum      = '#fine-sum';
    public static $fieldIdCredit = '#fine-id_credit';
    public static $saveButton    = 'button[type="submit"]';

    // Validation error block (Bootstrap 3 / Yii2)
    public static $errorBlock = '.help-block';
    public static $errorSummary = '.error-summary';
}
