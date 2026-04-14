<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "costs".
 *
 * @property int $id
 * @property int|null $id_type
 * @property float $sum
 * @property string|null $note
 * @property string $datetime
 * @property int|null $id_user
 * @property int|null $id_kassa
 *
 * @property TypeProduct $type
 * @property Kassa $kassa
 * @property Users $user
 * @property TypeCosts $id0
 */
class Costs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'costs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_type', 'id_user', 'id_kassa'], 'integer'],
            [['sum', 'datetime'], 'required'],
            [['sum'], 'number'],
            [['note'], 'string'],
            [['datetime'], 'safe'],
            [['id_type'], 'exist', 'skipOnError' => true, 'targetClass' => TypeCosts::className(), 'targetAttribute' => ['id_type' => 'id']],
            [['id_kassa'], 'exist', 'skipOnError' => true, 'targetClass' => Kassa::className(), 'targetAttribute' => ['id_kassa' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['id_user' => 'id_user']],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => TypeCosts::className(), 'targetAttribute' => ['id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_type' => 'Əməliyyat',
            'sum' => 'Məbləğ',
            'note' => 'Qeyd',
            'datetime' => 'Tarix',
            'id_user' => 'Id User',
            'id_kassa' => 'Kassa',
        ];
    }

    /**
     * Gets query for [[Type]].
     *
     * @return \yii\db\ActiveQuery
     */
   
	public function getIdType()
    {
        return $this->hasOne(TypeCosts::className(), ['id' => 'id_type']);
    }
    /**
     * Gets query for [[Kassa]].
     *
     * @return \yii\db\ActiveQuery
     */
   public function getIdKassa()
    {
        return $this->hasOne(Kassa::className(), ['id' => 'id_kassa']);
    }
    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::className(), ['id_user' => 'id_user']);
    }

	public function getGetSum()
    {
		if ($this->sum<0) return -$this->sum;
        return $this->sum;
    }
	public function getTypeName()
	{
		$typeRecord = $this->getIdType()->one();
		if ($typeRecord === null) {
			return '';
		}
		return $typeRecord->name;
	}
	public function getSum($model){
        $sum=0;
        $query =  Costs::find()->select("sum(abs(sum)) as sum")
         ->joinWith(["idType"])
            ->where($model->where)->andWhere("id_type!=4")->one();


        return $query->sum;
    }
}
