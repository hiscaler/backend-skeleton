<?php

namespace app\modules\api\modules\wechat\controllers;

use app\helpers\Config;
use app\modules\api\models\Constant;
use app\modules\api\modules\wechat\models\Response;
use EasyWeChat\Foundation\Application;
use EasyWeChat\Support\Collection;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 微信消息、事件处理
 * Class DefaultController
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    private $_message;

    /**
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Server\BadRequestException
     * @throws \EasyWeChat\Core\Exceptions\InvalidArgumentException
     * @throws \yii\base\ExitException
     */
    public function actionIndex()
    {
        $server = $this->wxApplication->server;
        $server->setMessageHandler(function ($message) {
            $this->_message = $message;
            switch (strtolower($message->MsgType)) {
                case 'event':
                    switch (strtolower($message->Event)) {
                        case 'subscribe':
                            $this->subscribeEvent();
                            $this->processHandlers('event.subscribe', $message, $this->wxApplication);
                            break;

                        case 'unsubscribe':
                            $this->unsubscribeEvent();
                            $this->processHandlers('event.unsubscribe', $message, $this->wxApplication);
                            break;

                        default:
                            return self::DEFAULT_RETURN_MESSAGE;
                    }

                    return self::DEFAULT_RETURN_MESSAGE;
                    break;

                case 'text':
                case 'location':
                    return call_user_func(Response::class . "::{$message->MsgType}", $message);

                    break;

                case 'scan':
                    $this->processHandlers('scan', $message, $this->wxApplication);
                    break;

                default:
                    return self::DEFAULT_RETURN_MESSAGE;
                    break;
            }
        });

        $response = $server->serve();

        $response->sendContent();
        Yii::$app->end();
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
            $wxFieldValue = $user->unionid; // unionid
        } else {
            $wxFieldName = 'openid';
            $wxFieldValue = $openId; // openid
        }
        $nickname = preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $user->nickname);
        $wechatMemberId = $db->createCommand("SELECT [[id]] FROM {{%wechat_member}} WHERE [[$wxFieldName]] = :wxId", [':wxId' => $wxFieldValue])->queryScalar();
        if ($wechatMemberId) {
            $columns = [
                'openid' => $openId,
                'subscribe' => Constant::BOOLEAN_TRUE,
                'nickname' => $nickname,
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
                'nickname' => $nickname,
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

    private function processHandlers($key, Collection $collection, Application $application)
    {
        $handlers = Config::get("wechat.messageHandlers.$key", []);
        if ($handlers && is_array($handlers)) {
            foreach ($handlers as $handler) {
                if (class_exists($handler)) {
                    try {
                        call_user_func([new $handler(), 'process'], $collection, $application);
                    } catch (Exception $e) {
                        Yii::error($handler . ':' . $e->getMessage(), 'wechat.message.handlers');
                    }
                }
            }
        }
    }

}
