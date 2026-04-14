<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "products".
 *
 * @property int $id
 * @property string $name
 * @property double $weight
 * @property double $net_weight
 * @property int $id_credit
 *
 * @property Credit $credit
 */
class Products extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['weight', 'net_weight'], 'number'],
            [['id_credit'], 'integer'],
            [['name'], 'string', 'max' => 100],
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
            'name' => 'Malın adı',
            'weight' => 'Cəkisi',
            'net_weight' => 'Xalis cəkisi',
            'id_credit' => 'Id Credit',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCredit()
    {
        return $this->hasOne(Credit::className(), ['id' => 'id_credit']);
    }
}
