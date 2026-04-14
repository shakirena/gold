<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property string|null $path
 * @property string|null $thumb
 * @property string|null $note
 * @property int $id_tre
 *
 * @property Products $tre
 */
class Image extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['thumb', 'note'], 'string'],
            [['id_tre'], 'required'],
            [['id_tre'], 'integer'],
            [['path'], 'string', 'max' => 200],
            [['id_tre'], 'exist', 'skipOnError' => true, 'targetClass' => Products::className(), 'targetAttribute' => ['id_tre' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'path' => 'Path',
            'thumb' => 'Thumb',
            'note' => 'Note',
            'id_tre' => 'Id Tre',
        ];
    }

    /**
     * Gets query for [[Tre]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTre()
    {
        return $this->hasOne(Products::className(), ['id' => 'id_tre']);
    }
}
