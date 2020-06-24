<?php

namespace app\modules\api\controllers;

use app\modules\api\extensions\AuthController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;

/**
 * Class OptionController
 *
 * 选项接口
 *
 * @package app\modules\api\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class OptionController extends AuthController
{

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    '*' => ['GET'],
                ],
            ],
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
        ]);
    }

    /**
     * 选项集合
     *
     * @param $name string 选项名称
     * @return array
     */
    public function actionIndex($name)
    {
        $options = [];
        $name = strtolower($name);
        if (!in_array($name, ['tables', 'core-tables', 'models'])) {
            $name = lcfirst(Inflector::id2camel($name));
            $class = '\app\modules\api\models\Option';
            if (method_exists($class, $name)) {
                try {
                    foreach ($class::$name() as $key => $name) {
                        $options[] = [
                            'key' => $key,
                            'name' => $name,
                        ];
                    }
                } catch (\Exception $e) {
                }
            }
        }

        return $options;
    }

}
