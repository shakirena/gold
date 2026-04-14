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
           Kreditin alan:
        </div>
        <div class="col-md-9">
           <?= \app\models\Client::find()->where(['id'=>$model->id_client])->one()->name;?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            Malın dəyəri:
        </div>
        <div class="col-md-9">
            <?= $model->sum;?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            İlkin mədaxil:
        </div>
        <div class="col-md-9">
            <?= $model->fee;?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            Kreditin məbləği:
        </div>
        <div class="col-md-9">
            <?= $model->debt;?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            Aylıq ödəniş:
        </div>
        <div class="col-md-9">
            <?= $model->month_payment ;?> Ay
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            Kreditin müddəti:
        </div>
        <div class="col-md-9">
            <?= $model->month ;?> Ay
        </div>
    </div>
	<div class="row">
        <div class="col-md-3">
            Komissiya:
        </div>
        <div class="col-md-9">
            <?= $model->commission ;?> AZN
        </div>
    </div>
	
	<div class="row">
        <div class="col-md-3">
            Zamin:
        </div>
        <div class="col-md-9">
           <?php if ($model->id_guarantor) echo  \app\models\Guarantor::find()->where(['id'=>$model->id_guarantor])->one()->name;?>
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
            <?= $model->date_constribution  ;?>
        </div>
    </div>
</h4>
<br><br>
    <table class="table-rena kv-grid-table table table-bordered  kv-table-wrap">
        <thead>
        <th>Tarix</th>
        <th>Aylıq ödəniş</th>
        <th>Qalıq borc</th>
        <th>Ödənilib</th>
		<th>Qeyd</th>
        </thead>
        <tbody>
        <?php
            $sum=0;
            $debt=$model->sum-$model->fee;
			$sum=$model->fee;
			$mn=$model->debt/$model->month_payment;
			$mn=intval($model->month-$mn);
			
			$dateAt = strtotime("-$mn MONTH", strtotime($model->date_constribution));
			$date = date('Y-m-d', $dateAt);
            foreach ($payment as $pay)
            {
                $sum=$pay->sum+ $sum;
                $debt=$debt-$pay->sum;
				 if ($pay->datetime>$date && $pay->sum<=$model->month_payment) $color="red";
				 else $color="";
				  echo
                    "
                        <tr>
                            <td style='background-color:$color'>$pay->datetime</td>
                            <td>$pay->sum</td>
                            <td>$debt</td>
                            <td>$sum</td>
							<td>$pay->note</td>
                        </tr>
                    ";
			$date = strtotime('+1 MONTH', strtotime($date));
           $date= date('Y-m-d', $date);
      
            }
        ?>
        </tbody>
    </table>
</div>
