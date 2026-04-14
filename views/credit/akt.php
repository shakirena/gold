<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\assets\AppAsset;
AppAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Credit */


\yii\web\YiiAsset::register($this);
?>
<div class="container" onafterprint="myFunction()">
<h4 style="text-align: right;"><b><?=$credit->number?></b>№ li Girov biletinə əlavə</h4>



<h3><center> LOMBARD-ın girov qoyulmuş qızıl-zinət <br> əşyalarının Qiymətləndirmə və təhvil-təslim</center></h3>
<h2><b><center> AKTI</center></b></h2>


<table border="1" cellpadding="6" cellspacing="0" style="width:100%">
  <tr>
    <th>  Məmulatın adı və təsviri</th>
    <th>Əyarı</th>
	<th>Ümumi çəkisi(qr)</th>
	<th>Çıxar çəkisi(qr)</th>
	<th>Xalis  çəkisi(qr)</th>
	<th>Məmulatın qiyməti<br> (AZN ilə)</th>
  </tr>
  
 
<?php
$sum_w =0; $sum_net = 0; 
		foreach ($products as $product)
		{
		$sum_w = $sum_w + $product->weight;
		$sum_net = $sum_net + $product->net_weight;
			echo "
				<tr>
					<td>$product->name</td>
					<td></td>
					<td>$product->weight</td>
					<td></td>
					<td>$product->net_weight</td>
					<td></td>
				</tr>
			";
		
		}
		
		echo "
				<tr>
					<td>Cəmi</td>
					<td></td>
					<td>$sum_w</td>
					<td></td>
					<td>$sum_net </td>
					<td></td>
				</tr>";
?>	

</table>
<br><br>
<p>
<b>Təhvil alma:</b> </br>
İştirakçı qiymətləndirici:_____________________/_________</br>
Müştəri təhvil verdi: <b><?=$client->name?></b>/_________</br>
Anbardar təhvil aldı: <b>Ülkər Ramazanova</b> /_________</br>
</p>
<br><br>
<p>
<b>Təhvil vermə:</b>
İştirakçı qiymətləndirici: _____________________/_________</br>
Müştəriyə təhvil verdı:_____________________/_________</br>
Müştəri təhvil aldı:_____________________/_________</br>
</br>
</p>
<b><?=$credit->date_create?></b>
</div>
<?php
$script = <<< JS

$(document).ready(function () {
window.print();

});

JS;
$this->registerJs($script);