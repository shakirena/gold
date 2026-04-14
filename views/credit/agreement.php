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
<h5 style="text-align: right;"><b>LOMBARD-ın <br>
 Direktoru  Ülkər Ramazanovaya   </b><br>
  <?=$client->adress?> <br>
  ünvanında yaşayan  <br>
  <?=$client->name?>

 </h5>
 
<h3><center><b>Razılıq  ərizəsi</b></center></h3>
<div>
	<p>Mən, <b><?=$client->name?></b>    bu  <b>RAZILIQ ƏRİZƏMİ LOMBARD</b>-a verirəm, o məqsədlə ki, həqiqətən  
	<b><?=$credit->date_create?></b> tarixli <b><?=$credit->number?></b>  saylı  müqavilə  əsasında <b><?= $credit->sum ?> AZN </b> məbləğində  ayliq <b><?= $credit->month_payment ?> AZN 
	<?= $credit->percant ?> %  </b> faiz ödənilməsi şərtilə götürdüyüm kredit borc öhdəliyimi  10 (on) gün ərzində yerinə yetirməsəm, yəni ödəniş etməsəm, gecikdirsəm, və ya tam ödəniş etməsəm, əlaqəyə çıxmasam və ya gəlməkdən imtina etsəm, mənim iştirakım olmadan borcumun ödənilməsi məqsədi ilə girova verdiyim qızıl-zinət əşyalarını  LOMBARD-ın öz mülkiyyətinə götürmək səlahiyyəti vardır.
	</p>
	<p>
	Həmçinin bildirirəm ki, girov qoyduğum qızıl-zinət əşyaları şəxsən özümə məxsusdur, heç kimin iddiası ola bilməz, faktiki ünvanımı və ya telefon nömrələrimi dəyişsəm bu barədə ən geci  3 (üç) gün ərzində    <b>LOMBARD</b>-a yazılı və ya poçtla xəbərdarlıq etməyi öhdəmə  götürürəm.
	</p>
	<p>
	 Həmçinin öhdəmə götürürəm ki, öhdəlik üzrə faizləri və əsas məbləği <b><?= $credit->sum ?> AZN ödəyəcəyəm.<br>
	 Ərizəni şəxsən oxudum, mənim sözlərimlə yazılıb, tam anladım, razıyam, heç bir əlavəm yoxdur  və imzamla təsdiq edirəm. İmza edərkən heç bir kənar təsir altında olmamışam. 
	</p>
	
	Imza: __________/
</div>


<?php
$script = <<< JS

$(document).ready(function () {
window.print();

});

JS;
$this->registerJs($script);