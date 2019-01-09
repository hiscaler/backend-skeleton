<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\Category;
use yadjet\helpers\ArrayHelper;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class CategoryController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class CategoryController extends ActiveController
{

    public $modelClass = Category::class;

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    /**
     * @return array
     * @throws \yii\db\Exception
     */
    public function behaviors()
    {
        $cmd = Yii::$app->getDb()->createCommand('SELECT MAX([[updated_at]]) FROM {{%category}}');
        if ($this->dbCacheTime !== null) {
            $cmd->cache($this->dbCacheTime);
        }
        $timestamp = $cmd->queryScalar();

        $behaviors = array_merge(parent::behaviors(), [
            [
                'class' => 'yii\filters\HttpCache',
                'lastModified' => function () use ($timestamp) {
                    return $timestamp;
                },
                'etagSeed' => function () use ($timestamp) {
                    return $timestamp;
                }
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['DELETE'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['index', 'view'],
                        'allow' => true,
                        'roles' => ['?', '@'],
                    ],
                ],
            ],
        ]);

        return $behaviors;
    }

    /**
     * 分类数据
     *
     * @param null|string $sign
     * @param int $level
     * @param bool $flat
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionIndex($sign = null, $level = 0, $flat = true)
    {
        if ($sign) {
            $parentId = \Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => $sign])->queryScalar();
            if (!$parentId) {
                return [];
            }
        } else {
            $parentId = 0;
        }
        $items = Category::getChildren($parentId, $level);
        !$flat && $items = ArrayHelper::toTree($items, 'id', 'parent');

        return $items;
    }

}