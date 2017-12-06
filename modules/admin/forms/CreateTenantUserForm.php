<?php

namespace app\modules\admin\forms;

use app\models\User;
use app\models\Yad;
use Yii;
use yii\base\Model;

class CreateTenantUserForm extends Model
{

    public $tenant_id;
    public $tenant_name;
    public $user_id;
    public $username;
    public $user_group_id;
    public $role;
    public $rule_id;

    public function rules()
    {
        return [
            [['username', 'role'], 'required'],
            [['username'], 'trim'],
            [['rule_id', 'user_group_id'], 'default', 'value' => 0],
            [['user_id', 'user_group_id', 'rule_id', 'tenant_id'], 'integer'],
            ['role', 'default', 'value' => User::ROLE_USER],
            ['role', 'in', 'range' => array_keys(User::roleOptions())],
            [['username'], 'checkUser'],
        ];
    }

    public function checkUser($attribute, $params)
    {
        $db = Yii::$app->getDb();
        $userId = $db->createCommand('SELECT [[id]] FROM {{%user}} WHERE [[username]] = :username AND [[status]] = :status')->bindValues([
            ':username' => $this->username,
            ':status' => User::STATUS_ACTIVE
        ])->queryScalar();
        if (!$userId) {
            $this->addError($attribute, '该用户不存在或者处于非激活状态。');
        } else {
            $this->user_id = $userId;
            $exists = $db->createCommand('SELECT COUNT(*) FROM {{%tenant_user}} WHERE [[tenant_id]] = :tenantId AND [[user_id]] = :userId')->bindValues([
                ':tenantId' => $this->tenant_id,
                ':userId' => $userId
            ])->queryScalar();
            if ($exists) {
                $this->addError($attribute, $this->username . ' 已经绑定「' . Yad::getTenantName() . '」站点，禁止重复绑定。');
            }
        }
    }

    /**
     * Declares attribute labels.
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Username'),
            'user_group_id' => Yii::t('user', 'Group'),
            'role' => Yii::t('user', 'Role'),
            'rule_id' => Yii::t('user', 'Rule'),
        ];
    }

}
