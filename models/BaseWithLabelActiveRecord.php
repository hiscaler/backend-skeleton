<?php

namespace app\models;

use Exception;
use Yii;
use yii\db\Expression;
use yii\web\HttpException;

/**
 * Class BaseWithLabelActiveRecord
 *
 * @package app\models
 * @author hiscaler <hiscaler@gmail.com>
 */
class BaseWithLabelActiveRecord extends BaseActiveRecord
{

    private $_oldEntityLabels = [];

    /**
     * @var array 推送位
     */
    public $entityLabels = [];

    public function rules()
    {
        $rules = [
            [['entityLabels'], 'safe'],
        ];

        return array_merge(parent::rules(), $rules);
    }

    /**
     * 数据关联的推送位
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getRelatedLabels()
    {
        return $this->hasMany(Label::class, ['id' => 'label_id'])
            ->select(['id', 'name'])
            ->viaTable('{{%entity_label}}', ['entity_id' => 'id'], function ($query) {
                /* @var $query yii\db\Query */
                $query->where(['model_name' => static::class]);
            });
    }

    /**
     * 自定义推送位数据
     *
     * @return \yii\db\ActiveQuery
     * @throws \yii\base\InvalidConfigException
     */
    public function getCustomLabels()
    {
        return $this->hasMany(Label::class, ['id' => 'label_id'])
            ->select(['id', 'name'])
            ->viaTable('{{%entity_label}}', ['entity_id' => 'id'], function ($query) {
                /* @var $query yii\db\Query */
                $query->where(['model_name' => static::class]);
            });
    }

    public function attributeLabels()
    {
        return [
            'entityLabels' => Yii::t('app', 'Entity Labels'),
        ];
    }

    // Events

    /**
     * @throws Exception
     */
    public function afterFind()
    {
        parent::afterFind();
        if (!$this->isNewRecord) {
            $this->entityLabels = Label::getEntityLabelIds($this->getPrimaryKey(), static::class);
            $this->_oldEntityLabels = $this->entityLabels;
        }
    }

    /**
     * @param $insert
     * @param $changedAttributes
     * @throws HttpException
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        // Entity labels
        $entityLabels = $this->entityLabels;
        if (!is_array($this->_oldEntityLabels)) {
            $this->_oldEntityLabels = [];
        }
        if (!is_array($entityLabels)) {
            $entityLabels = [];
        }

        if ($insert) {
            $insertLabels = $entityLabels;
            $deleteLabels = [];
        } else {
            if ($entityLabels) {
                $insertLabels = array_diff($entityLabels, $this->_oldEntityLabels);
                $deleteLabels = array_diff($this->_oldEntityLabels, $entityLabels);
            } else {
                $insertLabels = [];
                $deleteLabels = $this->_oldEntityLabels;
            }
        }

        if ($insertLabels || $deleteLabels) {
            $db = Yii::$app->getDb();
            $cmd = $db->createCommand();
            $transaction = $db->beginTransaction();
            try {
                $primaryKeyValue = $this->getPrimaryKey();
                // Insert data
                if ($insertLabels) {
                    $rows = [];
                    $userId = Yii::$app->getUser()->getId() ?: 0;
                    $now = time();
                    foreach ($insertLabels as $labelId) {
                        $rows[] = [$primaryKeyValue, static::class, $labelId, Constant::BOOLEAN_TRUE, static::DEFAULT_ORDERING_VALUE, $userId, $now, $userId, $now];
                    }
                    if ($rows) {
                        $cmd->batchInsert('{{%entity_label}}', ['entity_id', 'model_name', 'label_id', 'enabled', 'ordering', 'created_by', 'created_at', 'updated_by', 'updated_at'], $rows)->execute();
                        $cmd->update('{{%label}}', [
                            'frequency' => new Expression('frequency + 1')
                        ], ['id' => $insertLabels])->execute();
                    }
                }
                // Delete data
                if ($deleteLabels) {
                    $cmd->delete('{{%entity_label}}', [
                        'entity_id' => $primaryKeyValue,
                        'model_name' => static::class,
                        'label_id' => $deleteLabels
                    ])->execute();
                    $cmd->update('{{%label}}', [
                        'frequency' => new Expression('frequency - 1')
                    ], ['id' => $deleteLabels])->execute();
                }
                $transaction->commit();
            } catch (Exception $e) {
                $transaction->rollback();
                throw new HttpException('500', $e->getMessage());
            }
        }
    }

    /**
     * @throws \Throwable
     */
    public function afterDelete()
    {
        parent::afterDelete();
        // Delete label relation data and update label frequency value
        Yii::$app->getDb()->transaction(function ($db) {
            /* @var $db \yii\db\Connection */
            $labels = $db->createCommand('SELECT [[id]], [[label_id]] FROM {{%entity_label}} WHERE [[entity_id]] = :entityId AND [[model_name]] = :modelName', [
                ':entityId' => $this->getPrimaryKey(),
                ':modelName' => static::class
            ])->queryAll();
            if ($labels) {
                $ids = $labelIds = [];
                foreach ($labels as $label) {
                    $ids[] = $label['id'];
                    $labelIds[] = $label['label_id'];
                }
                $cmd = $db->createCommand();
                $cmd->delete('{{%entity_label}}', ['id' => $ids])->execute();
                $cmd->update('{{%label}}', [
                    'frequency' => new Expression('frequency - 1')
                ], ['id' => $labelIds])->execute();
            }
        });
    }

}
