<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $model app\models\Credit */

$this->title = $model->id;

\yii\web\YiiAsset::register($this);
if ( $model->debt>0) $date_constribution= $model->date_constribution  ;
else $date_constribution="-----";
?>
<div class="container">
<h4>
 <?php
    Modal::begin([
    

        'size' => 'modal-sm',
        'options' => [
            'id' => 'object-create',
            'tabindex' => true,
        ],
    ]);

    echo '<div id="modalContent">
	
	</div>';

    Modal::end();
    ?>
<div class="row">
	 <div class="col-md-7">
			<div class="row">
				<div class="col-md-3">
				   Girovun nomrəsi:
				</div>
				<div class="col-md-9">
				  <b><?= $model->number;?></b> 
			    </div>
			</div>
			<div class="row">
				<div class="col-md-3">
				   Müştəri:
				</div>
				<div class="col-md-9">
				   <?= \app\models\Client::find()->where(['id'=>$model->id_client])->one()->name;?>
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					 Borc məbləği:
				</div>
				<div class="col-md-9">
					<?= $model->sum;?> AZN
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					 Qalıq borc:
				</div>
				<div class="col-md-9">
					<?= $model->debt;?> AZN
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					Girovun dəyəri:
				</div>
				<div class="col-md-9">
					<?= $model->commission;?> AZN
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
				   Faiz:
				</div>
				<div class="col-md-9">
					<?= $model->percant ;?> %
				</div>
			</div>
			
			
			<div class="row">
				<div class="col-md-3">
					Ayliq faiz məbləği:
				</div>
				<div class="col-md-9">
					 <?= $model->month_payment ;?> 
				</div>
			</div>
			<div class="row">
				<div class="col-md-3">
					Sənədin tarixi:
				</div>
				<div class="col-md-9">
					<?= $model->date_create ;?>
				</div>
			</div>
			  <div class="row">
				<div class="col-md-3">
				   Ödəniş tarixi:
				</div>
				<div class="col-md-9">
					<?= $model->getDateConstribution();?>
				</div>
			</div>
	 </div>
	  <div class="col-md-5">
	  <?php Pjax::begin(['id' => 'dynagrid-pjax']); ?>
		<?= GridView::widget([
			'dataProvider' => $dataProvider,
			'filterModel' => $searchModel,
			'tableOptions' => [

            'class' => 'table-rena table-rena3',
            'style' => 'font-size:9pt'

			],
			'rowOptions' => function($model, $key, $index, $grid) {
					if ($model->status) {
						return ['style' => 'background-color:#d4edda']; // светло-зелёный
					} else {
						return ['style' => 'background-color:#f8d7da']; // светло-красный
					}
				
				},
			'columns' => [
				['class' => 'kartik\grid\SerialColumn'],
				
				'name',
				'weight',
				'net_weight',
				[
					'class' => 'kartik\grid\ActionColumn',
					'template' => '{delete}',
					'buttons' => [
					'delete' => function ($url, $model) {
					if ($model->status)
						return Html::a('<span class="glyphicon glyphicon-trash"></span>', "return-product?id=$model->id", [
							'title' => Yii::t('app', 'Delete'),
							'data-method' => false, // отключаем стандартное Yii удаление
							'data-pjax' => '0', // отключаем pjax для этой ссылки
							'class' => 'delete-button' // добавляем наш класс
						]);
						return '-';
						},
					],
				],
        ],
		]);
		
		?>
		<?php Pjax::end(); ?>
	  </div>
</div>

</h4>
<table class='table-play'>
	<tr>
		<td><b>Ayliq faiz məbləği ödənişi: </b> </td>
		<td><?=Html::input("text",'month_payment',0,[ 'size' => '10','id'=>'month_payment'])?></td>
		<td></td>
	</tr>
	<tr>
		<td><b>Gecikməyə görə ödəniş: </b></td>
		<td><?=Html::input("text",'sum',0,[ 'size' => '10','id'=>'fine'])?></td>
		<td></td>
	</tr>
	<tr>
		<td><b>Əsas borc məbləği ödənişi: </b></td>
		<td><?=Html::input("text",'sum',0,[ 'size' => '10','id'=>'sum'])?></td>
		<td></td>
	</tr>
	<tr>
		<td><b>Qeyd: </b></td>
		<td><?=Html::textarea('note',"",[ 'size' => '10','id'=>'note'])?></td>
		<td></td>
	</tr>
</table>
  
    <?= Html::button('<i class="glyphicon glyphicon-ok"></i>  ok', ['class' => 'btn btn-success', 'onclick' => "receivedCredit($model->id)"]); ?>
<br><br>
    <?= Html::button('<i class="glyphicon glyphicon-print"></i>  Print plan', ['class' => 'btn btn-info', 'onclick' => "printCredit($model->id)"]); ?>
<br><br>
<div class="row">
        <div class="col-md-4">
		<b>Əsas Borc məbləği</b>
			  <table class="table-rena kv-grid-table table table-bordered  kv-table-wrap">
				<thead>
					<th>Tarix</th>
					<th>Ödənilib</th>
					<th>Qalıq borc</th>
					<th>Cəmi ödənilib</th>
					<th>Silmek</th>
				</thead>
				<tbody>
				<?php
					$sum=0;
					$debt=$model->sum-$model->fee;
					$sum=$model->fee;
					if ($model->month_payment) $mn=$model->debt/$model->month_payment; else $mn = 0 ;
					$mn=intval($model->month-$mn);
					

					$dateAt = strtotime("-$mn MONTH", strtotime($model->date_constribution));
					$date = date('Y-m-d', $dateAt);
					foreach ($payment as $pay)
					{
						$sum=$pay->sum+ $sum;
						$debt=$debt-$pay->sum;
						
						
						 
							$date = $date." 23:59:59";
						
						/* if ($pay->datetime>$date && $pay->sum<=$model->month_payment) $color="red";
						 else $color="";style='background-color:$color'*/
						echo
							"
								<tr>
									<td >$pay->datetime</td>
									<td>$pay->sum</td>
									<td>$debt</td>
									<td>$sum</td>
									 <td>".Html::a('<i class="glyphicon glyphicon-remove"></i>', ["delete-payment","id"=>$pay->id], ['class' => 'btn btn-danger'])."</td>
						
								</tr>
							";
					
						$date = strtotime("+1 MONTH", strtotime($date));
						$date = date('Y-m-d', $date);
					}
				?>
				</tbody>
			</table>
        </div>
        <div class="col-md-4">
			<b>Ayliq faiz ödənişləri</b>
			<table class="table-rena kv-grid-table table table-bordered  kv-table-wrap">
				<thead>
					<th>Tarix</th>
					<th>Ödənilib</th>
					<th>Cəmi ödənilib</th>
					<th>Silmek</th>
				</thead>
				<tbody>
				<?php
				$sum_mn = 0;
					foreach ($month as $mn)
					{
						$sum_mn = $sum_mn + $mn->sum ;
						echo
							"
								<tr>
									<td>$mn->date</td>
									<td>$mn->sum</td>
									<td>$sum_mn</td>
									 <td>". Html::button('<i class="glyphicon glyphicon-remove"></i>', ['onclick' => "deletMonth($mn->id,'$model->date_constribution')", 'class' => 'btn btn-danger'])."</td>
						
								</tr>
							";
					}
				?>
				</tbody>
			</table>
        </div>
		 <div class="col-md-4">
			<b>Gecikməyə görə ödənişlər</b>
			<table class="table-rena kv-grid-table table table-bordered  kv-table-wrap">
				<thead>
					<th>Tarix</th>
					<th>Ödənilib</th>
					<th>Cəmi ödənilib</th>
					<th>Silmek</th>
				</thead>
				<tbody>
				<?php
				$sum_fn = 0;
					foreach ($fine as $fn)
					{
						$sum_fn = $sum_fn + $fn->sum ;
						echo
							"
								<tr>
									<td>$fn->date</td>
									<td>$fn->sum</td>
									<td>$sum_fn</td>
									 <td>".Html::a('<i class="glyphicon glyphicon-remove"></i>', ["delete-fine","id"=>$fn->id], ['class' => 'btn btn-danger'])."</td>
						
								</tr>
							";
					}
				?>
				</tbody>
			</table>
        </div>
    </div>

  
</div>
