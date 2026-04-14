<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Credit */

$this->title = $model->id;

\yii\web\YiiAsset::register($this);
?>
<div class="container">
<h4>
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
   
</h4>

  

    <?= Html::button('<i class="glyphicon glyphicon-print"></i>  Müqavilə', ['class' => 'btn btn-success', 'onclick' => "printStatement($model->id)"]); ?>
	<?= Html::button('<i class="glyphicon glyphicon-print"></i>  Razılıq ərizəsi', ['class' => 'btn btn-warning', 'onclick' => "printAgreement($model->id)"]); ?>
	<?= Html::button('<i class="glyphicon glyphicon-print"></i>  Qızıl Aktı', ['class' => 'btn btn-info', 'onclick' => "printAkt($model->id)"]); ?>
</div>
