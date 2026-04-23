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
            ['sum', 'validateFineSum'],
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
    /**
     * Validates that fine sum does not exceed Credit.debt and debt is positive.
     *
     * @param string $attribute the attribute being validated
     * @param array $params additional parameters
     */
    public function validateFineSum($attribute, $params)
    {
        if ($this->hasErrors('id_credit') || $this->hasErrors('sum')) {
            return;
        }
        $credit = $this->getCredit()->one();
        if ($credit === null) {
            return;
        }
        if ($credit->debt <= 0) {
            $this->addError($attribute, 'Нельзя начислить штраф: остаток долга равен нулю.');
        }
        if ($this->sum > $credit->debt) {
            $this->addError($attribute, 'Сумма штрафа не может превышать остаток долга (' . $credit->debt . ').');
        }
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
