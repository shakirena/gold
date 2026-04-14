<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\date\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel app\models\MonthSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>
<div class="month-index">

    <h1>Ayliq faiz</h1>

   

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
		 'tableOptions' => [

            'class' => 'table-rena table-rena2',
            'style' => 'font-size:9pt'

        ],
		'showFooter' => true,
        'footerRowOptions' => ['style' => 'font-weight:bold;text-decoration:underline;color:red;'],
       
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

             [
                'attribute' => 'date',
                'format'=>'raw',
                'width' => '250px',

                'filter' =>DatePicker::widget([
                

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
            'nameCredit',
			
			[   
				'label' => 'Girovun nomrəsi',
                'value' => 'creditLink',
				     'format'=>'raw',
              
            ],
			[
				'attribute' => 'sum',
				'footer' =>  round($searchModel->getSum($dataProvider->query),2)
			],
				
            'note',

          //  ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
