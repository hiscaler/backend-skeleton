<?php

namespace app\modules\api\forms;

use app\modules\api\models\Member;
use Yii;
use yii\base\Model;

/**
 * Class WechatMemberBindForm
 * 微信会员绑定
 *
 * @package app\modules\api\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class WechatMemberBindForm extends Model
{

    private $_member_id;

    /**
     * 微信 openid 或者 unionid
     *
     * @var
     */
    public $xid;

    /**
     * @var string 操作的字段（openid, unionid）
     */
    public $xid_field = 'openid';

    /**
     * @var string 用户名
     */
    public $username;

    /**
     * @var string 密码
     */
    public $password;

    public function rules()
    {
        return [
            [['open_id', 'username', 'password'], 'required'],
            [['open_id', 'username', 'password'], 'string'],
            [['open_id', 'username'], 'trim'],
            ['username', function ($attribute, $params) {
                $member = Member::findByUsername($this->username);
                if ($member) {
                    if (!$member->validatePassword($this->password)) {
                        $this->addError('password', '密码错误。');
                    } else {
                        $id = Yii::$app->getDb()->createCommand("SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[{$this->xid_field}]] = :xid", [
                            ':xid' => $this->xid
                        ])->queryScalar();
                        if ($id === false) {
                            $this->addError($attribute, '未找到您的关注信息，请确认是否已经关注服务号。');
                        } elseif ($id > 0) {
                            $this->addError($attribute, '此账号已经被绑定，如有问题，请联系客服。');
                        } else {
                            $this->_member_id = $member->id;
                        }
                    }
                } else {
                    $this->addError($attribute, '帐号不存在。');
                }
            }]
        ];
    }

    /**
     * 绑定微信帐号
     *
     * @throws \yii\db\Exception
     */
    public function bind()
    {
        return Yii::$app->getDb()->createCommand()->update("{{%wechat_member}}", ['member_id' => $this->_member_id], [$this->xid_field => $this->xid])->execute() ? true : false;
    }

}