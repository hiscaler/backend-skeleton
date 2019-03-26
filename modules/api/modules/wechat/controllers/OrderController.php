<?php

namespace app\modules\api\modules\wechat\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\modules\wechat\models\Order;

/**
 * api/wechat/order 接口
 * Class OrderController
 *
 * @package app\modules\api\modules\wechat\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class OrderController extends ActiveController
{

    public $modelClass = Order::class;

}
