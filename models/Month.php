<?php

namespace app\models;

use Yii;
use yii\bootstrap\Html;
/**
 * This is the model class for table "month".
 *
 * @property int $id
 * @property int $id_credit
 * @property float $sum
 * @property string|null $note
 * @property string|null $date
 *
 * @property Credit $credit
 */
class Month extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'month';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_credit', 'sum'], 'required'],
            [['id_credit'], 'integer'],
            [['sum'], 'number'],
            [['date'], 'safe'],
            [['note'], 'string', 'max' => 100],
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
            'note' => 'Note',
            'date' => 'Date',
        ];
    }
	public function getNameCredit()
	{
		return Client::find()->where(['id' => $this->getCredit()->one()->id_client])->one()->name;
	
	}
	
	
	public function getCreditLink()
	{
		
		return Html::a( $this->getCredit()->one()->number, ["/credit/view-credit/", "id" => $this->id_credit ]);

	}
    /**
     * Gets query for [[Credit]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCredit()
    {
        return $this->hasOne(Credit::className(), ['id' => 'id_credit']);
    }
	
	 public function getSum($model){
        $sum=0;
		if ($model->where)
		 $query =  Month::find()->select("sum(sum) as sum")
			->andWhere($model->where)
			->one();
			else 
			 $query = Month::find()->select("sum(sum) as sum")
			->one();
		
        return $query->sum;
		
    }
}
