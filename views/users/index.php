<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\Store;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';

?>
<div class="users-index">

    <h1><?= Html::encode($this->title) ?></h1>

       <p>
        <?= Html::a('Yeni', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
	<?php $store = ArrayHelper::map(Store::find()->all(), 'id', 'name'); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		 'tableOptions'=>[
            'style'=>'width:1000px;',
            'class' => 'table-rena table-rena2 ',

        ],
        'columns' => [
          

          
            'telephone',
            'fio',
            'login',
           // 'password',
            //'id_role',
            //'salary',
             [
                'attribute' => 'id_store',
                'filter' => $store,
                'value' => 'idStore.name',
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true]
                ],
                'filterType' => GridView::FILTER_SELECT2,
                'width' => '200px',
                'filterInputOptions' => ['placeholder' => 'Any type']
            ],

            ['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>


</div>
