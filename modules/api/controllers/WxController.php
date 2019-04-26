<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\BaseController;
use app\modules\api\forms\WechatMemberBindForm;
use app\modules\api\models\Constant;
use app\modules\api\models\Member;
use EasyWeChat\Foundation\Application;
use Exception;
use yadjet\helpers\StringHelper;
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
    public function actionAuth($redirectUrl = null)
    {
        $application = new Application(Yii::$app->params['wechat']);
        if (empty($redirectUrl)) {
            $application->server->serve()->send();
        } else {
            $db = Yii::$app->getDb();
            $user = $application->oauth->scopes(['snsapi_userinfo'])->user();
            if ($user) {
                $now = time();
                $accessToken = null;
                $openId = $user->getId();
                $memberId = $db->createCommand('SELECT [[member_id]] FROM {{%wechat_member}} WHERE [[openid]] = :openid', [':openid' => $openId])->queryScalar();
                if ($memberId) {
                    $member = Member::findOne(['id' => $memberId]);
                    if ($member !== null) {
                        $accessToken = $member->generateAccessToken();
                        $member->access_token = $accessToken;
                        $member->save(false);
                    }
                } else {
                    $member = new Member();
                    $nickname = $user->nickname;
                    if (strtolower($db->charset) != 'utf8mb4') {
                        $nickname = StringHelper::removeEmoji($nickname);
                    }
                    $maxId = $db->createCommand('SELECT MAX([[id]]) FROM {{%member}}')->queryScalar();
                    $member->username = sprintf('wx%08d', $maxId + 1) . rand(1000, 9999);
                    $member->nickname = $nickname ?: $member->username;
                    $member->real_name = $member->nickname;
                    $member->setPassword($member->username);
                    $member->avatar = $user->headimgurl;
                    $member->status = Member::STATUS_ACTIVE;
                    $member->generateAccessToken();
                    $accessToken = $member->access_token;
                    $transaction = $db->beginTransaction();
                    try {
                        if ($member->save()) {
                            $memberId = $member->id;

                            $columns = [
                                'member_id' => $memberId,
                                'subscribe' => Constant::BOOLEAN_TRUE,
                                'openid' => $openId,
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

    /**
     * 微信帐号绑定
     *
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function actionBind()
    {
        $config = Yii::$app->params['wechat'];
        if (!isset($config['thirdPartyLogin'], $config['thirdPartyLogin']['app_id'], $config['thirdPartyLogin']['secret'])) {
            $field = 'unionid';
        } else {
            $field = 'openid';
        }

        $model = new WechatMemberBindForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        $model->xid_field = $field;
        if ($model->validate() && $model->bind()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(204);
        }

        return $model;
    }

}
