<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use app\models\Store;
use app\models\Kassa;
use app\models\Product;
use kartik\select2\Select2;

use app\models\TypeProduct;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SellSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sell-index row" xmlns="http://www.w3.org/1999/html">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="btn-group" style="width: 200px">
        <?= Select2::widget([
            'data' => ArrayHelper::map(Kassa::find()->all(), 'id', 'name'),
            'name' => 'kassa',
			//'value'=>Yii::$app->request->get("id"),
            'options' => [
                'placeholder' => 'Seçin',
			
                'id'=>'kassa',

            ]
        ]); ?>
    </div>
    <div class="btn-group">
        <?= DatePicker::widget([
            'name' => 'check_issue_date',
            'id' => 'date1',
            'value' => date('Y-m-d'),
            'options' => ['placeholder' => 'Select issue date ...'],
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => false
            ]
        ]); ?>
    </div>
    <div class="btn-group">
        <?= DatePicker::widget([
            'name' => 'check_issue_date',
            'id' => 'date2',
            'value' => date('Y-m-d'),
            'options' => ['placeholder' => 'Select issue date ...'],
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => false
            ]
        ]); ?>
    </div>





  


        <div class="btn-group">
            <?= Html::button('<i class="glyphicon glyphicon-ok"></i>  OK', ['class' => 'btn btn-success','onclick' =>"document.location.href='report?id='+$('#kassa').val()+'&date1='+$('#date1').val()+'&date2='+$('#date2').val()+'&type='+$('#type').val()"]); //?>

        </div>
<br><br>


       <div id="contentAjax">

    </div>


</div>


