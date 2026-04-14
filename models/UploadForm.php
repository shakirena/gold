<?php
namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $imageFiles;

    public function rules()
    {
        return [
            [['imageFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg', 'maxFiles' => 4],
        ];
    }

    public function upload($id)
    {
       // if ($this->validate()) {
            foreach ($this->imageFiles as $file) {
                mkdir("uploads/".$id);
				mkdir("thumb/".$id);
                $file->saveAs('uploads/'.$id.'/' . $file->baseName . '.' . $file->extension);
            }
            return true;
       /* } else {
            return false;
        }*/
    }
}