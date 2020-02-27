<?php

namespace app\modules\admin\controllers;

use app\models\Constant;
use app\models\GridColumnConfig;
use Exception;
use InvalidArgumentException;
use Yii;
use yii\base\InvalidCallException;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * 表格类设定管理
 * Class GridColumnConfigsController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class GridColumnConfigsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'toggle'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'toggle' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all GridColumnConfig models.
     *
     * @rbacDescription 表格自定义栏位展示
     * @param $id
     * @param $name
     * @return mixed
     * @throws BadRequestHttpException
     * @throws \yii\db\Exception
     */
    public function actionIndex($id, $name)
    {
        $request = Yii::$app->getRequest();
        if (!$request->getIsAjax() && !$request->getIsPjax()) {
            throw new BadRequestHttpException(Yii::t('app', 'Bad Request.'));
        }
        try {
            $attributeLabels = Yii::createObject(['class' => $name])->attributeLabels();
        } catch (\Exception $ex) {
            throw new InvalidCallException($ex->getMessage());
        }

        $cache = Yii::$app->getCache();
        $cacheKey = "cache_{$id}_columns";
        try {
            $columns = [];
            $rawModels = $request->post('models');
            if ($rawModels) {
                $models = unserialize(base64_decode($rawModels));
                $cache->set($cacheKey, $models, 0);
            } else {
                // 翻页的时候不会传递 models 参数过来，所以从缓存中获取
                $models = $cache->get($cacheKey);
                if ($models === false) {
                    $models = [];
                }
            }
            $models = reset($models);
            if (is_array($models) || is_object($models)) {
                foreach ($models as $key => $value) {
                    if ($value === null || is_scalar($value) || is_callable([$value, '__toString'])) {
                        $columns[] = (string) $key;
                    }
                }
            }
        } catch (Exception $e) {
            throw new InvalidArgumentException('`models` params is invalid.');
        }
        $invisibleColumns = GridColumnConfig::getInvisibleColumns($id);
        $models = [];
        foreach ($columns as $value) {
            if ($value == 'id') {
                continue;
            }
            $models[] = [
                'id' => $value,
                'attribute' => isset($attributeLabels[$value]) ? $attributeLabels[$value] : Inflector::camel2words(Inflector::camelize($value)),
                'visible' => !in_array($value, $invisibleColumns)
            ];
        }

        $dataProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $models,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        return $this->renderAjax('index', [
            'gridId' => $id,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @rbacDescription 开启或关闭表格自定义栏位
     *
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionToggle()
    {
        $request = Yii::$app->getRequest();
        $attribute = $request->post('id');
        $name = $request->post('name');
        $userId = \Yii::$app->getUser()->getId();
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[visible]] FROM {{%grid_column_config}} WHERE [[user_id]] = :userId AND [[name]] = :name AND [[attribute]] = :attribute', [
            ':userId' => $userId,
            ':name' => $name,
            ':attribute' => $attribute
        ])->queryScalar();
        if ($value !== false) {
            $value = $value ? Constant::BOOLEAN_FALSE : Constant::BOOLEAN_TRUE;
            $db->createCommand()->update('{{%grid_column_config}}', ['visible' => $value], '[[user_id]] = :userId AND [[name]] = :name AND [[attribute]] = :attribute', [
                ':userId' => $userId,
                ':name' => $name,
                ':attribute' => $attribute
            ])->execute();
            $responseBody = [
                'success' => true,
                'data' => [
                    'value' => $value ? true : false
                ],
            ];
        } else {
            // Insert config data
            $db->createCommand()->insert('{{%grid_column_config}}', [
                'name' => $name,
                'attribute' => $attribute,
                'visible' => Constant::BOOLEAN_FALSE,
                'user_id' => $userId,
            ])->execute();

            $responseBody = [
                'success' => true,
                'alias' => 'value',
                'data' => [
                    'value' => false
                ],
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

}
