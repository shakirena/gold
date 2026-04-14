<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use app\models\Roles;
use app\models\Store;
use yii\helpers\ArrayHelper;
/* @var $this yii\web\View */
/* @var $model app\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-form">

    <?php $form = ActiveForm::begin(); ?>



    <?= $form->field($model, 'fio')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'login')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password')->passwordInput(['maxlength' => true,'value' => false]) ?>

    <?= $form->field($model, 'id_role')->widget(Select2::className(),[
        'data' =>  ArrayHelper::map(Roles::find()->all(), 'id_role', 'role'),
        'options' => [
            'placeholder' => 'Seçin',

]

    ]); ?>
	
	<?= $form->field($model, 'id_store')->widget(Select2::className(),[
        'data' =>  ArrayHelper::map(Store::find()->all(), 'id', 'name'),
        'options' => [
            'placeholder' => 'Seçin',

		]

    ]); ?>

  
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Əlavə et' : 'Yaddaşa ver', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
