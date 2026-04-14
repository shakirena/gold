<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Kassa */

$this->title = 'Create Kassa';
$this->params['breadcrumbs'][] = ['label' => 'Kassas', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kassa-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
