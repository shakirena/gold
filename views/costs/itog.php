<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use kartik\date\DatePicker;
use app\models\Client;
use app\models\Contractor;
use app\models\Product;
use kartik\select2\Select2;

use app\models\TypeProduct;
/* @var $this yii\web\View */
/* @var $searchModel app\models\SellSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
<div class="sell-index col-xs-7" xmlns="http://www.w3.org/1999/html">

 <div class="btn-group">
 <?php
	if (!$date1) $date1=date('Y-m-d');
	if (!$date2) $date2=date('Y-m-d');
	?>
        <?= DatePicker::widget([
            'name' => 'check_issue_date',
            'id' => 'date1',
            'value' =>$date1 ,
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
            'value' => $date2,
            'options' => ['placeholder' => 'Select issue date ...'],
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format' => 'yyyy-mm-dd',
                'todayHighlight' => false
            ]
        ]); ?>
    </div>
	  <div class="btn-group">
            <?= Html::button('<i class="glyphicon glyphicon-ok"></i>  OK', ['class' => 'btn btn-success','onclick' =>"document.location.href='itog?date1='+$('#date1').val()+'&date2='+$('#date2').val()"]); //?>

        </div>
	<!--	</br></br>
		Mədaxil:  <input type="text" id="rasxod">  <?= Html::button('<i class="glyphicon glyphicon-ok"></i>  OK', ['class' => 'btn btn-success','onclick' =>"document.location.href='itog?date1='+$('#date1').val()+'&date2='+$('#date2').val()+'&prixod='+$('#rasxod').val()"]); //?>
		-->
		<br><br>
		
<b><?=$date1." - ".$date2?></br>	</b></br>
<?php

?>	



<div>Faiz ödənişi: <?=round($month,2)?> AZN</div>
---------------------------------------------------------<br/>
<div>Əsas borc məbləği ödənişi: <?= round($payment,2)?> AZN </div>
---------------------------------------------------------<br/>
<div>Gecikməyə görə ödəniş: <?= round($fine,2)?> AZN </div>
---------------------------------------------------------<br/>


<?php 
 $itog=round( $month + $payment + $fine,2);
 
?>
<div><b>Yekun:  <?= $itog;?> AZN</b></div>



</div>
</div>



