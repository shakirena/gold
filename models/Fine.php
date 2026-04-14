<?php

namespace app\models;

use Yii;
use yii\bootstrap\Html;
/**
 * This is the model class for table "fine".
 *
 * @property int $id
 * @property int $id_credit
 * @property float $sum
 * @property string $date
 *
 * @property Credit $credit
 */
class Fine extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'fine';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_credit', 'sum', 'date'], 'required'],
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
            'date' => 'Date',
			'note' => 'Note',
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
}
