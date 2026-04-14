<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "type_costs".
 *
 * @property int $id
 * @property string $name
 * @property int|null $type 1-Medaxi , 0- Mexaric
 *
 * @property Costs $costs
 */
class TypeCosts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type_costs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['type'], 'integer'],
            [['name'], 'string', 'max' => 200],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Ad',
            'type' => 'Type',
        ];
    }

    /**
     * Gets query for [[Costs]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCosts()
    {
        return $this->hasOne(Costs::className(), ['id' => 'id']);
    }
}
