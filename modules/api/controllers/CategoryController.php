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

        return array_merge(parent::behaviors(), [
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
    }

    /**
     * 获取分类数据列表
     *
     * @param null|string $sign
     * @param int $level
     * @param string $flat
     * @param string $fields id:id,name:newName
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionIndex($sign = null, $level = 0, $flat = 'y', $fields = '')
    {
        $items = [];
        if ($rawItems = Category::getChildren($sign, $level)) {
            $fieldsValue = trim($fields);
            if ($fieldsValue) {
                $fields = [];
                foreach (explode(',', $fieldsValue) as $str) {
                    if (strpos($str, ':') === false) {
                        $k = $v = $str;
                    } else {
                        list($k, $v) = explode(':', $str);
                    }
                    $fields[$k] = $v;
                }
                if (strtolower($flat) != 'y') {
                    !isset($fields['id']) && $fields['id'] = 'id';
                    !isset($fields['parent']) && $fields['parent'] = 'parent';
                }
            } else {
                $fields = [];
            }
            $idKey = 'id';
            $parentKey = 'parent';
            foreach ($rawItems as $item) {
                $item['short_name'] = $item['shortName'];
                unset($item['shortName']);
                if ($fields) {
                    foreach ($item as $k => $v) {
                        if (!array_key_exists($k, $fields)) {
                            unset($item[$k]);
                        } else {
                            if ($fields[$k] !== $k) {
                                if (in_array($k, ['id', 'parent'])) {
                                    $idKey = $fields[$k];
                                }
                                $item[$fields[$k]] = $v;
                                unset($item[$k]);
                            }
                        }
                    }
                }
                $items[] = $item;
            }
            strtolower($flat) != 'y' && $items = ArrayHelper::toTree($items, $idKey, $parentKey);
        }

        return $items;
    }

}