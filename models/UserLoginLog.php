<?php

namespace app\models;

use yadjet\helpers\UtilHelper;
use Yii;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%user_login_log}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $login_ip
 * @property string $client_informations
 * @property integer $login_at
 */
class UserLoginLog extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_login_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'login_ip', 'client_informations', 'login_at'], 'required'],
            [['user_id', 'login_at'], 'integer'],
            [['login_ip', 'client_informations'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('user', 'Username'),
            'login_ip' => Yii::t('userLoginLog', 'Login IP'),
            'client_informations' => Yii::t('userLoginLog', 'Client Informations'),
            'login_at' => Yii::t('userLoginLog', 'Login At'),
        ];
    }

    /**
     * User relational
     *
     * @return ActiveQueryInterface the relational query object.
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->select(['id', 'username']);
    }

    public static function write()
    {
        Yii::$app->getDb()->createCommand()->insert('{{%user_login_log}}', [
            'user_id' => Yii::$app->getUser()->getId(),
            'login_ip' => Yii::$app->getRequest()->getUserIP(),
            'client_informations' => UtilHelper::getBrowserName(),
            'login_at' => time(),
        ])->execute();
    }

}
