<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Month */

$this->title = 'Create Month';
$this->params['breadcrumbs'][] = ['label' => 'Months', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="month-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
