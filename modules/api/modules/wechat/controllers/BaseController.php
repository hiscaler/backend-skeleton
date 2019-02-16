<?php

namespace app\modules\api\modules\wechat\controllers;

use EasyWeChat\Foundation\Application;
use Yii;
use yii\base\InvalidConfigException;
use yii\rest\Controller;

/**
 * 微信处理接口
 * Class BaseController
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseController extends Controller
{

    const DEFAULT_RETURN_MESSAGE = '';

    protected $_config;

    /* @var $_application Application */
    protected $_application;

    /* @var $service \EasyWeChat\Core\AccessToken|\EasyWeChat\Server\Guard|\EasyWeChat\User\User|\EasyWeChat\User\Tag|\EasyWeChat\User\Group|\EasyWeChat\Js\Js|\Overtrue\Socialite\Providers\WeChatProvider|\EasyWeChat\Menu\Menu|\EasyWeChat\Notice\Notice|\EasyWeChat\Material\Material|\EasyWeChat\Material\Temporary|\EasyWeChat\Staff\Staff|\EasyWeChat\Url\Url|\EasyWeChat\QRCode\QRCode|\EasyWeChat\Semantic\Semantic\EasyWeChat\Stats\Stats|\EasyWeChat\Payment\Merchant|\EasyWeChat\Payment\Payment|\EasyWeChat\Payment\LuckyMoney\LuckyMoney|\EasyWeChat\Payment\MerchantPay\MerchantPay|\EasyWeChat\Payment\CashCoupon\CashCoupon|\EasyWeChat\Reply\Reply|\EasyWeChat\Broadcast\Broadcast|\EasyWeChat\Card\Card|\EasyWeChat\Device\Device|\EasyWeChat\Comment\Comment|\EasyWeChat\ShakeAround\ShakeAround|\EasyWeChat\OpenPlatform\OpenPlatform|\EasyWeChat\MiniProgram\MiniProgram */
    protected $service;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->params['wechat']) || !Yii::$app->params['wechat'] || !isset(Yii::$app->params['wechat']['app_id'], Yii::$app->params['wechat']['secret'])) {
            throw new InvalidConfigException('无效的微信配置。');
        }
        $this->_config = Yii::$app->params['wechat'];
        $this->_application = new Application($this->_config);
    }

}
