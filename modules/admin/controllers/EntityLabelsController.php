<?php

namespace app\modules\admin\controllers;

use app\models\Attribute;
use app\models\Constant;
use app\models\Label;
use app\modules\admin\components\ApplicationHelper;
use PDO;
use Yii;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\Exception;
use yii\db\Query;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 数据推送位管理
 * Class EntityLabelsController
 *
 * @package app\modules\admin\controllers
 * @author hiscaler <hiscaler@gmail.com>
 */
class EntityLabelsController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'entities', 'set', 'delete', 'toggle'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                    'toggle' => ['post'],
                    'set-entity-label' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 查看实体记录自定义属性
     *
     * @param integer $entityId
     * @param string $modelName
     * @return mixed
     */
    public function actionIndex($entityId, $modelName)
    {
        if (Yii::$app->getRequest()->isAjax) {
            $attributes = [];
            $groups = [];
            $entityLabels = Label::getEntityItems($entityId, ApplicationHelper::id2ClassName($modelName));
            $rawData = Label::getItems(true);
            foreach ($rawData as $key => $value) {
                if (strpos($value, '.') !== false) {
                    $groupName = current(explode('.', $value));
                    $groups[] = $groupName;
                    $attributes[$groupName][] = [
                        'id' => $key,
                        'name' => $value,
                        'entityId' => $entityId,
                        'modelName' => $modelName,
                        'enabled' => isset($entityLabels[$key])
                    ];
                } else {
                    $groups['*'] = Yii::t('app', 'Common Attributes List');
                    $attributes[$groups['*']][] = [
                        'id' => $key,
                        'name' => $value,
                        'entityId' => $entityId,
                        'modelName' => $modelName,
                        'enabled' => isset($entityLabels[$key])
                    ];
                }
            }
            $dataProviders = [];
            foreach ($groups as $group) {
                $dataProviders[$group] = new ArrayDataProvider([
                    'allModels' => $attributes[$group],
                    'pagination' => [
                        'pageSize' => 10,
                    ],
                ]);
            }

            return $this->renderAjax('index', [
                'dataProviders' => $dataProviders,
            ]);
        } else {
            throw new BadRequestHttpException('Bad Request.');
        }
    }

    /**
     * Deletes an existing Attribute model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     *
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        Yii::$app->getDb()->createCommand()->delete('{{%entity_label}}', ['id' => (int) $id])->execute();

        return $this->redirect(Yii::$app->getRequest()->referrer);
    }

    /**
     * 激活禁止操作
     *
     * @return Response
     */
    public function actionToggle()
    {
        $id = Yii::$app->getRequest()->post('id');
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand('SELECT [[enabled]] FROM {{%entity_label}} WHERE [[id]] = :id');
        $command->bindValue(':id', (int) $id, PDO::PARAM_INT);
        $value = $command->queryScalar();
        if ($value !== null) {
            $value = !$value;
            $connection->createCommand()->update('{{%entity_label}}', ['enabled' => $value], '[[id]] = :id', [':id' => (int) $id])->execute();
            $responseData = [
                'success' => true,
                'data' => [
                    'value' => $value
                ],
            ];
        } else {
            $responseData = [
                'success' => false,
                'error' => [
                    'message' => '数据有误',
                ],
            ];
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseData,
        ]);
    }

    /**
     * 添加或者删除实体数据自定义属性
     *
     * @return Response
     * @throws Exception
     */
    public function actionSet()
    {
        $request = Yii::$app->getRequest();
        $entityId = (int) $request->post('entityId');
        $modelName = trim($request->post('modelName'));
        $labelId = (int) $request->post('labelId');
        if (!$entityId || empty($modelName) || !$labelId) {
            $responseData = [
                'success' => false,
                'error' => [
                    'message' => '提交参数有误'
                ]
            ];
        } else {
            $modelName = ApplicationHelper::id2ClassName($modelName);
            $db = Yii::$app->getDb();
            $entityEnabled = $db->createCommand('SELECT [[enabled]] FROM {{%label}} WHERE [[id]] = :id', [':id' => $labelId])->queryScalar();
            if ($entityEnabled === false) {
                $responseData = [
                    'success' => false,
                    'error' => [
                        'message' => '该推送位不存在。'
                    ]
                ];
            } else {
                $id = $db->createCommand('SELECT [[id]] FROM {{%entity_label}} WHERE [[entity_id]] = :entityId AND [[model_name]] = :modelName AND [[label_id]] = :labelId', [
                    ':entityId' => $entityId,
                    ':modelName' => $modelName,
                    ':labelId' => $labelId,
                ])->queryScalar();
                $transaction = $db->beginTransaction();
                try {
                    if ($id) {
                        // Delete it
                        $db->createCommand()->delete('{{%entity_label}}', '[[id]] = :id', [':id' => $id])->execute();
                        // Update attribute frequency count
                        $db->createCommand('UPDATE {{%label}} SET [[frequency]] = [[frequency]] - 1 WHERE [[id]] = :id', [':id' => $labelId])->execute();
                        $responseData = [
                            'success' => true,
                            'data' => [
                                'value' => Constant::BOOLEAN_FALSE
                            ]
                        ];
                    } else {
                        // Add it
                        $now = time();
                        $userId = Yii::$app->getUser()->getId();
                        $db->createCommand()->insert('{{%entity_label}}', [
                            'entity_id' => $entityId,
                            'model_name' => $modelName,
                            'label_id' => $labelId,
                            'enabled' => $entityEnabled,
                            'ordering' => Label::DEFAULT_ORDERING_VALUE,
                            'created_at' => $now,
                            'created_by' => $userId,
                            'updated_at' => $now,
                            'updated_by' => $userId,
                        ])->execute();
                        // Update attribute frequency count
                        $db->createCommand('UPDATE {{%label}} SET [[frequency]] = [[frequency]] + 1 WHERE [[id]] = :id')->bindValue(':id', $labelId, PDO::PARAM_INT)->execute();
                        $responseData = [
                            'success' => true,
                            'data' => [
                                'value' => Constant::BOOLEAN_TRUE
                            ]
                        ];
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                    $responseData = [
                        'success' => false,
                        'error' => [
                            'message' => $e
                        ],
                    ];
                }
            }
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseData,
        ]);
    }

    /**
     * 自定义属性关联的实体数据
     *
     * @param string $modelName
     * @return ActiveDataProvider
     */
    public function actionEntities($modelName, $labelId = null)
    {
        $object = Yii::createObject($modelName);
        // 自定义属性名称
        $labels = [];
        $attributesRawData = (new Query)->select(['a.id', 'a.alias', 'a.name'])
            ->distinct(true)
            ->from(['{{%entity_label}} t'])
            ->leftJoin($object::tableName() . ' e', 't.entity_id = e.id')
            ->leftJoin('{{%label}} a', 't.label_id = a.id')
            ->where([
                't.entity_name' => $modelName,
            ])
            ->all();
        foreach ($attributesRawData as $attribute) {
            $labels[$attribute['id']] = [
                'alias' => $attribute['alias'],
                'name' => $attribute['name']
            ];
        }

        // 关联数据
        $select = ['t.id', 't.entity_id', 't.ordering', 't.enabled'];
        switch ($modelName) {
            case 'app-models-Product':
                $title = 'name';
                break;
            default:
                $title = 'title';
                break;
        }
        $select[] = "e.{$title} AS title";
        $query = (new Query)->select($select)->from(['{{%entity_label}} t'])
            ->leftJoin($object::tableName() . ' e', 't.entity_id = e.id')
            ->where([
                't.entity_name' => $modelName,
            ])->orderBy(['ordering' => SORT_ASC]);
        if (!empty($labelId)) {
            $query->andWhere('t.label_id = :labelId', [':labelId' => (int) $labelId]);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'key' => 'id',
        ]);

        return $this->render('entities', [
            'modelName' => $modelName,
            'labelId' => $labelId,
            'labels' => $labels,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * Finds the Attribute model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     *
     * @param integer $id
     * @return Attribute the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = Yii::$app->getDb()->createCommand('SELECT * FROM {{%entity_label}} WHERE [[id]] = :id')->bindValue(':id', (int) $id, PDO::PARAM_INT)->queryOne();
        if ($model !== false) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
