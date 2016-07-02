<?php

namespace app\models;

use ptheofan\statemachine\StateMachineBehavior;
use yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;

/**
 * Class User
 *
 * @package app\models
 *
 * @property StateMachineBehavior $status
 * @property int $id
 * @property string $email
 * @property string $auth_key
 * @property string $password_hash
 * @property string $_status
 * @property string $fname
 * @property string $lname
 * @property string $role
 */
class User extends ActiveRecord implements \yii\web\IdentityInterface
{
    const ROLE_ADMIN = 'admin';
    const ROLE_OWNER = 'owner';
    const ROLE_USER = 'user';
    const ROLE_GUEST = 'guest';
    const ROLE_SYSTEM = 'system';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'status' => [
                'class' => StateMachineBehavior::className(),
                'sm' => Yii::$app->smUserAccountStatus,
                'attr' => '_status',
                'virtAttr' => 'status',
            ],
        ];
    }

    public function rules()
    {
        return [
            [['email'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find()->andWhere(['email' => $email])->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * @param User $user
     * @return string
     */
    public function getUserRole($user)
    {
        if (!$user) {
            return self::ROLE_GUEST;
        }

        if ($user->role === User::ROLE_ADMIN) {
            return self::ROLE_ADMIN;
        }

        if ($user->role === User::ROLE_SYSTEM) {
            return self::ROLE_SYSTEM;
        }

        if ($this->hasProperty('created_by') && $this->created_by === $this->created_by) {
            return self::ROLE_OWNER;
        }

        return self::ROLE_USER;
    }
}
