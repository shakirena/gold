<?php

namespace app\models;
use yii\helpers\Html;
use app\models\Store;
use app\models\Guarantor;
use Yii;

/**
 * This is the model class for table "credit".
 *
 * @property int $id
 * @property int $id_client
 * @property string $product_name
 * @property double $sum
 * @property double $fee
 * @property int $month
 * @property double $month_payment
 * @property string $date_constribution
 * @property string $date_create
 * @property double $debt
 *
 * @property Client $client
 * @property Payment[] $payments
 */
class Credit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_client', 'product_name', 'sum', 'month_payment', 'date_constribution', 'date_create','id_store','number'], 'required'],
            [['id_client', 'month','id_user'], 'integer'],
            [['sum', 'fee', 'month_payment', 'debt','commission','percant'], 'number'],
            [['date_constribution', 'date_constribution_start', 'date_create','guarantor','id_guarantor'], 'safe'],
            [['product_name'], 'string', 'max' => 100],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['id_client' => 'id']],
			[['id_store'], 'exist', 'skipOnError' => true, 'targetClass' => Store::className(), 'targetAttribute' => ['id_store' => 'id']],			
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['id_user' => 'id_user']],
		
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_client' => 'Müştəri',
            'product_name' => 'Malın adı',
            'sum' => 'Borc məbləği',
            'fee' => 'İlkin mədaxil',
            'month' => 'Ay',
            'month_payment' => 'Aylıq ödəniş',
            'date_constribution' => 'Ödəniş tarixi',
            'date_constribution_start' => 'İlk ödəniş tarixi',
            'date_create' => 'Tarix',
            'debt' => 'Qalıq',
			'id_user' => 'Satıcı',
			'id_guarantor' => 'Zamin',
			'id_store' => 'Sklad',
			'commission' => 'Girovun dəyəri',
			'percant' => 'Faiz',
			'number' => 'Girovun nomrəsi'
			
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'id_client']);
    }
	public function getIdUser()
    {
        return $this->hasOne(Users::className(), ['id_user' => 'id_user']);
    }
	
	public function getIdStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'id_store']);
    }
	
	
    public function getDateConstribution()
    {
       $current=date("Y-m-d");
       $dateAt=$this->date_constribution;
       foreach (Payment::find()->where(["id_credit"=>$this->id])->all() as $payment) {
		   while ($payment->sum>=$this->month_payment ) {
		   
           $dateAt = strtotime('+1 MONTH', strtotime($dateAt));
           $dateAt= date('Y-m-d', $dateAt);
		   $payment->sum=$payment->sum-$this->month_payment;
		   }
		   
       }
	   

        $diff = strtotime($dateAt) - strtotime($current);
        if ($diff<0) return "<div style='background-color:red'>$dateAt</div>";
        else
        {
            $diff=$diff/(60*60*24);
            if ($diff<=3) return "<div style='background-color:yellow'>$dateAt</div>";
        }


        return $dateAt ;
    }
	
	public function getDateConstributionStatus()
	{
		$current = date("Y-m-d");
		$diff = (strtotime($this->date_constribution) - strtotime($current)) / (60 * 60 * 24);
		if ($diff < 0) {
			return 'overdue';
		}
		if ($diff <= 3) {
			return 'soon';
		}
		return 'ok';
	}


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['id_credit' => 'id']);
    }
	
	
	public function getPayment()
    {
		return $this->sum - $this->debt;
    }
	
	public function getDelete()
	{
		$payment = Payment::find()->where(["id_credit" => $this->id])->one();
		if ($payment !== null) {
			return "-";
		}
		return Html::a('<i class="glyphicon glyphicon-remove"></i> Silmek', ["delete", "id" => $this->id], ['class' => 'btn btn-danger']);
	}
	 public function getSumCredit($model,$column){
        $sum=0;
		if ($model->where)
		 $query =  Credit::find()->select("sum(sum) as sum,sum(fee) as fee,sum(debt) as debt, sum(commission) as commission")
            ->joinWith('client')
           // ->andWhere('debt>0')
			->andWhere($model->where)
			->one();
			else 
			 $query =  Credit::find()->select("sum(sum) as sum,sum(fee) as fee,sum(debt) as debt, sum(commission) as commission")
            ->joinWith('client')
           // ->andWhere('debt>0')
			
			->one();
		if ($column=="payment")
		{
			return $query->sum- $query->debt;
		}
		else 
		{
       


        return $query->$column;
		}
    }


    /**
     * Recalculate next payment date based on total Month payments from the original start date.
     * Shifts date_constribution by +1 month for each full month_payment covered by Month records.
     */
    public function recalculateNextPaymentDateFromMonth()
    {
        $startDate = $this->date_constribution_start;
        if (!$startDate || $this->month_payment <= 0) {
            return;
        }

        $totalPaid = (float) Month::find()
            ->where(['id_credit' => $this->id])
            ->sum('sum');

        $monthsCovered = 0;
        $remaining = $totalPaid;
        while ($remaining >= $this->month_payment) {
            $monthsCovered++;
            $remaining -= $this->month_payment;
        }

        $dateAt = $startDate;
        for ($i = 0; $i < $monthsCovered; $i++) {
            $dateAt = date('Y-m-d', strtotime('+1 MONTH', strtotime($dateAt)));
        }

        $this->date_constribution = $dateAt;
        $this->save(false);
    }

    /**
     * Recalculate next payment date based on total payments from the original start date.
     * Shifts date_constribution by +1 month for each full month_payment covered.
     */
    public function recalculateNextPaymentDate()
    {
        $startDate = $this->date_constribution_start;
        if (!$startDate || $this->month_payment <= 0) {
            return;
        }

        $totalPaid = (float) Payment::find()
            ->where(['id_credit' => $this->id])
            ->sum('sum');

        $monthsCovered = 0;
        $remaining = (float) $totalPaid;
        while ($remaining >= $this->month_payment) {
            $monthsCovered++;
            $remaining -= $this->month_payment;
        }

        $dateAt = $startDate;
        for ($i = 0; $i < $monthsCovered; $i++) {
            $dateAt = date('Y-m-d', strtotime('+1 MONTH', strtotime($dateAt)));
        }

        $this->date_constribution = $dateAt;
        $this->save(false);
    }

    /**
     * Recalculate debt based on total payments.
     */
    public function recalculateDebt()
    {
        $totalPaid = (float) Payment::find()
            ->where(['id_credit' => $this->id])
            ->sum('sum');

        $this->debt = round($this->sum - $totalPaid, 2);

        if ($this->debt < 0) {
            $this->debt = 0;
        }

        $this->save(false);
    }
}
