<?php

use yii\helpers\Html;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\StoreSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sklad';

?>
<div class="store-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Store', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

   

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'tableOptions'=>[
            'style'=>'width:1000px;',
            'class' => 'table-rena table-rena2 ',

        ],
        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

           
            'name',

            ['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>


</div>
