<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TypeCostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="type-costs-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <p>
        <?= Html::button('<i class="glyphicon glyphicon-plus"></i>Əlavə et', ['value' => Url::to(['create']), 'class' => 'btn btn-danger', 'id' => 'product']) ?>
    </p>
    <?php
    Modal::begin([
        'header' => '<h2>Yeni mal adının açılması</h2>',
        'id' => 'product-create',
        'size' => 'modal-sm',
		  'options' => [
          
            'tabindex' => true,

        ],

    ]);

    echo '<div id="modalContent"></div>';

    Modal::end();
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'tableOptions'=>[
            'style'=>'width:1000px;',
            'class' => 'table-rena table-rena2 ',

        ]
        ,
        'pjax' => true,
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

            'id',
            'name',

            ['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>
</div>
