<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Products */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="products-form">

    <?php $form = ActiveForm::begin([
			'id' => 'product-form',
	
	]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'weight')->textInput() ?>

    <?= $form->field($model, 'net_weight')->textInput() ?>

	<?= $form->field($image, 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success', 'id' => 'saveProductButton']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
