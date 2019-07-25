<?php

namespace app\modules\admin\controllers;

use app\modules\admin\forms\CacheCleanForm;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;

/**
 * 缓存管理
 * Class CachesController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class CachesController extends Controller
{

    private $keys = [
        'app.models.Category.rawData.0',
        'app.models.Category.rawData.1',
        'app.models.FileUploadConfig.getConfigs',
        'app.models.Lookup.getRawData',
        'api.module.getDevelopmentModules',
    ];

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
     * 缓存管理
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new CacheCleanForm();
        if ($model->load(Yii::$app->getRequest()->post()) && $model->validate()) {
            ini_set('max_execution_time', 600);
            switch ($model->type) {
                case $model::TYPE_SYSTEM:
                    $cache = Yii::$app->getCache();
                    foreach ($this->keys as $key) {
                        $cache->delete($key);
                    }
                    break;

                default:
                    Yii::$app->getCache()->flush();
                    break;
            }
            Yii::$app->getSession()->setFlash('notice', "缓存清理完毕。" . Html::a('继续清理', ['index'], ['class' => 'button']));
        }

        return $this->render('index', [
            'model' => $model,
        ]);
    }

}
