<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Store;
$this->title = 'TuReN Credit';
?>
<div class="container  ">
    <div class="col-md-4"></div>
    <div class="col-md-4 site-login">
        <!-- <h1><? /*= Html::encode('Login') */ ?></h1>-->

        <!--  <?= Html::img("img/merinos.jpg")?>-->


        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-7\">{input}</div>\n<div class=\"col-lg-7\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-4 control-label'],
            ],
        ]); ?>

        <?= $form->field($model, 'username') ?>

        <?= $form->field($model, 'password')->passwordInput() ?>


        <?= $form->field($model, 'rememberMe')->checkbox([
            'template' => "<div class=\"col-lg-offset-2 col-lg-7\">{input} {label}</div>\n<div class=\"col-lg-7\">{error}</div>",
        ]) ?>

        <div class="form-group">
            <div class="col-lg-offset-8 col-lg-2">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-4"></div>
</div>

