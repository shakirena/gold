<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Kassa;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
/* @var $this yii\web\View */
/* @var $model app\models\Finance */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="finance-form row" style="margin:10px">

	<div class="col-xs-6" style="padding-left:100px">
	Umumi medaxil: <?= $balance ?> AZN <br><br>
		<?php
			$sum = 0;
			
				
				foreach ($kassa as $model)
				{
				?>
				<div><b><?=$model->name?></b>: <?=$model->kassaSum($model->id)?> AZN</div><br>
				<?php
				}
				
				?>	
				
				

	
	</div>
</div>
