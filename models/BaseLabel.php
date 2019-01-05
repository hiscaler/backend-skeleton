<?php

namespace app\models;

use Yii;
use yii\db\Query;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%label}}".
 *
 * @property integer $id
 * @property string $alias
 * @property string $name
 * @property integer $frequency
 * @property integer $enabled
 * @property integer $ordering
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_by
 * @property integer $updated_at
 */
class BaseLabel extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%label}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DELETE => self::OP_DELETE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ordering'], 'required'],
            ['alias', 'match', 'pattern' => '/^[a-z]+[.]?[a-z-]+[a-z]$/'],
            ['alias', 'unique', 'targetAttribute' => ['alias']],
            [['enabled'], 'boolean'],
            [['frequency', 'ordering', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['alias', 'name'], 'trim'],
            [['alias', 'name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'name' => Yii::t('label', 'Name'),
            'frequency' => Yii::t('label', 'Frequency'),
        ]);
    }

    /**
     * 获取自定义属性列表
     *
     * @param boolean $all 是否查询出所有数据
     * @param boolean $group 是否分组
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getItems($all = false, $group = false)
    {
        $items = [];
        $sql = 'SELECT [[id]], [[alias]], [[name]] FROM {{%label}}';
        $params = [];
        if (!$all) {
            $sql .= ' WHERE [[enabled]] = :enabled';
            $params[':enabled'] = Constant::BOOLEAN_TRUE;
        }
        $sql .= ' ORDER BY [[alias]] ASC, [[ordering]] ASC';
        $rawData = Yii::$app->getDb()->createCommand($sql, $params)->queryAll();
        foreach ($rawData as $data) {
            if ($group) {
                if (($index = strpos($data['alias'], '.')) !== false) {
                    $groupPrefix = substr($data['alias'], 0, $index);
                } else {
                    $groupPrefix = '*';
                }

                $items[$groupPrefix][$data['id']] = "{$data['alias']}: {$data['name']}";
            } else {
                $items[$data['id']] = "{$data['alias']}: {$data['name']}";
            }
        }

        return $items;
    }

    /**
     * 根据实体编号和实体名称获取关联的自定义属性列表
     *
     * @param integer $entityId
     * @param string $modelName
     * @return array
     */
    public static function getEntityItems($entityId, $modelName)
    {
        $items = [];
        $rawItems = (new Query())
            ->select(['a.id', 'a.name'])
            ->from('{{%entity_label}} t')
            ->leftJoin('{{%label}} a', '[[t.label_id]] = [[a.id]]')
            ->where([
                't.entity_id' => (int) $entityId,
                't.model_name' => trim($modelName)
            ])
            ->all();

        foreach ($rawItems as $item) {
            $items[$item['id']] = $item['name'];
        }

        return $items;
    }

    /**
     * 根据实体编号和实体名称获取关联的自定义属性内容（文本）
     *
     * @param integer $entityId
     * @param string $entityName
     * @return string
     */
    public static function getEntityItemSentence($entityId, $entityName)
    {
        $sentence = Inflector::sentence(static::getEntityItems($entityId, $entityName), '、', null, '、');
        if (!empty($sentence)) {
            $sentence = "<span class=\"labels\">{$sentence}</span>";
        }

        return $sentence;
    }

    /**
     * 根据实体编号和实体名称获取关联的自定义属性编号列表
     *
     * @param integer $entityId
     * @param string $modelName
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getEntityLabelIds($entityId, $modelName)
    {
        return Yii::$app->getDb()->createCommand('SELECT [[label_id]] FROM {{%entity_label}} WHERE [[entity_id]] = :entityId AND [[model_name]] = :modelName', [':entityId' => (int) $entityId, ':modelName' => $modelName])->queryColumn();
    }

    /**
     * 根据自定义属性 id 和 模型名称获取关联的数据 id
     *
     * @param integer $labelId
     * @param string $entityName
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getEntityIds($labelId, $entityName)
    {
        return Yii::$app->getDb()->createCommand('SELECT [[entity_id]] FROM {{%entity_label}} WHERE [[label_id]] = :labelId AND [[model_name]] = :modelName', [':labelId' => (int) $labelId, ':modelName' => $entityName])->queryColumn();
    }

    // Events

    /**
     * @return bool
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if ($this->getIsNewRecord()) {
                $this->frequency = 0;
            }
            if (empty($this->alias) && !empty($this->name)) {
                $alias = [];
                foreach (explode('-', Inflector::slug($this->name)) as $slug) {
                    $alias[] = $slug[0];
                }
                $this->alias = implode('', $alias);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->getDb()->createCommand()->delete('{{%entity_label}}', ['label_id' => $this->id])->execute();
    }

}
