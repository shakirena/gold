<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Fine */

$this->title = 'Create Fine';
$this->params['breadcrumbs'][] = ['label' => 'Fines', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fine-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
