<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Credit */

$this->title = 'Create Credit';


?>
<div class="credit-create">

    
<br>
    <?= $this->render('_form', [
        'model' => $model,
		'searchModel' => $searchModel,
        'dataProvider' => $dataProvider,

    ]) ?>

</div>
