<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use app\models\TypeCosts;
use app\models\Kassa;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\CostsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="costs-index">

  
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		'tableOptions' => [

            'class' => 'table-rena table-rena3',
            'style' => 'font-size:9pt;width:1000px'

        ],
		'showFooter' => true,
        'footerRowOptions' => ['style' => 'font-weight:bold;text-decoration:underline;color:red;'],

        'columns' => [
            ['class' => 'kartik\grid\SerialColumn'],

             [

                'attribute'  => 'id_kassa',
                'value' =>'idKassa.name',
                'filter' => ArrayHelper::map(Kassa::find()->all(), 'id', 'name'),
                'filterWidgetOptions' =>[
                    'pluginOptions'=>['allowClear'=>true]
                ],
                'filterType' =>GridView::FILTER_SELECT2,
                'width' => '300px',
                'filterInputOptions' =>['placeholder'=>'Any type']
            ] ,
             [

                'attribute'  => 'id_type',
                'value' =>'idType.name',
                'filter' => ArrayHelper::map(TypeCosts::find()->all(), 'id', 'name'),
                'filterWidgetOptions' =>[
                    'pluginOptions'=>['allowClear'=>true]
                ],
                'filterType' =>GridView::FILTER_SELECT2,
                'width' => '300px',
                'filterInputOptions' =>['placeholder'=>'Any type']
            ] ,
			[
			'attribute'  => 'sum',
			'value' => 'getSum',
			 'footer' =>  round($searchModel->getSum($dataProvider->query),2)
			],
            
            'note:ntext',
                        [
                'attribute' => 'datetime',
                'label' => 'Gəbul tarixi',
                'format'=>'raw',
                'value' => 'datetime',
                'width' => '150px',

                'filter' =>DatePicker::widget([
                    //,

                    'model' => $searchModel,
                    'attribute' => 'date_start',
                    'value' => date('Y-m-d'),
                    //'options' => ['placeholder' => 'Select issue date ...'],
                    'type' => DatePicker::TYPE_RANGE,
                    'attribute2' => 'date_end',
                    'value2' => date('Y-m-d'),
                    'pluginOptions' => [
                        'format' => 'yyyy-mm-dd',
                        'autoClose' => true
                        // 'todayHighlight' => false
                    ]
                ]),

                // 'group'=>true,

            ],

           [
				'class' => 'kartik\grid\ActionColumn',
				'template' => '{delete}',
				'visibleButtons' => [
					'delete' => function($model) {
						return $model->id_type != 2 && $model->id_type != 3 ; // показывать только если status = 0
					}
				],
			],

        ],
    ]); ?>
</div>
