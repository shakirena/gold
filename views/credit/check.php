<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\assets\AppAsset;
AppAsset::register($this);
/* @var $this yii\web\View */
/* @var $model app\models\Credit */


\yii\web\YiiAsset::register($this);
$note = "";
if ($payment) { $paymentSum = $payment->sum;; $note = $note.$payment->note;} else $paymentSum  = 0;
if ($month) {$monthSum = $month->sum;$note = $note.$month->note;} else $monthSum = 0;
if ($fine) {$fineSum = $fine->sum;$note = $note.$fine->note;} else $fineSum = 0;

$sum_do=$credit->debt+$paymentSum;
?>
<div class="container" onafterprint="alert(2)">
<h4>
    
	<style>
h2 {text-align: center;}

</style>
	<h2>LOMBARD</h2>
    <table width="100%" >
        <tr  class="border-bottom" >
            <td colspan="2" align="center">Qebz №: <?=$number?></td>

        </tr>
        <tr class="border-bottom">

            <td width="20%">Tarix:</td>
            <td><?=$date?></td>
        </tr>
        <tr class="border-bottom">

            <td>Müştəri: &nbsp &nbsp </td>
            <td><?=$client->name?></td>
        </tr>
		 <tr class="border-bottom">

            <td>Faiz məbləğinin ödənişi:</td>
            <td>&nbsp &nbsp<?=$monthSum?></td>
        </tr>
		<tr class="border-bottom">

            <td>Əvvəlki əsas borc qalığı:</td>
            <td>&nbsp &nbsp<?=$sum_do?></td>
        </tr>
        <tr class="border-bottom">

            <td>Əsas borc ödənişi:</td>
            <td>&nbsp &nbsp<?=$paymentSum?></td>
        </tr>
		<tr class="border-bottom">

            <td>Ümumi qalıq:</td>
            <td>&nbsp &nbsp<?=$credit->debt?></td>
        </tr>
			<tr class="border-bottom">

            <td>Gecikməyə görə ödəniş:</td>
            <td>&nbsp &nbsp<?=$fineSum?></td>
        </tr>
        <tr class="border-bottom">

            <td>Qeyd:</td>
            <td><?=$note?></td>
        </tr>
    </table>
	</br>
	<p>Təşəkkür edirik!</p> 
 
		

<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-phone" viewBox="0 0 16 16">
  <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
  <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
</svg> 055 579 22 63 </br>

	<h2>LOMBARD</h2>
    <table width="100%" >
        <tr  class="border-bottom" >
            <td colspan="2" align="center">Qebz №: <?=$number?></td>

        </tr>
        <tr class="border-bottom">

            <td width="20%">Tarix:</td>
            <td><?=$date?></td>
        </tr>
        <tr class="border-bottom">

            <td>Müştəri: &nbsp &nbsp </td>
            <td><?=$client->name?></td>
        </tr>
		 <tr class="border-bottom">

            <td>Faiz məbləğinin ödənişi:</td>
            <td>&nbsp &nbsp<?=$monthSum?></td>
        </tr>
		<tr class="border-bottom">

            <td>Əvvəlki əsas borc qalığı:</td>
            <td>&nbsp &nbsp<?=$sum_do?></td>
        </tr>
        <tr class="border-bottom">

            <td>Əsas borc ödənişi:</td>
            <td>&nbsp &nbsp<?=$paymentSum?></td>
        </tr>
		<tr class="border-bottom">

            <td>Ümumi qalıq:</td>
            <td>&nbsp &nbsp<?=$credit->debt?></td>
        </tr>
			<tr class="border-bottom">

            <td>Gecikməyə görə ödəniş:</td>
            <td>&nbsp &nbsp<?=$fineSum?></td>
        </tr>
        <tr class="border-bottom">

            <td>Qeyd:</td>
            <td><?=$note?></td>
        </tr>
    </table>
	</br>
	<p>Təşəkkür edirik!</p> 
	
 
		

<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-phone" viewBox="0 0 16 16">
  <path d="M11 1a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2z"/>
  <path d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2"/>
</svg> 055 579 22 63 </br>

			
	


  
</div>
<?php
$script = <<< JS

$(document).ready(function () {
window.print();



});
JS;
$this->registerJs($script);