<?php

namespace app\modules\admin\controllers;

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
                        'actions' => ['index', 'entities', 'set'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'set' => ['post'],
                ],
            ],
        ];
    }

    /**
     * 查看数据推送位
     *
     * @rbacDescription 查看数据推送位权限
     *
     * @param integer $entityId
     * @param string $modelName
     * @return mixed
     * @throws BadRequestHttpException
     * @throws Exception
     */
    public function actionIndex($entityId, $modelName)
    {
        if (Yii::$app->getRequest()->getIsAjax()) {
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
                    $groups['*'] = Yii::t('app', 'Common Labels List');
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
     * 添加或者删除数据推送位
     *
     * @rbacDescription 添加或者删除数据推送位权限
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
            $responseBody = [
                'success' => false,
                'error' => [
                    'message' => '提交参数有误。'
                ]
            ];
        } else {
            $modelName = ApplicationHelper::id2ClassName($modelName);
            $db = Yii::$app->getDb();
            $entityEnabled = $db->createCommand('SELECT [[enabled]] FROM {{%label}} WHERE [[id]] = :id', [':id' => $labelId])->queryScalar();
            if ($entityEnabled === false) {
                $responseBody = [
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
                        $responseBody = [
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
                        $responseBody = [
                            'success' => true,
                            'data' => [
                                'value' => Constant::BOOLEAN_TRUE
                            ]
                        ];
                    }
                    $transaction->commit();
                } catch (Exception $e) {
                    $transaction->rollback();
                    $responseBody = [
                        'success' => false,
                        'error' => [
                            'message' => $e->getMessage()
                        ],
                    ];
                }
            }
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

    /**
     * 自定义属性关联的实体数据
     *
     * @rbacDescription 查看自定义属性关联的实体数据权限
     * @param string $modelName
     * @param null $labelId
     * @return ActiveDataProvider
     * @throws \yii\base\InvalidConfigException
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

}
