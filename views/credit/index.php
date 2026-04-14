<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use app\models\Client;
use app\models\Users;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CreditSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="credit-index">

    <h1><?= Html::encode($this->title) ?></h1>

   <?php $clientList = ArrayHelper::map(Client::find()->all(), 'id', 'name'); ?>
   <?php $usersList = ArrayHelper::map(Users::find()->all(), 'id', 'fio'); ?>
   <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [

            'class' => 'table-rena table-rena3',
            'style' => 'font-size:9pt'

        ],
		'showFooter' => true,
        'footerRowOptions' => ['style' => 'font-weight:bold;text-decoration:underline;color:red;'],
        'striped'=>true,
        'hover'=>true,

        'columns' => [
            // ['class' => 'kartik\grid\SerialColumn'],


            [

                'attribute'  => 'id_client',
                'value' =>'client.name',
                'filter' => $clientList,
                'filterWidgetOptions' =>[
                    'pluginOptions'=>['allowClear'=>true]
                ],
                'filterType' =>GridView::FILTER_SELECT2,
                'width' => '300px',
                'filterInputOptions' =>['placeholder'=>'Any type'],
			 'footer' => 'Yekun',
            ] ,
			'number',
			 [

               
                'value' =>'client.phone',
            
            ] ,
			[

                'attribute'  => 'id_user',
                'value' =>'idUser.fio',
                'filter' => $usersList,
                'filterWidgetOptions' =>[
                    'pluginOptions'=>['allowClear'=>true]
                ],
                'filterType' =>GridView::FILTER_SELECT2,
                'width' => '300px',
                'filterInputOptions' =>['placeholder'=>'Any type']
            ] ,
			
			[
				'attribute'  => 'sum',
				'footer' =>  round($searchModel->getSumCredit($dataProvider->query,'sum'),2)
			],
			
            'month_payment',
			[
				'attribute'  => 'commission',
				'footer' =>  round($searchModel->getSumCredit($dataProvider->query,'commission'),2)
			],
           /*[
                'attribute' =>  'date_constribution',
				'value'=>'dateConstribution',
                'format'=>'raw',
               
                'width' => '150px',

                'filter' =>DatePicker::widget([
                    //,

                    'model' => $searchModel,
                    'attribute' => 'date_start1',
                   'value' => date('Y-m-d'),
                    //'options' => ['placeholder' => 'Select issue date ...'],
                    'type' => DatePicker::TYPE_RANGE,
                    'attribute2' => 'date_end1',
                    'value2' => date('Y-m-d'),
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoClose' => true
                        // 'todayHighlight' => false
                    ]
                ]),

                // 'group'=>true,

            ],*/
           
			[
                'attribute' =>  'date_create',
               
                'format'=>'raw',
               
                'width' => '150px',

             

                // 'group'=>true,

            ],
			[
				'attribute'  =>   'debt',
				'footer' =>  round($searchModel->getSumCredit($dataProvider->query,'debt'),2)
			],
			[
				 'label'=> 'Ödəniş сəmi',
				'value'=>'payment',
				'footer' =>  round($searchModel->getSumCredit($dataProvider->query,'payment'),2)
			],
          
            [
					
                    'format' => 'raw',
                     'value' => function ($model, $index, $widget) {
                        return Html::a('<i class="glyphicon glyphicon-ok"></i>  ok', ["view-credit?id=$model->id"], ['class' => 'btn btn-success']);
                        },
            ],
		/*	[
					
                    'format' => 'raw',
                     'value' => 'delete'
            ],
*/
             ['class' => 'kartik\grid\ActionColumn'],
        ],
    ]); ?>

</div>


