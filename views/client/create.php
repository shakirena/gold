<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Client */

$this->title = 'Create Client';

?>
<div class="client-create">

   

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
