<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "balance".
 *
 * @property int $id
 * @property float $sum
 * @property int $id_costs
 * @property string $datetime
 */
class Balance extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'balance';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sum', 'id_costs', 'datetime'], 'required'],
            [['sum'], 'number'],
            [['id_costs'], 'integer'],
            [['datetime'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sum' => 'Sum',
            'id_costs' => 'Id Costs',
            'datetime' => 'Datetime',
        ];
    }
}
