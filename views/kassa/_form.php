<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Kassa */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="kassa-form">

    <?php $form = ActiveForm::begin(); ?>
	<?php
		 $date = [0 => "Sade", 1 =>"POS"]; ?>
    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
	<?= $form->field($model, 'pos')->widget(Select2::className(),[
        'data' => $date,
        'options' => [
            'placeholder' => 'Seçin',

        ]

    ]); ?>

   

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
