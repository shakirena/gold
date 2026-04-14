<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Kassa */

$this->title = 'Update Kassa: ' . $model->name;


?>
<div class="kassa-update update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
