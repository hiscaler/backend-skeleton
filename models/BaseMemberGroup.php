<?php

namespace app\models;

use Overtrue\Pinyin\Pinyin;
use Yii;
use yii\db\Query;

/**
 * This is the model class for table "{{%member_group}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $alias
 * @property string $name
 * @property string $icon
 * @property integer $min_credits
 * @property integer $max_credits
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class BaseMemberGroup extends \yii\db\ActiveRecord
{

    /**
     * 分组类型
     */
    const TYPE_USER_GROUP = 0;
    const TYPE_SYSTEM_GROUP = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_group}}';
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
            [['type', 'name'], 'required'],
            ['alias', 'match', 'pattern' => '/^[a-z]+[.]?[a-z-]+[a-z]$/'],
            [['type', 'min_credits', 'max_credits', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['min_credits', 'max_credits'], 'default', 'value' => 0],
            [['name'], 'trim'],
            [['alias'], 'string', 'max' => 20],
            [['name'], 'string', 'max' => 30],
            [['icon'], 'string', 'max' => 100],
            [['max_credits'], 'valuesComparison'],
            ['alias', 'unique'],
        ];
    }

    /**
     * 用户积分最大值与最小值比较
     *
     * @param string $attribute
     * @param array $params
     */
    public function valuesComparison($attribute, $params)
    {
        if ($this->max_credits <= $this->min_credits) {
            $this->addError($attribute, "最大值应大于最小值");
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'type' => Yii::t('memberGroup', 'Type'),
            'type_text' => Yii::t('memberGroup', 'Type'),
            'alias' => Yii::t('memberGroup', 'Alias'),
            'name' => Yii::t('memberGroup', 'Name'),
            'icon' => Yii::t('memberGroup', 'Icon'),
            'min_credits' => Yii::t('memberGroup', 'Min Credits'),
            'max_credits' => Yii::t('memberGroup', 'Max Credits'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public static function typeOptions()
    {
        return [
            self::TYPE_USER_GROUP => '用户组',
            self::TYPE_SYSTEM_GROUP => '系统组',
        ];
    }

    /**
     * 用户组
     *
     * @return array
     */
    public static function userGroupOptions()
    {
        return (new Query())->select('name')->from(self::tableName())->where(['type' => self::TYPE_USER_GROUP])->indexBy('alias')->column();
    }

    /**
     * 系统组
     *
     * @return array
     */
    public static function systemGroupOptions()
    {
        return (new Query())->select('name')->from(self::tableName())->where(['type' => self::TYPE_SYSTEM_GROUP])->indexBy('alias')->column();
    }

    // Events

    private $_alias = null;

    public function afterFind()
    {
        parent::afterFind();
        if (!$this->getIsNewRecord()) {
            $this->_alias = $this->alias;
        }
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = $this->updated_at = time();
                $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
            } else {
                if (empty($this->alias) && !empty($this->name)) {
                    $this->alias = $this->alias = (new Pinyin())->abbr($this->name);
                }
                $this->updated_at = time();
                $this->updated_by = Yii::$app->getUser()->getId();
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if (!$insert && $this->alias != $this->_alias) {
            \Yii::$app->getDb()->createCommand('UPDATE {{%member}} SET [[group]] = :new WHERE [[group]] = :old', [':new' => $this->alias, ':old' => $this->_alias])->execute();
        }
    }

    /**
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        \Yii::$app->getDb()->createCommand('UPDATE {{%member}} SET [[group]] = NULL WHERE [[group]] = :group', [':group' => $this->alias])->execute();
    }

}
