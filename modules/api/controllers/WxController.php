<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\models\Constant;
use app\modules\api\models\Member;
use EasyWeChat\Foundation\Application;
use Exception;
use Yii;
use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;

/**
 * 微信处理接口
 * Class WxController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class WxController extends BaseController
{

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->params['wechat']) || !Yii::$app->params['wechat'] || !isset(Yii::$app->params['wechat']['app_id'], Yii::$app->params['wechat']['secret'])) {
            throw new InvalidConfigException('无效的微信配置。');
        }
    }

    /**
     * 微信认证
     *
     * @param $redirectUrl
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     * @throws Exception
     */
    public function actionAuth($redirectUrl)
    {
        $db = Yii::$app->getDb();
        $application = new Application(Yii::$app->params['wechat']);
        $user = $application->oauth->scopes(['snsapi_userinfo'])->user();
        if ($user) {
            $now = time();
            $accessToken = null;
            $openid = $user->openid;
            $memberId = $db->createCommand('SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openid])->queryScalar();
            if ($memberId) {
                $member = Member::findOne(['id' => $memberId]);
                if ($member !== null) {
                    $accessToken = $member->generateAccessToken();
                    $member->access_token = $accessToken;
                    $member->save(false);
                }
            } else {
                $member = new Member();
                $nickname = preg_replace('/([0-9#][\x{20E3}])|[\x{00ae}\x{00a9}\x{203C}\x{2047}\x{2048}\x{2049}\x{3030}\x{303D}\x{2139}\x{2122}\x{3297}\x{3299}][\x{FE00}-\x{FEFF}]?|[\x{2190}-\x{21FF}][\x{FE00}-\x{FEFF}]?|[\x{2300}-\x{23FF}][\x{FE00}-\x{FEFF}]?|[\x{2460}-\x{24FF}][\x{FE00}-\x{FEFF}]?|[\x{25A0}-\x{25FF}][\x{FE00}-\x{FEFF}]?|[\x{2600}-\x{27BF}][\x{FE00}-\x{FEFF}]?|[\x{2900}-\x{297F}][\x{FE00}-\x{FEFF}]?|[\x{2B00}-\x{2BF0}][\x{FE00}-\x{FEFF}]?|[\x{1F000}-\x{1F6FF}][\x{FE00}-\x{FEFF}]?/u', '', $user->getNickname());
                $maxId = $db->createCommand('SELECT MAX[[id]] FROM {{%member}}')->queryScalar();
                $member->username = sprintf('wx%08d', $maxId + 1) . rand(1000, 9999);
                $member->nickname = $nickname ?: $member->username;
                $member->real_name = $member->nickname;
                $member->setPassword($member->username);
                $member->avatar = $user->headimgurl;
                $member->status = Member::STATUS_ACTIVE;
                $accessToken = $member->generateAccessToken();
                $member->access_token = $accessToken;
                $transaction = $db->beginTransaction();
                try {
                    if ($member->save()) {
                        $memberId = $member->id;
                        $columns = [
                            'member_id' => $memberId,
                            'subscribe' => Constant::BOOLEAN_TRUE,
                            'openid' => $openid,
                            'nickname' => $user->nickname,
                            'sex' => $user->sex,
                            'country' => $user->country,
                            'province' => $user->province,
                            'city' => $user->city,
                            'language' => $user->language,
                            'headimgurl' => $user->headimgurl,
                            'subscribe_time' => $now,
                        ];
                        $db->createCommand()->insert('{{%wechat_member}}', $columns)->execute();
                        $transaction->commit();
                    } else {
                        $memberId = null;
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    $memberId = null;
                    throw new Exception($e->getMessage());
                }
            }

            $redirectUrl = urldecode($redirectUrl);
            if ($accessToken) {
                if (strpos($redirectUrl, '?') === false) {
                    $redirectUrl .= '?';
                } else {
                    $redirectUrl .= '&';
                }
                $redirectUrl .= "accessToken=$accessToken";
            }

            $this->redirect($redirectUrl);
        } else {
            throw new InvalidCallException('拉取微信认证失败。');
        }
    }

    /**
     * JsSdk 配置值
     *
     * @param null $url
     * @param string $api
     * @param bool $debug
     * @param bool $beta
     * @return array|string
     */
    public function actionJssdk($url = null, $api = '', $debug = false, $beta = true)
    {
        $validApi = ['checkJsApi', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'];
        $api = array_filter(explode(',', $api), function ($api) use ($validApi) {
            return $api && in_array($api, $validApi);
        });
        empty($api) && $api = ['checkJsApi'];

        $application = new Application(Yii::$app->params['wechat']);
        $js = $application->js;
        $url = $url ? urldecode($url) : Yii::$app->getRequest()->getHostInfo();
        $js->setUrl($url);

        return $js->config($api, $debug, $beta, false);
    }

}
