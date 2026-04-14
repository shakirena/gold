<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="client-index">


    <p>
        <?= Html::a('Yeni Müştəri', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [

            'class' => 'table-rena table-rena3',
            'style' => 'font-size:9pt'

        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],


            'name',
            'phone:ntext',
            'adress:ntext',
			'note:ntext',
			'phone2:ntext',
            'adress2:ntext',
           

            ['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>


</div>
