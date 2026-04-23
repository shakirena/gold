<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Fine */
/* @var $form yii\widgets\ActiveForm */
/* @var $credit app\models\Credit|null */
?>

<div class="fine-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php if ($credit !== null): ?>
        <?= $form->field($model, 'id_credit')->hiddenInput()->label(false) ?>
        <div class="form-group">
            <label class="control-label">Кредит</label>
            <p class="form-control-static">
                <?= Html::encode($credit->number) ?>
                (остаток долга: <strong><?= Html::encode($credit->debt) ?></strong>)
            </p>
        </div>
    <?php else: ?>
        <?= $form->field($model, 'id_credit')->textInput() ?>
    <?php endif; ?>

    <?= $form->field($model, 'sum')->textInput(['step' => '0.01']) ?>

    <?= $form->field($model, 'note')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
