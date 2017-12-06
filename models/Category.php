<?php

namespace app\models;

use yadjet\helpers\TreeFormatHelper;
use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $alias
 * @property string $name
 * @property integer $parent_id
 * @property integer $level
 * @property string $parent_ids
 * @property string $parent_names
 * @property string $icon_path
 * @property string $description
 * @property integer $enabled
 * @property integer $ordering
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Category extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ordering'], 'required'],
            [['type', 'parent_id', 'level', 'enabled', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['type', 'parent_id', 'level'], 'default', 'value' => 0],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => Constant::BOOLEAN_TRUE],
            [['description'], 'string'],
            [['alias'], 'string', 'max' => 120],
            ['alias', 'match', 'pattern' => '/^[a-z]+[a-z-\/]+[a-z]$/'],
            [['name'], 'string', 'max' => 30],
            [['parent_ids', 'icon_path'], 'string', 'max' => 100],
            [['parent_names'], 'string', 'max' => 255],
            ['alias', 'unique', 'targetAttribute' => ['alias']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type' => Yii::t('category', 'Type'),
            'alias' => Yii::t('category', 'Alias'),
            'name' => Yii::t('category', 'Name'),
            'parent_id' => Yii::t('category', 'Parent ID'),
            'level' => Yii::t('category', 'Level'),
            'parent_ids' => Yii::t('category', 'Parent Ids'),
            'parent_names' => Yii::t('category', 'Parent Names'),
            'icon_path' => Yii::t('category', 'Icon'),
            'description' => Yii::t('category', 'Description'),
            'ordering' => Yii::t('app', 'Ordering'),
        ]);
    }

    /**
     * 类别选项
     *
     * @return array
     */
    public static function typeOptions()
    {
        return Lookup::getValue('system.models.category.type', [], 'array');
    }

    /**
     * 生成数据缓存
     */
    public static function generateCache($toTree = false)
    {
        $items = [];
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[id]], [[type]], [[alias]], [[name]], [[parent_id]], [[icon_path]], [[enabled]] FROM {{%category}} ORDER BY [[level]] ASC')->queryAll();
        foreach ($rawData as $data) {
            $items[$data['id']] = [
                'id' => $data['id'],
                'type' => $data['type'],
                'alias' => $data['alias'],
                'name' => $data['name'],
                'parent' => $data['parent_id'],
                'icon' => $data['icon_path'],
                'enabled' => $data['enabled'] ? true : false,
                'hasChildren' => false
            ];
            if ($data['parent_id'] && isset($items[$data['parent_id']])) {
                $items[$data['parent_id']]['hasChildren'] = true;
            }
        }
        $cache = Yii::$app->getCache();
        $cache->set('__category_items_common_', $items);
        if ($toTree) {
            $items = \yadjet\helpers\ArrayHelper::toTree($items, 'id', 'parent', 'children');
            $cache->set('__category_items_tree_', $items);
        }

        return $items;
    }

    /**
     * 处理并生成分类数据缓存，供后续代码调取
     *
     * @param boolean $toTree
     * @return array
     */
    private static function getRawItems($toTree = false)
    {
        $key = '__category_items';
        $key = $toTree ? '_tree_' : '_common_';
        $cache = Yii::$app->getCache();
        $items = $cache->get($key);
        if ($items === false) {
            $items = self::generateCache($toTree);
        }

        return $items;
    }

    private static function getRawItemsByType($type = 0, $all = false, $toTree = false)
    {
        $items = [];
        foreach (self::getRawItems($toTree) as $key => $item) {
            if ($item['type'] == $type) {
                if ($all || $item['enabled']) {
                    $items[$key] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * 获取分类项目
     *
     * @param integer $type
     * @param mixed $prompt
     * @param boolean $all
     * @return string
     */
    public static function getTree($type, $prompt = null, $all = false)
    {
        $items = [];
        if ($prompt) {
            $items[] = $prompt;
        }
        $rawData = self::getRawItemsByType($type, $all, true);
        if ($rawData) {
            $rawData = TreeFormatHelper::dumpArrayTree($rawData);
            foreach ($rawData as $data) {
                $items[$data['id']] = $data['levelstr'] . $data['name'];
            }
        }

        return $items;
    }

    /**
     * 获取用户可操作分类项目
     *
     * @param integer $type
     * @param mixed $prompt
     * @param boolean $all
     * @param mixed|integer $userId
     * @return string
     */
    public static function getOwnerTree($type, $prompt = null, $all = false, $userId = null)
    {
        $items = [];
        if ($prompt) {
            $items[] = $prompt;
        }
        $rawData = self::getRawItemsByType($type, $all, false);
        $ownerCategoryIds = Yii::$app->getDb()->createCommand('SELECT [[category_id]] FROM {{%user_auth_category}} WHERE [[user_id]] = :userId', [':userId' => $userId ?: Yii::$app->getUser()->getId()])->queryColumn();
        if ($ownerCategoryIds) {
            foreach ($rawData as $key => $data) {
                if (!in_array($data['id'], $ownerCategoryIds)) {
                    unset($rawData[$key]);
                }
            }
            if ($rawData) {
                $rawData = TreeFormatHelper::dumpArrayTree(\yadjet\helpers\ArrayHelper::toTree($rawData, 'id', 'parent'));
                foreach ($rawData as $data) {
                    $items[$data['id']] = $data['levelstr'] . $data['name'];
                }
            }
        }

        return $items;
    }

    public static function sortItems($tree)
    {
        $ret = [];
        if (isset($tree['children']) && is_array($tree['children'])) {
            $children = $tree['children'];
            unset($tree['children']);
            $ret[] = $tree;
            foreach ($children as $child) {
                $ret = array_merge($ret, self::sortItems($child, 'children'));
            }
        } else {
            unset($tree['children']);
            $ret[] = $tree;
        }

        return $ret;
    }

    /**
     * 获取所有父节点数据
     *
     * @param mixed|integer $id
     * @return array
     */
    public static function getParents($id)
    {
        $parents = [];
        $row = Yii::$app->getDb()->createCommand('SELECT * FROM {{%category}} WHERE [[id]] = :id', [':id' => $id])->queryOne();
        $parents[] = $row;
        if ($row['parent_id']) {
            $parents = array_merge($parents, static::getParents($row['parent_id']));
        }

        return ArrayHelper::sortByCol($parents, 'parent_id');
    }

    /**
     * 判断是否有子节点
     *
     * @param integer $id
     * @return boolean
     */
    private static function hasChildren($id)
    {
        $rawData = self::getRawItems();

        return isset($rawData[$id]) && $rawData[$id]['hasChildren'];
    }

    /**
     * 获取所有子节点数据
     *
     * @param mixed|integer $parent
     * @return array
     */
    public static function getChildren($parent = 0)
    {
        $children = [];
        $parent = (int) $parent;
        $rawItems = self::getRawItems(true);
        if ($parent) {
            foreach ($rawItems as $item) {
                if ($item['id'] == $parent) {
                    if ($item['hasChildren'] && $item['children']) {
                        foreach ($item['children'] as $child) {
                            $children = array_merge($children, \yadjet\helpers\ArrayHelper::treeToArray($child));
                        }
                        break;
                    }
                }
            }
        }

        return $children;
    }

    /**
     * 获取所有子节点 id 集合
     *
     * @param mixed|integer $parent
     * @return array
     */
    public static function getChildrenIds($parent = 0)
    {
        $ids = [];
        foreach (self::getChildren($parent) as $child) {
            $ids[] = (int) $child['id'];
        }

        return $ids;
    }

    // 事件
    private $_alias;

    public function afterFind()
    {
        parent::afterFind();
        $this->_alias = $this->alias;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->alias) && !empty($this->name)) {
                $this->alias = Inflector::slug($this->name);
            }
            if ($this->parent_id && strpos($this->alias, '/') === false) {
                $parentAlias = Yii::$app->getDb()->createCommand('SELECT [[alias]] FROM {{%category}} WHERE [[id]] = :id', [':id' => $this->parent_id])->queryScalar();
                $this->alias = "{$parentAlias}/{$this->alias}";
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        self::generateCache();
        if (!$insert && $this->_alias != $this->alias) {
            // 更新子栏目别名
            $childrenIds = self::getChildrenIds($this->id);

            if ($childrenIds) {
                $db = Yii::$app->getDb();
                $cmd = $db->createCommand();
                $children = $db->createCommand('SELECT [[id]], [[alias]] FROM {{%category}} WHERE [[id]] IN (' . implode(', ', $childrenIds) . ')')->queryAll();
                foreach ($children as $child) {
                    $prefix = $this->parent_id ? '/' : '';
                    /* @todo 需要验证子栏目雷同的名称如何处理 */
                    $alias = str_replace($prefix . $this->_alias . '/', $prefix . $this->alias . '/', $child['alias']);
                    $cmd->update('{{%category}}', ['alias' => $alias], ['id' => $child['id']])->execute();
                }
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        self::generateCache();
    }

}
