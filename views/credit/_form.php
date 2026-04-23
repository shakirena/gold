<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Client;
use app\models\Store;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\dynagrid\DynaGrid;
use kartik\date\DatePicker;
use kartik\grid\GridView;
use app\models\Guarantor;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Credit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="credit-form">

	 <?php
    Modal::begin([
        'header' => '<h2>Yeni müştəri</h2>',

        'size' => 'modal-lg',
        'options' => [
            'id' => 'object-create',
            'tabindex' => true,
        ],
    ]);

    echo '<div id="modalContent"></div>';

    Modal::end();
    ?>
	<?php
    Modal::begin([
        'header' => '<h2>Yeni mal</h2>',

        'size' => 'modal-sm',
        'options' => [
            'id' => 'product-create',
            'tabindex' => true,
        ],
    ]);

    echo '<div id="modalProduct"></div>';

    Modal::end();
    ?>
    <?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-md-6">
		  <div class="row">
			<div class="col-md-7">
				<?php
				$model->id_client= Yii::$app->session->get('id_client');


				echo $form->field($model, 'id_client')->widget(Select2::className(),[
					'data' =>  ArrayHelper::map(Client::find()->all(), 'id', 'name'),
					'options' => [
						'placeholder' => 'Seçin',


					],


				]);

				?>

			</div>
			<div class="col-md-5">
				<?= Html::button('<i class="glyphicon glyphicon-plus"></i>Əlavə et', ['value' => Url::to(['create-client']), 'class' => 'btn btn-danger', 'id' => 'client']) ?>
			</div>
		</div>

		<?= $form->field($model, 'product_name')->textInput(['maxlength' => true])->label("Qeyd")?>
		<?= $form->field($model, 'number')->textInput(['maxlength' => true])?>
		<?php Pjax::begin(['id' => 'dynagrid-pjax']); ?>
		<?= DynaGrid::widget([
			'storage'=>DynaGrid::TYPE_COOKIE,
			'theme'=>'panel-danger',
			'gridOptions'=>[
				'dataProvider'=>$dataProvider,
				'filterModel'=>$searchModel,
			
				 'toolbar' =>  [
					['content'=>
						Html::button('<i class="glyphicon glyphicon-plus"></i>', ['value' => Url::to(['product-create']),'type'=>'button', 'title'=>'Əlavə et', 'class'=>'btn btn-danger', 'id'=>'addProduct']) 
					],
					
				],
			],
			'options'=>['id'=>'dynagrid-1', ], // a unique identifier is important
			'columns' => [
				['class' => 'kartik\grid\SerialColumn'],
				'name',
				'weight',
				'net_weight',
				[
					'label' => 'Şəkil',
					'value' => function ($model, $index, $widget) {
                        return  0;//$form->field($image[], 'imageFiles[]')->fileInput(['multiple' => true, 'accept' => 'image/*']); 
                        },
					
				],
				[
					'class' => 'kartik\grid\ActionColumn',
					'template' => '{delete}',
					'buttons' => [
					'delete' => function ($url, $model) {
						return Html::a('<span class="glyphicon glyphicon-trash"></span>', "../products/delete?id=$model->id", [
							'title' => Yii::t('app', 'Delete'),
							'data-method' => false, // отключаем стандартное Yii удаление
							'data-pjax' => '0', // отключаем pjax для этой ссылки
							'class' => 'delete-button' // добавляем наш класс
						]);
						},
					],
				],
        ],
    ]); ?>
	<?php Pjax::end(); ?>
		<?= $form->field($model, 'commission')->label("Girovun dəyəri")->textInput() ?>
		<?php
			if ($model->sum > 0 ) echo "<b>Borc məbləği:</b> ".$model->sum;
			else echo $form->field($model, 'sum')->label("Borc məbləği")->textInput(['id' =>'commission' ,'onchange'=>'changeSum()']) ?>
		<?php $model->percant = 10;?>
		<?= $form->field($model, 'percant')->label(" Faiz %")->textInput(['id' =>'percant' ,'onchange'=>'changeSum()']) ?>
		<?= $form->field($model, 'month_payment')->textInput(['onchange'=>'paymentPlanMonth()','id'=>'month_payment','readonly' =>true]) ?>
	
		
		</div>
		<div class="col-md-6">
		<?php
			$model->percant = 10;
			if (!$model->date_constribution_start) $model->date_constribution_start = date('Y-m-d');
		?>


			<?= $form->field($model, 'date_constribution_start')->widget(DatePicker::className(),[
				'name' => 'check_issue_date',
				'id' => 'date',

				'options' => ['placeholder' => 'Select payment date ...'],
				'type' => DatePicker::TYPE_INPUT,
				'pluginOptions' => [
					'format' => 'yyyy-mm-dd',
					'todayHighlight' => false,
					'autoclose'=>true

				]
			]) ?>
			
			 
		
					<?= $form->field($model, 'id_guarantor')->textInput(['maxlength' => true]) ?>
					

			<?php $model->id_store = Yii::$app->user->identity->id_store ?>
			<?php  $model->date_create= date('Y-m-d');
				echo $form->field($model, 'date_create')->widget(DatePicker::className(),[
				'name' => 'check_issue_date',
				'id' => 'date',

				'options' => ['placeholder' => 'Select issue date ...'],
				'type' => DatePicker::TYPE_INPUT,
				'pluginOptions' => [
					'format' => 'yyyy-mm-dd',
					'todayHighlight' => false,
					'autoclose'=>true
					
				]
			]) ?>

		 

			<div class="form-group">
				<?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
			</div>
		</div>
    <?php ActiveForm::end(); ?>

</div>
</div>
