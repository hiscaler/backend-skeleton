<?php

namespace app\models;

use yadjet\behaviors\FileUploadBehavior;
use yadjet\helpers\ArrayHelper;
use yadjet\helpers\TreeFormatHelper;
use Yii;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property string $sign
 * @property string $alias
 * @property string $name
 * @property string $short_name
 * @property integer $parent_id
 * @property integer $level
 * @property string $parent_ids
 * @property string $parent_names
 * @property string $icon
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
     * 全部的
     */
    const RETURN_TYPE_ALL = 'all';
    /**
     * 仅分配给个人的
     */
    const RETURN_TYPE_PRIVATE = 'private';
    /**
     * 公有的
     */
    const RETURN_TYPE_PUBLIC = 'public';

    private $_fileUploadConfig;

    public function init()
    {
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::className2Id(), 'icon');
        parent::init();
    }

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
            [['sign', 'alias', 'name', 'short_name', 'description'], 'trim'],
            [['parent_id', 'level', 'enabled', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['parent_id', 'level'], 'default', 'value' => 0],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => Constant::BOOLEAN_TRUE],
            [['description'], 'string'],
            [['alias'], 'string', 'max' => 120],
            ['alias', 'match', 'pattern' => '/^([a-z0-9\-]+[\/]{0,1})*[^\/]$/'],
            [['sign'], 'string', 'max' => 20],
            ['sign', 'match', 'pattern' => '/^[a-z]*[a-z]$/'],
            [['sign'], 'unique'],
            [['name', 'short_name'], 'string', 'max' => 30],
            [['parent_id'], 'checkParent'],
            [['parent_ids'], 'string', 'max' => 100],
            [['parent_names'], 'string', 'max' => 255],
            ['alias', 'unique', 'targetAttribute' => ['alias']],
            ['icon', 'file',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
            ],
        ];
    }

    /**
     * 验证上级分类有效性
     *
     * @param $attribute
     * @param $params
     */
    public function checkParent($attribute, $params)
    {
        if (!$this->isNewRecord && $this->parent_id == $this->id) {
            $this->addError('parent_id', '上级分类选择有误。');
        }
    }

    public function behaviors()
    {
        return [
            [
                'class' => FileUploadBehavior::className(),
                'attribute' => 'icon'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'sign' => Yii::t('category', 'Sign'),
            'alias' => Yii::t('category', 'Alias'),
            'name' => Yii::t('category', 'Name'),
            'short_name' => Yii::t('category', 'Short Name'),
            'parent_id' => Yii::t('category', 'Parent ID'),
            'level' => Yii::t('category', 'Level'),
            'parent_ids' => Yii::t('category', 'Parent Ids'),
            'parent_names' => Yii::t('category', 'Parent Names'),
            'icon' => Yii::t('category', 'Icon'),
            'description' => Yii::t('category', 'Description'),
            'ordering' => Yii::t('app', 'Ordering'),
        ]);
    }

    /**
     * 获取未整理的分类数据
     *
     * @param bool $tree
     * @return array
     * @throws \yii\db\Exception
     */
    private static function rawData($tree = true)
    {
        $items = Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]], [[name]], [[short_name]] AS [[shortName]], [[description]], [[parent_id]] AS [[parent]], [[level]], [[icon]], [[enabled]] FROM {{%category}} ORDER BY [[ordering]] ASC')->queryAll();

        return $tree ? ArrayHelper::toTree($items, 'id', 'parent') : $items;
    }

    /**
     * 获取分类展示树
     *
     * @param null|string $sign
     * @param string $returnType
     * @param null $enabled
     * @param bool $shortName
     * @return array
     * @throws \yii\db\Exception
     */
    public static function tree($sign = null, $returnType = self::RETURN_TYPE_PUBLIC, $enabled = null, $shortName = true)
    {
        $tree = [];
        $sign = trim($sign);
        $db = \Yii::$app->getDb();
        if ($sign) {
            $parentId = $db->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => $sign])->queryScalar();
        } else {
            $parentId = 0;
        }
        if ($categories = self::getChildren($parentId)) {
            // 数据过滤
            if ($returnType == self::RETURN_TYPE_PRIVATE || $enabled !== null) {
                if ($returnType == self::RETURN_TYPE_PRIVATE) {
                    $privateCategoryIds = $db->createCommand('SELECT [[category_id]] FROM {{%user_auth_category}} WHERE [[user_id]] = :userId', [':userId' => \Yii::$app->getUser()->getId()])->queryColumn();
                    if (empty($privateCategoryIds)) {
                        return [];
                    }
                }
                foreach ($categories as $key => $category) {
                    if ($returnType == self::RETURN_TYPE_PRIVATE && !in_array($category['id'], $privateCategoryIds)) {
                        unset($categories[$key]);
                    }
                    if ($enabled !== null && $category['enabled'] != $enabled) {
                        unset($categories[$key]);
                    }
                }
            }

            if ($categories) {
                $categories = TreeFormatHelper::dumpArrayTree(\yadjet\helpers\ArrayHelper::toTree($categories, 'id', 'parent'));
                foreach ($categories as $category) {
                    $tree[$category['id']] = $category['levelstr'] . ($shortName ? $category['shortName'] : $category['name']);
                }
            }
        }

        return $tree;
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

    private static function _getParents($items, $id)
    {
        $children = [];
        foreach ($items as $i => $item) {
            if ($item['id'] == $id) {
                $children[] = $item;
                unset($items[$i]);
                $children = array_merge($children, self::_getParents($items, $item['parent']));
            }
        }

        return $children;
    }

    /**
     * 获取所有父节点数据
     *
     * @param $id
     * @param bool $self
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getParents($id, $self = true)
    {
        $parents = self::_getParents(self::rawData(false), (int) $id);
        !$self && array_shift($parents);

        return $parents;
    }

    /**
     * 获取子节点数据
     *
     * @param $items
     * @param $parent
     * @return array
     */
    private static function _getChildren($items, $parent)
    {
        $children = [];
        foreach ($items as $i => $item) {
            if ($item['parent'] == $parent) {
                $children[] = $item;
                unset($items[$i]);
                $children = array_merge($children, self::_getChildren($items, $item['id']));
            }
        }

        return $children;
    }

    /**
     * 获取所有子节点数据
     *
     * @param int $parent
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getChildren($parent = 0)
    {
        return self::_getChildren(self::rawData(false), (int) $parent);
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
            empty($this->short_name) && $this->short_name = $this->name;
            if ($this->parent_id && strpos($this->alias, '/') === false) {
                $parentAlias = Yii::$app->getDb()->createCommand('SELECT [[alias]] FROM {{%category}} WHERE [[id]] = :id', [':id' => $this->parent_id])->queryScalar();
                $parentAlias && $this->alias = "$parentAlias/$this->alias";
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert && ($this->_alias != $this->alias)) {
            // 更新子栏目别名数据
            $children = self::getChildren($this->id);
            if ($children) {
                $cmd = Yii::$app->getDb()->createCommand();
                foreach ($children as $child) {
                    $childAlias = explode('/', $child['alias']);
                    foreach (explode('/', $this->alias) as $key => $value) {
                        $childAlias[$key] = $value;
                    }
                    $alias = implode('/', $childAlias);
                    $cmd->update('{{%category}}', ['alias' => $alias], ['id' => $child['id']])->execute();
                }
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        \Yii::$app->getDb()->createCommand()->delete('{{%user_auth_category}}', ['category_id' => $this->id])->execute();
    }

}
