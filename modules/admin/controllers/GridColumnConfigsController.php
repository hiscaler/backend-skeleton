<?php

namespace app\modules\admin\controllers;

use app\models\BaseActiveRecord;
use app\models\Constant;
use app\models\GridColumnConfig;
use Yii;
use yii\base\InvalidCallException;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;
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
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'toggle'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'toggle' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all GridColumnConfig models.
     *
     * @return mixed
     */
    public function actionIndex($name)
    {
        $request = Yii::$app->getRequest();
        if (!$request->isAjax && !$request->isPjax) {
            throw new \yii\web\BadRequestHttpException(Yii::t('app', 'Bad Request.'));
        }
        try {
            $attributeLabels = Yii::createObject(BaseActiveRecord::id2ClassName($name))->attributeLabels();
        } catch (\Exception $ex) {
            throw new InvalidCallException($ex->getMessage());
        }

        if (!isset(Yii::$app->params['gridColumns'][$name])) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        $columns = Yii::$app->params['gridColumns'][$name];
        $invisibleColumns = GridColumnConfig::getInvisibleColumns($name);
        $rawData = [];
        foreach ($columns as $value) {
            $rawData[] = [
                'id' => $value,
                'attribute' => isset($attributeLabels[$value]) ? $attributeLabels[$value] : Inflector::camel2words(Inflector::camelize($value)),
                'visible' => !in_array($value, $invisibleColumns)
            ];
        }

        $dataProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $rawData,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);

        return $this->renderAjax('index', [
            'name' => $name,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionToggle()
    {
        $attribute = Yii::$app->getRequest()->post('id');
        $name = Yii::$app->request->post('name');
        $db = Yii::$app->getDb();
        $value = $db->createCommand('SELECT [[visible]] FROM {{%grid_column_config}} WHERE [[user_id]] = :userId AND [[name]] = :name AND [[attribute]] = :attribute')->bindValues([
            ':userId' => Yii::$app->getUser()->getId(),
            ':name' => $name,
            ':attribute' => $attribute
        ])->queryScalar();
        if ($value !== false) {
            $value = $value ? Constant::BOOLEAN_FALSE : Constant::BOOLEAN_TRUE;
            $db->createCommand()->update('{{%grid_column_config}}', ['visible' => $value], '[[user_id]] = :userId AND [[name]] = :name AND [[attribute]] = :attribute', [
                ':userId' => Yii::$app->getUser()->getId(),
                ':name' => $name,
                ':attribute' => $attribute
            ])->execute();
            $responseData = [
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
                'user_id' => Yii::$app->getUser()->getId(),
            ])->execute();

            $responseData = [
                'success' => true,
                'alias' => 'value',
                'data' => [
                    'value' => Constant::BOOLEAN_FALSE
                ],
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseData,
        ]);
    }

}
