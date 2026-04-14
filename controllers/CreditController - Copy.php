<?php

namespace app\controllers;

use app\models\Payment;
use Yii;
use app\models\Credit;
use app\models\Credit1;
use app\models\Credit2;
use app\models\Client;
use app\models\CreditSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\CreditPaymentSearch;


use PhpOffice\PhpWord\TemplateProcessor;
/**
 * CreditController implements the CRUD actions for Credit model.
 */
class CreditController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Credit models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CreditSearch();
		//$searchModel->date_start = date('Y-m-d');//." 00:00:00";
		//$searchModel->date_end = date('Y-m-d');//." 23:59:59";
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Credit model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionViewCredit($id)
    {
        $payment=Payment::find()->where(['id_credit'=>$id])->all();
        return $this->render('view_credit', [
            'model' => $this->findModel($id),
            'payment'=>$payment
        ]);
    }
	public function actionViewCreditHistory($id)
    {
        $payment=Payment::find()->where(['id_credit'=>$id])->all();
        return $this->render('view_credit_history', [
            'model' => $this->findModel($id),
            'payment'=>$payment
        ]);
    }
    public function actionReceivedCredit($sum,$note,$id_credit)
    {
        $payment=new Payment();
        $payment->id_credit=$id_credit;
        $payment->sum=$sum;
        $payment->note=$note;
        $payment->datetime=date("Y:m:d H:i:s");
        $payment->save();
        $model=Credit::find()->where(["id"=>$id_credit])->one();
        $model->debt=$model->debt-$sum;
		 
        $dateAt = strtotime('+1 MONTH', strtotime( $model->date_constribution));
        $model->date_constribution = date('Y-m-d', $dateAt);
        $model->save();

    }

	public function actionDeletePayment($id)
    {
        $payment=Payment::find()->where(["id"=>$id])->one();
		$id_credit=$payment->id_credit;
        
        $model=Credit::find()->where(["id"=>$payment->id_credit])->one();
        $model->debt=$model->debt+ $payment->sum;
        $model->save();
		$payment->delete();
		
		    return $this->redirect(['view-credit','id'=>$id_credit]);

    }
    public function actionPrint($id)
    {
        return $this->renderAjax('print', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionStatement($id)
    {
        $client=Client::find()->where(["id"=>$this->findModel($id)->id_client])->one();
        return $this->renderAjax('statement', [
            'credit' => $this->findModel($id),
            'client'=>$client
        ]);
    }
    public function actionCheck($id)
    {
        $payment=Payment::find()->where(["id_credit"=>$id])->orderBy("datetime DESC")->one();
        return $this->renderAjax('check', [
            'client' => Client::find()->where(["id"=>Credit::find()->where(["id"=>$id])->one()->id_client])->one(),
            'payment'=>$payment
        ]);
    }
    public function actionPayment()
    {
        $searchModel = new CreditPaymentSearch();
	//	$searchModel->date_start = date('Y-m-d');//." 00:00:00";
		$searchModel->date_end = date('Y-m-d');//." 23:59:59";
		$searchModel->date_start1 = date('Y-m-d');//." 23:59:59";
		$searchModel->date_end1 = date('Y-m-d');//." 23:59:59";
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('payment', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	  public function actionReport()
    {
        $searchModel = new CreditPaymentSearch();
	//	$searchModel->date_start = date('Y-m-d');//." 00:00:00";
		
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	public function actionTest()
    {
	set_time_limit(0);$i=0;
       foreach (Credit::find()->all() as $credit)
	   {
		    $current=date("Y-m-d");
			$flag=0;
			//$dateAt=$credit->date_create;
			$payment=Payment::find()->where(["id_credit"=>$credit->id])->orderBy("id DESC")->one()->datetime;
			if($payment) {
			$dateAt = strtotime('+1 MONTH', strtotime($payment));
			$dateAt= date('Y-m-d', $dateAt);
			$flag=1;}
			$i++;
	
		   
		    
		
		   
       	echo $i."<br>";
	   if ($flag==1){
		$date_new=Credit1::find()->where(["id"=>$credit->id])->one()->date_constribution;
		if (!$date_new) $date_new=Credit2::find()->where(["id"=>$credit->id])->one()->date_constribution;
		$date_new=date("d",strtotime($date_new));
		
		$dateAt=date("Y-m",strtotime($dateAt));
	
		   $credit->date_constribution=$dateAt."-".$date_new;
		   echo $credit->date_constribution;
		   	//echo $credit->date_constribution."<br>";
	  $credit->save();
	  }
	 }
    
	}
	
	public function actionTest1()
    {
       foreach (Client::find()->all() as $client)
	   {
		 $credit=Credit::find()->where(["id_client"=>$client->id])->one();
		if ($credit->id) continue;
			else $client->delete();
	 
	 }
    
	}
    /**
     * Creates a new Credit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Credit();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->debt=$model->sum-$model->fee;
			$model->id_user=Yii::$app->user->identity->id_user;;
            $model->save();
			Yii::$app->session->remove('id_client');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

	public function actionCreateClient()
    {
        $model = new Client();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->set('id_client', $model->id);
            return $this->redirect(['create']);

        }

        return $this->renderAjax('..\client\create', [
            'model' => $model,
        ]);
    }
    /**
     * Updates an existing Credit model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
		
		Yii::$app->session->remove('id_client');
            return $this->redirect(['view', 'id' => $model->id]);
        }
		Yii::$app->session->set('id_client', $model->id_client);
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Credit model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
	
       
		$payment=Payment::find()->where(["id_credit"=> $id])->one();
		if ($payment->id) echo "Удаление запрещенно";
       
		else
		 $this->findModel($id)->delete();
		  return $this->redirect(['payment']);
    }

    /**
     * Finds the Credit model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Credit the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Credit::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
	
	public function actionSetPayment($sum,$fee,$month)
	{
		
		$payment=round(($sum-$fee)/$month,2);
		return $payment;
		
	}
	
	public function actionSetPaymentMonth($sum,$payment,$month)
	{
		
		$fee=round(($sum-$payment*$month),2);
		return $fee;
		
	}
}
