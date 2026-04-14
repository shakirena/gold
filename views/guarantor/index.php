<?php

use yii\helpers\Html;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel app\models\GuarantorSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Zamin';

?>
<div class="guarantor-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Guarantor', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

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
            'phone',
            'adress:ntext',
            'note:ntext',
            'passport',

            ['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>


</div>
