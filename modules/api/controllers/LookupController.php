<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\ActiveController;
use app\modules\api\models\Lookup;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

/**
 * Class LookupController
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class LookupController extends ActiveController
{

    public $modelClass = Lookup::class;

    public function behaviors()
    {
        $behaviors = array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['POST'],
                    'update' => ['PUT', 'PATCH'],
                    'delete' => ['POST'],
                    '*' => ['GET'],
                ],
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'view', 'delete', 'value', 'values'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['value', 'values'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    throw new \yii\web\ForbiddenHttpException('You are not allowed to access this endpoint');
                }
            ],
        ]);

        return $behaviors;
    }

    /**
     * 获取常规设定值
     *
     * @param $key
     * @param null $defaultValue
     * @return mixed|null
     * @throws \yii\db\Exception
     */
    public function actionValue($key, $defaultValue = null)
    {
        return Lookup::getValue($key, $defaultValue);
    }

    /**
     * 获取多个常规设定值
     * a:1,b:2,c 会转换为：['a' => 1, 'b' => 2, c]
     *
     * @param $keys
     * @return array
     * @throws \yii\db\Exception
     */
    public function actionValues($keys)
    {
        $params = [];
        foreach (explode(',', $keys) as $value) {
            if (stripos($value, ':', $value) === false) {
                $params[] = $value;
            } else {
                list($key, $value) = explode(':', $value);
                $params[$key] = $value;
            }
        }

        return Lookup::getValues($params);
    }

}