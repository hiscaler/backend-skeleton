<?php

namespace app\modules\admin\controllers;

use app\models\Constant;
use app\models\GridColumnConfig;
use Yii;
use yii\base\InvalidCallException;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
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
     * @param $name
     * @return mixed
     * @throws NotFoundHttpException
     * @throws BadRequestHttpException
     */
    public function actionIndex($name)
    {
        $request = Yii::$app->getRequest();
        if (!$request->getIsAjax() && !$request->getIsPjax()) {
            throw new BadRequestHttpException(Yii::t('app', 'Bad Request.'));
        }
        try {
            $attributeLabels = Yii::createObject($name)->attributeLabels();
        } catch (\Exception $ex) {
            throw new InvalidCallException($ex->getMessage());
        }

        if (!isset(Yii::$app->params['gridColumns'][$name])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $columns = Yii::$app->params['gridColumns'][$name];
        $invisibleColumns = GridColumnConfig::getInvisibleColumns($name);
        $models = [];
        foreach ($columns as $value) {
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
            'name' => $name,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionToggle()
    {
        $attribute = Yii::$app->getRequest()->post('id');
        $name = Yii::$app->getRequest()->post('name');
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
                    'value' => $value
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
                    'value' => Constant::BOOLEAN_FALSE
                ],
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

}
