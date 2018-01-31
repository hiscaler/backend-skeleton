<?php

namespace app\models;

use yadjet\behaviors\FileUploadBehavior;
use yadjet\helpers\ArrayHelper;
use yadjet\helpers\TreeFormatHelper;
use Yii;
use yii\db\Query;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%category}}".
 *
 * @property integer $id
 * @property string $module_name
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
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::className2Id(), 'icon_path');
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
            [['module_name', 'name', 'ordering'], 'required'],
            [['alias', 'name', 'description'], 'trim'],
            [['parent_id', 'level', 'enabled', 'ordering', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['parent_id', 'level'], 'default', 'value' => 0],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => Constant::BOOLEAN_TRUE],
            [['description'], 'string'],
            [['alias'], 'string', 'max' => 120],
            ['alias', 'match', 'pattern' => '/^[a-z]+[a-z-\/]*[a-z]$/'],
            [['module_name'], 'string', 'max' => 20],
            [['module_name'], 'checkModuleName'],
            [['name'], 'string', 'max' => 30],
            [['parent_id'], 'checkParent'],
            [['parent_ids'], 'string', 'max' => 100],
            [['parent_names'], 'string', 'max' => 255],
            ['alias', 'unique', 'targetAttribute' => ['alias']],
            ['icon_path', 'file',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
            ],
        ];
    }

    /**
     * 验证模块名称有效性
     *
     * @param $attribute
     * @param $params
     * @throws \yii\db\Exception
     */
    public function checkModuleName($attribute, $params)
    {
        if ($this->module_name && $this->parent_id == 0) {
            $count = \Yii::$app->getDb()->createCommand('SELECT COUNT(*) FROM {{%category}} WHERE [[module_name]] = :moduleName AND [[parent_id]] = 0', [':moduleName' => $this->module_name])->queryScalar();
            if ($count && ($this->isNewRecord || (!$this->isNewRecord) && $count > 1)) {
                $this->addError('module_name', $this->module_name . ' 已经启用分类。');
            }
        }
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
                'attribute' => 'icon_path'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'module_name' => Yii::t('category', 'Module Name'),
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
     * 生成数据缓存
     */
    private static function rawData($tree = true)
    {
        $items = [];
        $rawData = Yii::$app->getDb()->createCommand('SELECT [[id]], [[alias]], [[name]], [[description]], [[parent_id]], [[level]], [[icon_path]], [[enabled]] FROM {{%category}}')->queryAll();
        foreach ($rawData as $data) {
            $items[$data['id']] = [
                'id' => $data['id'],
                'alias' => $data['alias'],
                'name' => $data['name'],
                'description' => $data['description'],
                'parent' => $data['parent_id'],
                'level' => $data['level'],
                'icon' => $data['icon_path'],
                'enabled' => $data['enabled'] ? true : false,
            ];
        }

        return $tree ? ArrayHelper::toTree($items, 'id', 'parent') : $items;
    }

    /**
     * 获取分类展示树
     *
     * @param string $returnType
     * @param null $enabled
     * @return array
     */
    public static function tree($returnType = self::RETURN_TYPE_PUBLIC, $enabled = null)
    {
        $tree = [];
        $moduleName = Yii::$app->controller->module->id;
        if ($returnType != self::RETURN_TYPE_ALL && !$moduleName) {
            return $tree;
        }

        $where = [];
        if ($returnType != self::RETURN_TYPE_ALL) {
            $where['module_name'] = $moduleName;
            if ($enabled !== null) {
                $where['enabled'] = boolval($enabled) ? Constant::BOOLEAN_TRUE : Constant::BOOLEAN_FALSE;
            }
            if ($returnType == self::RETURN_TYPE_PRIVATE) {
                $where = ['AND', $where, ['IN', 'id', (new Query())->select('category_id')->from(' {{%user_auth_category}}')->where(['user_id' => \Yii::$app->getUser()->getId()])]];
            }
        }

        $categories = (new Query())
            ->select(['id', 'name', 'parent_id'])
            ->from('{{%category}}')
            ->where($where)
            ->all();

        if ($categories) {
            $categories = TreeFormatHelper::dumpArrayTree(\yadjet\helpers\ArrayHelper::toTree($categories, 'id'));
            foreach ($categories as $category) {
                $tree[$category['id']] = "{$category['levelstr']} {$category['name']}";
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
     * @param mixed|integer $parent
     * @return array
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
    private $_moduleName;

    public function afterFind()
    {
        parent::afterFind();
        $this->_alias = $this->alias;
        $this->_moduleName = $this->module_name;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (empty($this->alias) && !empty($this->name)) {
                $this->alias = Inflector::slug($this->name);
            }
            if ($this->parent_id) {
                $parent = Yii::$app->getDb()->createCommand('SELECT [[module_name]], [[alias]] FROM {{%category}} WHERE [[id]] = :id', [':id' => $this->parent_id])->queryOne();
                $this->module_name = $parent['module_name'];
                if (strpos($this->alias, '/') === false) {
                    $this->alias = "{$parent['alias']}/{$this->alias}";
                }
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert && ($this->_alias != $this->alias || $this->_moduleName != $this->module_name)) {
            // 更新子栏目别名
            $children = self::getChildren($this->id);
            if ($children) {
                $db = Yii::$app->getDb();
                $cmd = $db->createCommand();
                if ($this->_alias != $this->alias) {
                    foreach ($children as $child) {
                        $childAlias = explode('/', $child['alias']);
                        foreach (explode('/', $this->alias) as $key => $value) {
                            $childAlias[$key] = $value;
                        }
                        $alias = implode('/', $childAlias);
                        $cmd->update('{{%category}}', ['alias' => $alias], ['id' => $child['id']])->execute();
                    }
                }
                if ($this->_moduleName != $this->module_name) {
                    $cmd->update('{{%category}}', ['module_name' => $this->module_name], ['id' => \yii\helpers\ArrayHelper::getColumn($children, 'id')])->execute();
                }
            }
        }
    }

}
