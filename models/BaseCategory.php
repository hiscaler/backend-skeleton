<?php

namespace app\models;

use yadjet\behaviors\FileUploadBehavior;
use yadjet\helpers\ArrayHelper;
use yadjet\helpers\TreeFormatHelper;
use Yii;
use yii\caching\DbDependency;
use yii\helpers\FileHelper;
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
 * @property string $id_path
 * @property string $name_path
 * @property string $icon
 * @property string $description
 * @property integer $enabled
 * @property integer $ordering
 * @property integer $quantity
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class BaseCategory extends BaseActiveRecord
{

    /**
     * @var string 文件上传字段
     */
    public $fileFields = 'icon';

    /**
     * 仅分配给个人的
     */
    const RETURN_TYPE_PRIVATE = 'private';

    /**
     * 公有的
     */
    const RETURN_TYPE_PUBLIC = 'public';

    const PATH_TYPE_ID = 'id';
    const PATH_TYPE_NAME = 'name';
    const PATH_TYPE_ID_NAME = 'id-name';

    private $_fileUploadConfig;

    /**
     * @throws \yii\db\Exception
     */
    public function init()
    {
        parent::init();
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::class, 'icon');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'ordering'], 'required'],
            [['sign', 'alias', 'name', 'short_name', 'description'], 'trim'],
            [['parent_id', 'level', 'enabled', 'ordering', 'quantity', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['parent_id', 'level', 'quantity'], 'default', 'value' => 0],
            [['enabled'], 'boolean'],
            [['enabled'], 'default', 'value' => Constant::BOOLEAN_TRUE],
            [['description'], 'string'],
            [['alias'], 'string', 'max' => 120],
            ['alias', 'match', 'pattern' => '/^([a-z0-9\-]+[\/]{0,1})*[^\/]$/'],
            [['sign'], 'string', 'max' => 40],
            ['sign', 'match', 'pattern' => '/^[a-z]*[a-z\.]*[a-z]$/'],
            [['sign'], 'unique'],
            [['name', 'short_name'], 'string', 'max' => 30],
            [['parent_id'], 'checkParent'],
            [['id_path'], 'string', 'max' => 100],
            [['name_path'], 'string', 'max' => 255],
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
                'class' => FileUploadBehavior::class,
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
            'id_path' => Yii::t('category', 'ID Path'),
            'name_path' => Yii::t('category', 'Name Path'),
            'icon' => Yii::t('category', 'Icon'),
            'description' => Yii::t('category', 'Description'),
            'ordering' => Yii::t('app', 'Ordering'),
            'quantity' => Yii::t('category', 'Quantity'),
        ]);
    }

    /**
     * 获取未整理的分类数据
     *
     * @param bool $toTree
     * @return array
     * @throws \yii\db\Exception
     */
    private static function rawData($toTree = true)
    {
        $cacheKey = 'app.models.category.rawData.' . (int) $toTree;
        $cache = Yii::$app->getCache();
        $items = $cache->get($cacheKey);
        if ($items === false) {
            $url = Yii::$app->getRequest()->getHostInfo();
            $items = Yii::$app->getDb()->createCommand('SELECT [[id]], [[sign]], [[alias]], [[name]], [[short_name]] AS [[shortName]], [[description]], [[parent_id]] AS [[parent]], [[level]], [[icon]], [[enabled]] FROM {{%category}} ORDER BY [[ordering]] ASC')->queryAll();
            foreach ($items as $key => $item) {
                $items[$key]['enabled'] = $item['enabled'] ? true : false;
                $item['icon'] && $items[$key]['icon'] = $url . $item['icon'];
            }
            $toTree && $items = ArrayHelper::toTree($items, 'id', 'parent');

            $cache->set($cacheKey, $items, 0, new DbDependency([
                'sql' => 'SELECT MAX([[updated_at]]) FROM {{%category}}'
            ]));
        }

        return $items;
    }

    /**
     * 获取分类展示树
     *
     * @param null|string $signOrId
     * @param string $returnType
     * @param null $enabled
     * @param bool $shortName
     * @return array
     * @throws \yii\db\Exception
     */
    public static function tree($signOrId = null, $returnType = self::RETURN_TYPE_PUBLIC, $enabled = null, $shortName = true)
    {
        $tree = [];
        $signOrId = trim($signOrId);
        $db = Yii::$app->getDb();
        if (is_numeric($signOrId)) {
            $parentId = (int) $signOrId;
        } elseif (is_string($signOrId) && $signOrId) {
            $parentId = $db->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => $signOrId])->queryScalar();
            if (!$parentId) {
                return [];
            }
        } else {
            $parentId = 0;
        }
        if ($categories = self::getChildren($parentId, 0, true)) {
            // 数据过滤
            if ($returnType == self::RETURN_TYPE_PRIVATE || $enabled !== null) {
                $privateCategoryIds = [];
                if ($returnType == self::RETURN_TYPE_PRIVATE) {
                    $user = Yii::$app->getUser();
                    if (!$user->getIsGuest()) {
                        $privateCategoryIds = $db->createCommand('SELECT [[category_id]] FROM {{%user_auth_category}} WHERE [[user_id]] = :userId', [':userId' => $user->getId()])->queryColumn();
                    }

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
                $categories = TreeFormatHelper::dumpArrayTree(ArrayHelper::toTree($categories, 'id', 'parent'));
                foreach ($categories as $category) {
                    $tree[$category['id']] = $category['levelstr'] . ($shortName ? $category['shortName'] : $category['name']);
                }
            }
        }

        return $tree;
    }

    private static function _getParents($items, $id)
    {
        $parents = [];
        foreach ($items as $i => $item) {
            if ($item['id'] == $id) {
                $parents[] = $item;
                unset($items[$i]);
                if ($item['parent']) {
                    $parents = array_merge($parents, self::_getParents($items, $item['parent']));
                }
            }
        }

        return $parents;
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

        $parents = array_reverse($parents);
        array_shift($parents);

        return $parents;
    }

    /**
     * 获取分类顶级目录
     *
     * @param $id
     * @return mixed
     * @throws \yii\db\Exception
     */
    public static function getRoot($id)
    {
        $items = self::getParents($id, true);
        reset($items);

        return current($items);
    }

    /**
     * 获取子节点数据
     *
     * @param $items
     * @param $parent
     * @param $level
     * @param bool $getAll
     * @return array
     */
    private static function _getChildren($items, $parent, $level, $getAll = false)
    {
        $children = [];
        $currentLevel = 0;
        $currentParent = 0;
        foreach ($items as $i => $item) {
            if ($item['parent'] == $parent) {
                if (!$getAll && $item['enabled'] == Constant::BOOLEAN_FALSE) {
                    continue;
                }
                $children[] = $item;
                if (!$level) {
                    unset($items[$i]);
                    $children = array_merge($children, self::_getChildren($items, $item['id'], $level, $getAll));
                } else {
                    if ($currentParent != $item['parent']) {
                        $currentParent = $item['parent'];
                        $currentLevel++;
                    }
                    if ($currentLevel < $level) {
                        unset($items[$i]);
                        $currentLevel = 0;
                        $children = array_merge($children, self::_getChildren($items, $item['id'], $level, $getAll));
                    }
                }
            }
        }

        return $children;
    }

    /**
     * 获取所有子节点数据
     *
     * @param int|null|string $signOrId
     * @param int $level
     * @param bool $getAll
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getChildren($signOrId = null, $level = 0, $getAll = false)
    {
        $signOrId = trim($signOrId);
        if (is_numeric($signOrId)) {
            $id = (int) $signOrId;
        } elseif (is_string($signOrId) && $signOrId) {
            $id = Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => $signOrId])->queryScalar();
            if (!$id) {
                return [];
            }
        } else {
            $id = 0;
        }

        return self::_getChildren(self::rawData(false), $id, (int) $level, $getAll);
    }

    /**
     * 获取所有子节点 id 集合
     *
     * @param mixed|integer $signOrId
     * @param int $level
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getChildrenIds($signOrId = 0, $level = 0)
    {
        $ids = [];
        foreach (self::getChildren($signOrId, $level) as $child) {
            $ids[] = (int) $child['id'];
        }

        return $ids;
    }

    /**
     * 获取所有子节点 id 集合（包含自身）
     *
     * @param int $signOrId
     * @param int $level
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getChildrenIdsWithSelf($signOrId = 0, $level = 0)
    {
        $signOrId = trim($signOrId);
        if (is_numeric($signOrId)) {
            $id = (int) $signOrId;
        } elseif (is_string($signOrId) && $signOrId) {
            $id = Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => $signOrId])->queryScalar();
            if (!$id) {
                return [];
            }
        } else {
            $id = 0;
        }

        $childrenIds = self::getChildrenIds($id, $level);
        $id && $childrenIds[] = $id;

        return $childrenIds;
    }

    /**
     * 判断是否有子项目
     *
     * @param $id
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function hasChildren($id)
    {
        return Yii::$app->getDb()->createCommand('SELECT COUNT(*) FROM {{%category}} WHERE [[parent_id]] = :parentId', [':parentId' => (int) $id])->queryScalar() ? true : false;
    }

    /**
     * 根据 sign 值获取 id
     *
     * @param $sign
     * @return false|null|string
     * @throws \yii\db\Exception
     */
    public static function getIdBySign($sign)
    {
        return Yii::$app->getDb()->createCommand('SELECT [[id]] FROM {{%category}} WHERE [[sign]] = :sign', [':sign' => trim($sign)])->queryScalar();
    }

    /**
     * 获取分类全路径名称
     *
     * @param $id
     * @return string
     * @throws \yii\db\Exception
     */
    public static function getFullName($id)
    {
        $name = [];
        $parents = self::getParents($id);
        foreach ($parents as $parent) {
            if ($parent['parent']) {
                $name[] = $parent['name'];
            }
        }
        krsort($name);

        return implode('/', $name);
    }

    /**
     * 获取分类路径
     *
     * @param string $type
     * @param string $sep
     * @param int $start
     * @return array|string
     */
    public function getPath($type = self::PATH_TYPE_NAME, $sep = '/', $start = 0)
    {
        $oldSep = ',';
        switch ($type) {
            case self::PATH_TYPE_ID:
            case self::PATH_TYPE_NAME:
                if ($type == self::PATH_TYPE_ID) {
                    $path = $this->id_path;
                } else {
                    $path = $this->name_path;
                }

                $path = explode($oldSep, $path);
                if ($start != 0) {
                    if ($start > 0) {
                        $offset = $start;
                        $length = null;
                    } else {
                        $offset = 0;
                        $length = count($path) - abs($start);
                    }

                    $path = array_slice($path, $offset, $length, true);
                }
                $path = implode($sep, $path);
                break;

            case self::PATH_TYPE_ID_NAME:
                $idPath = $this->id_path;
                $namePath = $this->name_path;
                $path = array_combine(explode($oldSep, $idPath), explode($oldSep, $namePath));

                if ($start > 0) {
                    $path = array_slice($path, $start, null, true);
                } elseif ($start < 0) {
                    $path = array_slice($path, 0, count($path) - abs($start), true);
                }
                break;

            default:
                $path = null;
                break;
        }

        return $path;
    }

    // 事件
    private $_alias = null;
    private $_parent_id = null;
    private $_level = null;
    private $_name;

    public function afterFind()
    {
        parent::afterFind();
        if (!$this->isNewRecord) {
            $this->_alias = $this->alias;
            $this->_parent_id = $this->parent_id;
            $this->_level = $this->level;
            $this->_name = $this->name;
        }
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     */
    public function beforeValidate()
    {
        if (parent::beforeValidate()) {
            if (empty($this->alias) && !empty($this->name)) {
                $alias = [];
                foreach (explode('-', Inflector::slug($this->name)) as $slug) {
                    $alias[] = $slug[0];
                }
                $this->alias = implode('', $alias);
            }
            $level = 0;
            if ($this->parent_id) {
                $parent = Yii::$app->getDb()->createCommand('SELECT [[level]], [[alias]] FROM {{%category}} WHERE [[id]] = :id', [':id' => $this->parent_id])->queryOne();
                if (strpos($this->alias, '/') === false) {
                    $parent['alias'] && $this->alias = "{$parent['alias']}/$this->alias";
                }
                $level = $parent['level'] + 1;
            }
            $this->level = $level;

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            empty($this->sign) && $this->sign = null;
            empty($this->short_name) && $this->short_name = $this->name;

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $insert
     * @param $changedAttributes
     * @throws \yii\db\Exception
     * @throws \yii\web\HttpException
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $db = Yii::$app->getDb();
        $cmd = $db->createCommand();
        if (!$insert && ($this->_alias != $this->alias)) {
            // 更新子栏目别名数据
            $children = self::getChildren($this->id);
            if ($children) {
                foreach ($children as $child) {
                    $childAlias = explode('/', $child['alias']);
                    foreach (explode('/', $this->alias) as $key => $value) {
                        $value && $childAlias[$key] = $value;
                    }
                    $alias = implode('/', $childAlias);
                    $cmd->update('{{%category}}', ['alias' => $alias], ['id' => $child['id']])->execute();
                }
            }
        }

        if ($this->parent_id != $this->_parent_id || $this->name != $this->_name) {
            $childrenIds = self::getChildrenIds($this->id);
            $childrenIds[] = $this->id;
            foreach ($childrenIds as $childId) {
                $parents = self::getParents($childId);
                $idPath = $namePath = [];
                foreach ($parents as $parent) {
                    $idPath[] = $parent['id'];
                    $namePath[] = $parent['name'];
                }
                $cmd->update('{{%category}}', [
                    'id_path' => implode(Constant::DELIMITER, $idPath),
                    'name_path' => implode(Constant::DELIMITER, $namePath),
                ], ['id' => $childId])->execute();
            }
        }

        if ($this->level != $this->_level) {
            $children = self::getChildrenIds($this->id);
            if ($children) {
                $level = $this->level - $this->_level;
                $sql = 'UPDATE {{%category}} SET [[level]] = [[level]]';
                if ($level) {
                    $sql .= ' + :level';
                } else {
                    $sql .= ' - :level';
                }
                $sql .= ' WHERE [[id]] IN (' . implode(',', $children) . ')';
                $db->createCommand($sql, [':level' => abs($level)])->execute();
            }
        }
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        Yii::$app->getDb()->createCommand()->delete('{{%user_auth_category}}', ['category_id' => $this->id])->execute();
        if ($icon = $this->icon) {
            $icon = Yii::getAlias('@webroot' . $icon);
            file_exists($icon) && FileHelper::unlink($icon);
        }
    }

}
