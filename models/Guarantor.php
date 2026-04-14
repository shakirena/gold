<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "guarantor".
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
class Guarantor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'guarantor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['adress', 'note'], 'string'],
            [['name', 'passport'], 'string', 'max' => 200],
            [['phone'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'adress' => 'Adress',
            'note' => 'Note',
            'passport' => 'Passport',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCredits()
    {
        return $this->hasMany(Credit::className(), ['id_guarantor' => 'id']);
    }
}
