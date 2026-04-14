<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="client-form">
	<div class="row">
		
			<?php $form = ActiveForm::begin();
				$model->phone = '+994';

			?>
			<div class="col-md-6">
				<?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
				<?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>
			<?= $form->field($model, 'phone2')->textInput(['maxlength' => true]) ?>
			<?= $form->field($model, 'adress')->textInput(['maxlength' => true]) ?>
			<?= $form->field($model, 'adress2')->textInput(['maxlength' => true]) ?>
			<?= $form->field($model, 'passport')->textInput(['maxlength' => true]) ?>
			<?= $form->field($model, 'fin')->textInput(['maxlength' => true]) ?>
			<?= $form->field($model, 'note')->textarea(['rows' => 3]) ?>

			<div class="form-group">
				<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
			</div>

			<?php ActiveForm::end(); ?>
	</div>
</div>
