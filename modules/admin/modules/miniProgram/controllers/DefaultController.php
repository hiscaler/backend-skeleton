<?php

namespace app\modules\admin\modules\miniProgram\controllers;

use app\modules\admin\extensions\BaseController;
use EasyWeChat\Foundation\Application;
use Yii;
use yii\filters\AccessControl;

/**
 * 数据分析
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends BaseController
{

    public function init()
    {
        parent::init();
        if (!isset(Yii::$app->params['wechat']) || !is_array(Yii::$app->params['wechat'])) {
            throw new InvalidConfigException('无效的微信参数配置。');
        }
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * 数据分析
     *
     * @return string
     */
    public function actionIndex()
    {
        $application = new Application(Yii::$app->params['wechat']);
        $miniProgram = $application->mini_program;
        $data = $miniProgram->stats->montylyRetainInfo('20180101', '20180131')->all();

        return $this->render('index', [
            'data' => $data,
        ]);
    }

}
