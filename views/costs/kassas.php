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

<div style="padding:50px" xmlns="http://www.w3.org/1999/html">
<?php
	$sum = 0;
	foreach ($kassa as $model)
	{
		$sum = $sum + $model->sum;
	?>
		<div><?=$model->note?>: <?=round($model->sum,2)?> AZN</div>
		---------------------------------------------------------<br/>
		<?php }
		?>	

<div><b>Yekun:  <?= round($sum,2)?> AZN</b></div>


</div>





