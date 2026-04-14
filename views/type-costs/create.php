<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\TypeCosts */

$this->title = 'Create Type Costs';
$this->params['breadcrumbs'][] = ['label' => 'Type Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="type-costs-create">



    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
