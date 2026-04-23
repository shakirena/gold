<?php

namespace app\controllers;

use app\models\Payment;
use Yii;
use app\models\Credit;
use app\models\Credit1;
use app\models\Credit2;
use app\models\Client;
use app\models\Costs;
use app\models\Fine;
use app\models\Products;
use app\models\ProductsHistory;
use app\models\ProductsSearch;
use app\models\CreditSearch;
use app\models\PaymentSearch;
use app\models\Month;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\CreditPaymentSearch;
use app\models\CreditPaymentZadSearch;
use app\models\Guarantor;
use yii\filters\AccessControl;
use PhpOffice\PhpWord\TemplateProcessor;
use app\models\UploadForm;
use yii\web\UploadedFile;
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
			
			'access' => [
                'class' => AccessControl::className(),
                'only' => ['index','create','ViewCredit','ViewCreditHistory','Statement','Payment','PaymentReport'],
                'rules' => [
                    [
                       
                        'allow' => true,
                        'roles' => ['@'],
                    ],
					
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
	
	public function actionTest()
	{
		set_time_limit(0);
		
		foreach(Costs::find()->where(['id_type'=>2])->all() as $costs)
		{
			$id = $costs->id;
			$c = Costs::find()->where(['sum'=>$costs->sum])->andWhere("$id!= id")->one();
			if ($c->id) echo $costs->id."<br>";
				
			/*$credit = Credit::find()->where(['sum' => -$costs->sum])->one();
			if($credit->id) continue;
			echo $costs->id;
			//$costs->delete();*/
		}
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
		$month = Month::find()->where(['id_credit'=>$id])->all();
		$fine = Fine::find()->where(['id_credit'=>$id])->all();
		$searchModel = new ProductsSearch();
		$searchModel->id_credit = $id;
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('view_credit', [
            'model' => $this->findModel($id),
            'payment'=>$payment,
			'month' => $month,
			'fine' => $fine,
			'dataProvider' => $dataProvider,
			'searchModel' => $searchModel
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
	
	
	public function actionScript()
	{
		foreach( Costs::find()->where(["id_type" => 3])->all() as $cost)
		{
			$paym = Payment::find()->where(["id" =>$cost->fid])->one();
			if ($paym->id && $paym->sum == $cost->sum ) 
			{
				continue;
				//echo $credit->id."-".$credit->sum."-".$cost->sum."<br>";
				
			}
			else 
			{
				
					$cost->delete();
			}				
			
		
		}
		/*
		foreach(Credit::find()->select("sum(sum) as sum, sum(debt) as debt, id")->groupBy("id")->all() as $credit)
		{
			
				$payment = Payment::find()->select("sum(sum) as sum")->groupBy("id_credit")->where(['id_credit' => $credit->id])->one();
				$op = round($credit->sum - $credit->debt,2);
				if ($op > $payment->sum )
				{
					$sum = $op  - $payment->sum;
					$payment=new Payment();
					$payment->id_credit=$credit->id;
					$payment->sum=$sum;
					$payment->note='';
					$payment->datetime=date("Y:m:d H:i:s");
			
					 if($payment->save())
					{
						$cost = new Costs();
						$cost->sum = $sum;
						$cost->id_type = 3;
						$cost->id_user = Yii::$app->user->identity->id_user;
						$cost->datetime = $payment->datetime;
						$cost->id_kassa = 1;
						$cost->fid = $payment->id;
						$cost->save();
						}
				}
				else if  ($op < $payment->sum)
				{
					
					$payment = Payment::find()->where("id_credit =$credit->id and sum>$op")->one();
					$payment->sum = $op;
					$payment->save();
				}
		}
				foreach(Credit::find()->select("sum(sum) as sum, sum(debt) as debt, id")->groupBy("id")->all() as $credit)
				{
					$payment = Payment::find()->select("sum(sum) as sum")->groupBy("id_credit")->where(['id_credit' => $credit->id])->one();
					$op = round($credit->sum - $credit->debt,2);
					if ($op != $payment->sum)
					{
						echo "id_credit:".$credit->id."sum:".$credit->sum." (".$credit->debt." : ".$op."-".$payment->sum."<br>";
					}
					
				}
			
		*/
		
		
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
		$model->month_payment = round($model->debt * $model->percant / 100, 2);

        if($model->save(false))
		{
				$cost = new Costs();
				$cost->sum = $sum;
				$cost->id_type = 3;
				$cost->id_user = Yii::$app->user->identity->id_user;
				$cost->datetime = $payment->datetime;
				$cost->id_kassa = 1;
				$cost->fid = $payment->id;
				$cost->save();
		}
		return $payment->id;
    }

	 public function actionPaymentMonth($sum,$note,$id_credit)
    {
        $payment=new Month();
        $payment->id_credit=$id_credit;
        $payment->sum=$sum;
        $payment->note=$note;
        $payment->date=date("Y:m:d H:i:s");
        $payment->save();

	   $model=Credit::find()->where(["id"=>$id_credit])->one();
		$model->recalculateNextPaymentDateFromMonth((float)$sum, 0);
		return $payment->id;
    }
public function actionShowDate($id,$date)
{

  return $this->renderAjax('show_date', [
            'id' => $id,
            'date'=>$date,

        ]);

}
	public function actionPaymentFine($sum,$note,$id_credit)
    {
        $payment=new Fine();
        $payment->id_credit=$id_credit;
        $payment->sum=$sum;
        $payment->note=$note;
        $payment->date=date("Y:m:d H:i:s");
        $payment->save();
		return $payment->id;
    }
	
	public function actionReturnProduct($id)
	{
		$product = Products::find()->where(['id' => $id])->one();
		$product->status = 0;
		$product->save();
		
		$history = new ProductsHistory();
		$history->id_product =  $id;
		$history->date = date("Y:m:d H:i:s");
		$history->id_user = Yii::$app->user->identity->id_user;
	
		$history->save();

		//print_r($history);
		
	}
	public function actionDeleteMonth($id)
	{
		$payment=Month::find()->where(["id"=>$id])->one();
		$deletedSum = (float)$payment->sum;
		$payment->delete();

		$model=Credit::find()->where(["id"=>$payment->id_credit])->one();
		$model->recalculateNextPaymentDateFromMonth(0, $deletedSum);
		return $this->redirect(['view-credit','id'=>$payment->id_credit]);
	}
	
	public function actionDeleteFine($id)
	{
		$payment=Fine::find()->where(["id"=>$id])->one();
		$payment ->delete();
	}
	public function actionDeletePayment($id)
    {
        $payment=Payment::find()->where(["id"=>$id])->one();
		$id_credit=$payment->id_credit;
        
		
		
		
        $model=Credit::find()->where(["id"=>$payment->id_credit])->one();
        $model->debt=$model->debt + $payment->sum;
		$model->month_payment = round($model->debt * $model->percant / 100, 2);
        if ($model->save(false))
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
	public function actionAkt($id)
    {
        $client=Client::find()->where(["id"=>$this->findModel($id)->id_client])->one();
		$products=Products::find()->where(["id_credit"=>$id])->all();
        return $this->renderAjax('akt', [
            'credit' => $this->findModel($id),
            'client'=>$client,
			'products' => $products
        ]);
    }
	
	public function actionAgreement($id)
    {
        $client=Client::find()->where(["id"=>$this->findModel($id)->id_client])->one();
        return $this->renderAjax('agreement', [
            'credit' => $this->findModel($id),
            'client'=>$client
        ]);
    }
    public function actionCheck($id, $payment,$month,$fine )
    {
	
	
        if ($payment) {$payment=Payment::find()->where(["id"=>$payment])->one(); $number = $payment->id; $date = $payment->datetime;}
	
		if ($month) {$month=Month::find()->where(["id"=>$month])->one(); $number = $month->id;  $date = $month->date;}	
			
		if ($fine) {$fine=Fine::find()->where(["id"=>$fine])->one(); $number = $fine->id;  $date = $fine->date; }

        return $this->renderAjax('check', [
            'client' => Client::find()->where(["id"=>Credit::find()->where(["id"=>$id])->one()->id_client])->one(),
			'credit'=>Credit::findOne(['id'=>$id]),
            'payment'=>$payment,
			'month' => $month,
			'fine' => $fine,
			'number' =>$number,
			'date' => $date
        ]);
    }
    public function actionPayment()
    {
        $searchModel = new CreditPaymentSearch();
	//	$searchModel->date_start = date('Y-m-d');//." 00:00:00";
		$searchModel->id_store = Yii::$app->user->identity->id_store;
		$searchModel->date_end = date('Y-m-d');//." 23:59:59";
		$searchModel->date_start1 = date('Y-m-d');//." 23:59:59";
		$searchModel->date_end1 = date('Y-m-d');//." 23:59:59";
		
		
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	
        return $this->render('payment', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
		
        ]);
    }
	
	 public function actionPaymentReport()
    {
        $searchModel = new PaymentSearch();
		$searchModel->date_start = date('Y-m-d');//." 00:00:00";
		$searchModel->date_end = date('Y-m-d');//." 23:59:59";
		
		
		if ( Yii::$app->user->identity->id_role!=1) $searchModel->id_store = Yii::$app->user->identity->id_store;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('payment_report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
	  public function actionReport()
    {
        $searchModel = new CreditPaymentSearch();
		
	//	$searchModel->date_start = date('Y-m-d');//." 00:00:00";
		$searchModel->id_store = Yii::$app->user->identity->id_store;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	
		$dataProvider->query->orderBy(["id" => "DESC"]);
        return $this->render('report-arxiv', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			
        ]);
    }
	public function actionReportActive()
    {
        $searchModel = new CreditPaymentZadSearch();
		
	//	$searchModel->date_start = date('Y-m-d');//." 00:00:00";
		$searchModel->id_store = Yii::$app->user->identity->id_store;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
	
		$dataProvider->query->orderBy(["id" => "DESC"]);
        return $this->render('report', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			
        ]);
    }
	/*public function actionTest()
	{
	set_time_limit(0);
	 foreach (Credit::find()->andWhere("id>659")->all() as $credit)
	   {
	   $date_new=Credit1::find()->where(["id"=>$credit->id])->one()->date_constribution;
		if ($date_new) {$credit->date_constribution= $date_new; $credit->save();}
		
	   }
	}*/
	
	/*
	public function actionTest()
    {
	set_time_limit(0);$i=0;
       foreach (Credit::find()->all() as $credit)
	   {
		    $current=date("Y-m-d");
			$flag=0;
			//$dateAt=$credit->date_create;
			$payment=Payment::find()->where(["id_credit"=>$credit->id])->orderBy("id ASC")->one();
			if($payment->datetime) {
			$mn=(int)(Payment::find()->select("sum(sum) as sum")->where(["id_credit"=>$credit->id])->one()->sum/$credit->month_payment);
			
			echo $mn;
			$dateAt = strtotime("+$mn MONTH", strtotime($payment->datetime));
			$dateAt= date('Y-m-d', $dateAt);
			$flag=1;}
			$i++;
	
		   
		    
		
		   
       	
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
    
	}*/
    /**
     * Creates a new Credit model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Credit();
		
		
		$searchModel = new ProductsSearch();
		
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere(['is', 'id_credit', null]);
	
		$model->id_store = Yii::$app->user->identity->id_store ;
        if ($model->load(Yii::$app->request->post())) {
            $model->date_constribution = $model->date_constribution_start;
            if ($model->save()) {
                $model->debt=$model->sum-$model->fee;
                $model->id_user=Yii::$app->user->identity->id_user;
                $post=Yii::$app->request->post("Credit");
                $model->date_create  = $post['date_create'] . " " . date("H:s:i");
                if ($model->save())
                {
                    $cost = new Costs();
                    $cost->sum = -$model->sum;
                    $cost->id_type = 2;
                    $cost->id_user = Yii::$app->user->identity->id_user;
                    $cost->datetime = $model->date_create;
                    $cost->id_kassa = 1;
                    $cost->fid = $model->id;
                    $cost->save();
                }
                $products=new Products();
                $products->updateAll(["id_credit"=>$model->id], ['is', 'id_credit', null]);
                Yii::$app->session->remove('id_client');

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
			'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
			
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
	
	/*public function actionProductCreate()
    {
        $model = new Products();
		$image=new UploadForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
			print_r(UploadedFile::getInstances($image, 'imageFiles'));
			if ($image->imageFiles = UploadedFile::getInstances($image, 'imageFiles')){
                if ($image->upload($model->id)) {
                  
                    $image_tratment=new \app\models\Image();
                    $image_tratment->id_tre=$model->id;
                    $image_tratment->path="uploads/".$model->id."/".$image->imageFiles[0]->name;
                    $thumbnFile="thumb/".$model->id."/".$image->imageFiles[0]->name;
					$image_tratment->thumb=$thumbnFile;
					Image::thumbnail($_SERVER['DOCUMENT_ROOT'] .'/web/'.$image_tratment->path, 50, 50)->save($_SERVER['DOCUMENT_ROOT'] .'/web/'.$thumbnFile, ['quality' => 80]);
                    $image_tratment->save();
                    // return;
                }}
			
				if (Yii::$app->request->isAjax) {
					Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
					return ['success' => true];
				}
				return $this->redirect(['index']);
			}
		}
	  if (Yii::$app->request->isAjax) {
			return $this->renderAjax('..\products\create', [
				'model' => $model,
				'image' => $image,
			]);
		}

		return $this->render('..\products\create', [
			'model' => $model,
			'image' => $image,
		]);
    }*/

	public function actionProductCreate()
    {
        $model = new Products();
		$image=new UploadForm();
		if ($model->load(Yii::$app->request->post())) {
			if ($model->save()) {
				if (Yii::$app->request->isAjax) {
					Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
					return ['success' => true];
				}
				return $this->redirect(['index']);
			}
		}
	  if (Yii::$app->request->isAjax) {
			return $this->renderAjax('..\products\create', [
				'model' => $model,
					'image' => $image,
			]);
		}

		return $this->render('..\products\create', [
			'model' => $model,
				'image' => $image,
		]);
    }

	public function actionCreateGuarantor()
    {
        $model = new Guarantor();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->set('id_guarantor', $model->id);
            return $this->redirect(['create']);

        }

        return $this->renderAjax('..\guarantor\create', [
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
		
		
		$searchModel = new ProductsSearch();
		
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		$dataProvider->query->andWhere(['=', 'id_credit',  $id]);
		
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
		
		Yii::$app->session->remove('id_client');
            return $this->redirect(['view', 'id' => $model->id]);
        }
		Yii::$app->session->set('id_client', $model->id_client);
        return $this->render('update', [
            'model' => $model,
			'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
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
		if ($payment) echo "Удаление запрещенно";
       
		else
		$this->findModel($id)->delete();
		$costs=Costs::find()->where(["fid"=>$id, 'id_type'=>2])->one();
		$costs ->delete();
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
