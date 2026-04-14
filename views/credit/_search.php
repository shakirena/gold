<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CreditSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'id_client') ?>

    <?= $form->field($model, 'product_name') ?>

    <?= $form->field($model, 'sum') ?>

    <?= $form->field($model, 'fee') ?>

    <?php // echo $form->field($model, 'month') ?>

    <?php // echo $form->field($model, 'month_payment') ?>

    <?php // echo $form->field($model, 'date_constribution') ?>

    <?php // echo $form->field($model, 'date_create') ?>

    <?php // echo $form->field($model, 'debt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
