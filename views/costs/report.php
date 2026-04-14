<?php

use yii\helpers\Html;
//use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
use kartik\dateRange\DateRangePicker;
use kartik\grid\GridView;
use app\models\Costs;
use app\models\TypeCosts;
use app\models\Sell;
use app\models\Kassa;
use app\models\Transfer;
use app\models\Client;
use app\models\Contractor;
use app\models\Dclient;
use app\models\Debt;
/* @var $this yii\web\View */
/* @var $searchModel app\models\ArrivalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Arrivals';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arrival-index">
    <table class="table-rena kv-grid-table table table-bordered  kv-table-wrap">
        <thead>
        <th>Tarix</th>
        <th>Mədaxil / məxaric adı</th> 
        <th>Qeyd</th>
		<th>Mədaxil məbləği</th>
		<th>Məxarici məbləği</th>
        <th>Yekün</th>
	
		
        </thead>
        <tbody>
        <?php echo "<tr><td colspan='9' class='danger'>Kassa: $kassa</td> </tr>"; ?>
        <?php
        $current=round($current,2);$sum=$current;//round($current->sum,2);
		$prixod = 0;
		$rasxod = 0;
							echo "<tr>
								<td>Текущая сумма</td>
								<td></td>	
								<td></td>
								<td></td>
								<td></td>
								<td>$current</td>
											
							</tr>";
				foreach($model as $move) {
				
					if ($move->id_type)
						{
							
							$type = TypeCosts::find()->where(["id" => $move->id_type])->one()->name;
							if ($move->id_type ==3)
								 if ($move->id_kassa != 1)
									{
											$sum = $sum + $move->sum;
											$prixod = $prixod + $move->sum;
											
											if ($move->fid) {
												$name = Client::find()->where(["id_client" => Dclient::find()->where(["id" =>$move->fid])->one()->id_client])->one()->fio;
											
											
											}
											echo "<tr>
											<td>$move->datetime</td>
											
											<td><a href='../sell/report1?number=$move[fid]'> $type ($move->fid) $move->datetime tarixdən</a></td>
											<td>$move->note</td>
											<td>$move->sum</td>
											<td></td>
											<td>$sum</td>
											</tr>";
									
									
									}
								else 
									{
										$sum = $sum - $move->sum;
										$prixod = $prixod - $move->sum;
											
											if ($move->fid) {
												$name = Client::find()->where(["id_client" => Dclient::find()->where(["id" =>$move->fid])->one()->id_client])->one()->fio;
											
											
											}
											echo "<tr>
											<td>$move->datetime</td>
											
											<td><a href='../sell/report1?number=$move[fid]'> $type ($move->fid) $move->datetime tarixdən</a></td>
											<td>$move->note</td>
											<td></td>
											<td>$move->sum</td>
											<td>$sum</td>
											</tr>";
									}
							else if ($move->sum>0)
								{
									$prixod = $prixod + $move->sum;
									$sum = $sum + $move->sum;
									if ($move->fid) {
										$name = Client::find()->where(["id_client" => Dclient::find()->where(["id" =>$move->fid])->one()->id_client])->one()->fio;
										$type= $type." ( $name )";
									
									}
									echo "<tr>
									<td>$move->datetime</td>
									<td>$type </td>	
									<td>$move->note</td>
									<td>$move->sum</td>
									<td></td>
									<td>$sum</td>
									</tr>";
								}
							else 
								{
									$sum = $sum + $move->sum;
									$move->sum = -$move->sum;
									$rasxod = $rasxod + $move->sum;
									
									if ($move->fid) {
										$name = Contractor::find()->where(["id" => Debt::find()->where(["id" =>$move->fid])->one()->id_contr])->one()->name;
										$type= $type." ( $name )";
									
									}
									echo "<tr>
									<td>$move->datetime</td>
									<td>$type </td>	
									<td>$move->note</td>
									<td></td>
									<td>$move->sum</td>
									<td>$sum</td>
									</tr>";
								
								
								
								}
						
						}
					else 
						{
							$sum = $sum + $move->sum;
							$type = TypeCosts::find()->where(["id" => $move->id_type])->one()->name;
							if ($move->sum>0)
								{
									$prixod = $prixod + $move->sum;
									$kassa = Kassa::find()->where(["id" => $move->from_kassa])->one()->name;
									echo "<tr>
									<td>$move->datetime</td>
									<td> $kassa -dan trensfer</td>	
									<td>$move->note</td>
									<td>$move->sum</td>
									<td></td>
									<td>$sum</td>
									</tr>";
								}
							else 
								{
									$kassa = Kassa::find()->where(["id" => $move->from_kassa])->one()->name;
									$move->sum = -$move->sum;
									$rasxod = $rasxod + $move->sum;
									echo "<tr>
									<td>$move->datetime</td>
									<td> $kassa -ya transfer</td>	
									<td>$move->note</td>
									<td></td>
									<td>$move->sum</td>
									<td>$sum</td>
									</tr>";
								}
							
						
						
						
						}
				
				}
				
					echo "<tr  class='danger'>
								<td>Итог</td>
								<td></td>	
								<td></td>
								<td>$prixod</td>
								<td>$rasxod</td>
								<td>$sum</td>
											
								</tr>";
		?>
        </tbody>
  </div>
