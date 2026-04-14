<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "client".
 *
 * @property int $id
 * @property string $name
 * @property string $phone
 * @property string $adress
 * @property string $note
 * @property string $passport
 *
 * @property Credit[] $credits
 */
class Client extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'client';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['phone'], 'string'],
            [['name', 'adress', 'note','phone2', 'adress2'], 'string', 'max' => 200],
            [['passport'], 'string', 'max' => 100],
			[['fin'], 'string', 'max' => 7],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Müştəri adı',
            'phone' => 'Phone',
            'adress' => 'Ünvan',
			'phone2' => 'Phone2',
            'adress2' => 'Faktiki  ünvanı',
            'note' => 'Qeyd',
            'passport' => 'Şəxsiyyət Vəsiqəsi(S/N)',
			'fin' => 'FIN',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCredits()
    {
        return $this->hasMany(Credit::className(), ['id_client' => 'id']);
    }
}
