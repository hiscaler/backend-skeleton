<?php

namespace app\modules\api\modules\wechat\controllers;

use app\modules\api\models\Constant;
use EasyWeChat\Foundation\Application;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 微信消息、事件处理
 * Class DefaultController
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    private $_message;

    /**
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Server\BadRequestException
     */
    public function actionIndex()
    {
        $app = new Application(Yii::$app->params['wechat']);
        $server = $app->server;

        $server->setMessageHandler(function ($message) {
            $this->_message = $message;
            switch (strtolower($message->MsgType)) {
                case 'event':
                    switch (strtolower($message->Event)) {
                        case 'subscribe':
                            $this->subscribeEvent();
                            break;

                        case 'unsubscribe':
                            $this->unsubscribeEvent();
                            break;

                        default:
                            return self::DEFAULT_RETURN_MESSAGE;
                    }

                    return self::DEFAULT_RETURN_MESSAGE;
                    break;

                default:
                    return self::DEFAULT_RETURN_MESSAGE;
                    break;
            }
        });

        $response = $server->serve();

        $response->send();
    }

    /**
     * 订阅
     *
     * @throws \yii\db\Exception
     */
    private function subscribeEvent()
    {
        $openId = $this->_message->FromUserName;
        $user = $this->wxApplication->user->get($openId);
        $db = Yii::$app->getDb();
        if ($this->enableThirdPartyLogin) {
            $wxFieldName = 'unionid';
            $wxFieldValue = $user->unionid; // unionid\
        } else {
            $wxFieldName = 'openid';
            $wxFieldValue = $openId; // openid
        }
        $wechatMemberId = $db->createCommand("SELECT [[id]] FROM {{%wechat_member}} WHERE [[$wxFieldName]] = :wxId", [':wxId' => $wxFieldValue])->queryScalar();
        if ($wechatMemberId) {
            $columns = [
                'openid' => $openId,
                'subscribe' => Constant::BOOLEAN_TRUE,
                'nickname' => $user->nickname,
                'sex' => $user->sex,
                'country' => $user->country,
                'province' => $user->province,
                'city' => $user->city,
                'language' => $user->language,
                'headimgurl' => $user->headimgurl,
                'subscribe_time' => time(),
                'unionid' => $user->unionid,
            ];
            $db->createCommand()->update('{{%wechat_member}}', $columns, ['id' => $wechatMemberId])->execute();
        } else {
            $columns = [
                'member_id' => 0,
                'subscribe' => Constant::BOOLEAN_TRUE,
                'openid' => $openId,
                'nickname' => $user->nickname,
                'sex' => $user->sex ?: Constant::SEX_UNKNOWN,
                'country' => $user->country,
                'province' => $user->province,
                'city' => $user->city,
                'language' => $user->language,
                'headimgurl' => $user->headimgurl,
                'subscribe_time' => time(),
                'unionid' => $user->unionid,
            ];
            $db->createCommand()->insert('{{%wechat_member}}', $columns)->execute();
        }
    }

    /**
     * 取消订阅
     *
     * @throws \yii\db\Exception
     */
    public function unsubscribeEvent()
    {
        $db = Yii::$app->getDb();
        $wechatMemberId = $db->createCommand('SELECT [[id]] FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $this->_message->FromUserName])->queryScalar();
        if ($wechatMemberId) {
            if (ArrayHelper::getValue($this->wxConfig, 'other.subscribe.deleteAfterCancel', false) === true) {
                $db->createCommand()->delete('{{%wechat_member}}', ['id' => $wechatMemberId])->execute();
            } else {
                $db->createCommand()->update('{{%wechat_member}}', ['subscribe' => Constant::BOOLEAN_FALSE], ['id' => $wechatMemberId])->execute();
            }
        }
    }

}
