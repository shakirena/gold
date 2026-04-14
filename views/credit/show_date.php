 <?php
  
use yii\helpers\Html;
use kartik\date\DatePicker;


  echo "  Növbəti ödəniş". DatePicker::widget([
					'name' => 'check_issue_date',
					'id' => 'date',
					
					'value' => date('Y-m-d',strtotime('-1 MONTH', strtotime( $date))),
					'options' => ['placeholder' => 'Select issue date ...','autoclose'=> true,],
					'type' => DatePicker::TYPE_INPUT,
					'pluginOptions' => [
						'format' => 'yyyy-mm-dd',
						'todayHighlight' => false,
						'autoclose' => true
					]
				]);
				
				
				echo Html::a('ok', "#", ['class' => 'btn btn-danger','onclick'=>"deleteMonthBtn($id)"]);
				
				
				
?>