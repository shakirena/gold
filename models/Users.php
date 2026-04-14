<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "users".
 *
 * @property int $id_user Идентификатор пользователя
 * @property string $telephone Телефон работника
 * @property string $fio ФИО
 * @property string $login Логин 
 * @property string $password Пароль
 * @property int $id_role Идентификатор роли
 * @property double $salary Зарплата
 * @property int $id_store
 *
 * @property Users $role
 * @property Users[] $users
 */
class Users extends \yii\db\ActiveRecord  implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fio', 'login', 'password', 'id_role'], 'required'],
            [['id_role', 'id_store'], 'integer'],
            [['salary'], 'number'],
            [['telephone', 'login'], 'string', 'max' => 50],
            [['fio'], 'string', 'max' => 255],
            [['password'], 'string', 'max' => 200],
            [['id_role'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['id_role' => 'id_role']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_user' => 'Id User',
            'telephone' => 'Telephone',
            'fio' => 'Fio',
            'login' => 'Login',
            'password' => 'Password',
            'id_role' => 'Id Role',
            'salary' => 'Salary',
            'id_store' => 'Id Store',
        ];
    }
   public static function findByUsername($username)
    {
        return static::findOne([
            'login' => $username
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Roles::className(), ['id_role' => 'id_role']);
    }

    /**
  
	
	 /* Хелперы */
    /**
     * Сравнивает полученный пароль с паролем в поле password_hash, для текущего пользователя, в таблице user.
     * Вызываеться из модели LoginForm.
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }
    /* Аутентификация пользователей */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
	public function setPassword($password) {
        $this->password=Yii::$app->security->generatePasswordHash($password);
    }


    public function getId()
    {
        return $this->id_user;
    }
	public function getIdStore()
    {
        return $this->hasOne(Store::className(), ['id' => 'id_store']);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    public function getAuthKey()
    {
        return null;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }
}
