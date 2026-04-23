<?php

namespace app\models;

use Yii;
use yii\bootstrap\Html;
/**
 * This is the model class for table "payment".
 *
 * @property int $id
 * @property int $id_credit
 * @property double $sum
 * @property string $datetime
 * @property string $note
 *
 * @property Credit $credit
 */
class Payment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_credit', 'sum', 'datetime'], 'required'],
            [['id_credit'], 'integer'],
            [['sum'], 'number'],
            [['datetime'], 'safe'],
            [['note'], 'string'],
            [['id_credit'], 'exist', 'skipOnError' => true, 'targetClass' => Credit::className(), 'targetAttribute' => ['id_credit' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_credit' => 'Id Credit',
            'sum' => 'Sum',
            'datetime' => 'Datetime',
            'note' => 'Note',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCredit()
    {
        return $this->hasOne(Credit::className(), ['id' => 'id_credit']);
    }
	public function getNameCredit()
	{
		return Client::find()->where(['id' => $this->getCredit()->one()->id_client])->one()->name;
	}
	
	public function getCreditLink()
	{
		
		return Html::a( $this->getCredit()->one()->number, ["/credit/view-credit/", "id" => $this->id_credit ]);

	}
	 public function getSumPayment($model){
       
		 $query =  Payment::find()->select("sum(payment.sum) as sum")
            ->joinWith('credit.client')
            
			->andWhere($model->where)
			->one();
			
			return  $query->sum;
		
    }
	
	 public function getSum($model){
        $sum=0;
		if ($model->where)
		 $query =  Payment::find()->select("sum(sum) as sum")
			->andWhere($model->where)
			->one();
			else
			 $query = Payment::find()->select("sum(sum) as sum")
			->one();

        return $query->sum;

    }

    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $credit = $this->getCredit()->one();
        if ($credit !== null) {
            $credit->recalculateDebt();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $credit = Credit::findOne($this->id_credit);
        if ($credit !== null) {
            $credit->recalculateDebt();
        }
    }
}
