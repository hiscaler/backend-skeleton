<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%meta}}".
 *
 * @property integer $id
 * @property string $object_name
 * @property string $key
 * @property string $label
 * @property string $description
 * @property string $input_type
 * @property string $input_candidate_value
 * @property integer $return_value_type
 * @property string $default_value
 * @property integer $enabled
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_by
 * @property integer $updated_at
 * @property integer $deleted_by
 * @property integer $deleted_at
 */
class Meta extends \yii\db\ActiveRecord
{

    public $validatorsList;

    /**
     * 数据输入方式
     */
    const INPUT_TYPE_TEXT = 0;
    const INPUT_TYPE_TEXTAREA = 1;
    const INPUT_TYPE_DROPDOWNLIST = 2;
    const INPUT_TYPE_CHECKBOXLIST = 3;
    const INPUT_TYPE_RADIOLIST = 4;

    /**
     * 数据值返回类型
     */
    const RETURN_VALUE_TYPE_STRING = 0;
    const RETURN_VALUE_TYPE_INTEGER = 1;
    const RETURN_VALUE_TYPE_ARRAY = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%meta}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['object_name', 'key', 'label', 'description', 'input_type'], 'required'],
            [['return_value_type', 'enabled', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at'], 'integer'],
            ['enabled', 'boolean'],
            [['object_name', 'key'], 'string', 'max' => 30],
            [['label', 'description'], 'string', 'max' => 255],
            [['input_type', 'default_value'], 'string', 'max' => 16],
            [['input_candidate_value'], 'string'],
            ['validatorsList', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'object_name' => Yii::t('meta', 'Object Name'),
            'object_name_formatted' => Yii::t('meta', 'Object Name'),
            'key' => Yii::t('meta', 'Key'),
            'label' => Yii::t('meta', 'Label'),
            'description' => Yii::t('meta', 'Description'),
            'input_type' => Yii::t('meta', 'Input Type'),
            'input_type_text' => Yii::t('meta', 'Input Type'),
            'input_candidate_value' => Yii::t('meta', 'Input Candidate Value'),
            'return_value_type' => Yii::t('meta', 'Return Value Type'),
            'return_value_type_text' => Yii::t('meta', 'Return Value Type'),
            'default_value' => Yii::t('meta', 'Default Value'),
            'enabled' => Yii::t('app', 'Enabled'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'deleted_at' => Yii::t('app', 'Deleted At'),
        ];
    }

    /**
     * 输入类型选项
     *
     * @return array
     */
    public static function inputTypeOptions()
    {
        return [
            self::INPUT_TYPE_TEXT => '文本',
            self::INPUT_TYPE_TEXTAREA => '大段文本',
            self::INPUT_TYPE_DROPDOWNLIST => '下拉框',
            self::INPUT_TYPE_CHECKBOXLIST => '复选框',
            self::INPUT_TYPE_RADIOLIST => '单选框',
        ];
    }

    /**
     * 数据输入方式
     *
     * @return string|mixed
     */
    public function getInput_type_text()
    {
        $options = self::inputTypeOptions();

        return isset($options[$this->input_type]) ? $options[$this->input_type] : null;
    }

    /**
     * 返回数据类型选项
     *
     * @retrun array
     */
    public static function returnValueTypeOptions()
    {
        return [
            self::RETURN_VALUE_TYPE_STRING => '字符串',
            self::RETURN_VALUE_TYPE_INTEGER => '数字',
            self::RETURN_VALUE_TYPE_ARRAY => '数组',
        ];
    }

    /**
     * 返回数据类型
     *
     * @return string|mixed
     */
    public function getReturn_value_type_text()
    {
        $options = self::returnValueTypeOptions();

        return isset($options[$this->return_value_type]) ? $options[$this->return_value_type] : null;
    }

    /**
     * 对象集合
     *
     * @return array
     */
    public static function getObjectNames()
    {
        $names = [];
        $db = Yii::$app->getDb();
        $tables = array_map(function ($v) use ($db) {
            return str_replace($db->tablePrefix, '', $v);
        }, $db->getSchema()->getTableNames());

        $files = \yii\helpers\FileHelper::findFiles(\Yii::getAlias('@app/models'));
        foreach ($files as $file) {
            $name = basename($file, '.php');
            if (substr($name, -6) != 'Search') {
                try {
                    $class = new \ReflectionClass("\\app\\models\\{$name}");
                    if ($class->hasMethod('tableName')) {
                        $instance = $class->newInstanceWithoutConstructor();
                        $tableName = strtr($instance->tableName(), ['{' => '', '}' => '', '%' => '']);
                        if ($instance instanceof \yii\db\ActiveRecord && in_array($tableName, $tables)) {
                            $names[$tableName] = Yii::t('model', \yii\helpers\Inflector::camel2words($name));
                        }
                    }
                } catch (Exception $exc) {
                }
            }
        }

        return $names;
    }

    /**
     * 格式化之后的对对象名称
     *
     * @return string
     */
    public function getObject_name_formatted()
    {
        return Yii::t('model', \yii\helpers\Inflector::camel2words($this->object_name));
    }

    /**
     * 获取对象的 Meta 数据
     *
     * @param \yii\db\ActiveRecord $activeRecord
     * @return array
     */
    public static function getItems(ActiveRecord $activeRecord)
    {
        $items = [];
        $query = new \yii\db\Query();
        $rawData = $query->select(['id', 'key', 'label', 'description', 'input_type', 'input_candidate_value', 'default_value'])
            ->from(static::tableName())
            ->where([
                'object_name' => strtr($activeRecord->tableName(), ['{{%' => '', '}}' => '']),
                'enabled' => Constant::BOOLEAN_TRUE,
            ])
            ->indexBy('id')
            ->all();

        $objectId = $activeRecord->isNewRecord ? null : $activeRecord->getPrimaryKey();
        if ($objectId) {
            $values = [];
            $rawValues = (new \yii\db\Query())
                ->select('*')
                ->from('{{%meta_value}}')
                ->where([
                    'meta_id' => array_keys($rawData),
                    'object_id' => $objectId,
                ])
                ->all();
            foreach ($rawValues as $item) {
                $key = "{$item['meta_id']}.{$item['object_id']}";
                if (!isset($values[$key])) {
                    $values[$key] = $item['value'];
                } else {
                    if (is_array($values[$key])) {
                        $values[$key][] = $item['value'];
                    } else {
                        $values[$key] = [$values[$key], $item['value']];
                    }
                }
            }
        } else {
            $values = [];
        }

        foreach ($rawData as $data) {
            $data['value'] = $values && isset($values["{$data['id']}.{$objectId}"]) ? $values["{$data['id']}.{$objectId}"] : null;
            switch ($data['input_type']) {
                case self::INPUT_TYPE_TEXTAREA:
                    $data['input_type'] = 'textarea';
                    $data['input_candidate_value'] = [];
                    break;

                case self::INPUT_TYPE_DROPDOWNLIST:
                case self::INPUT_TYPE_CHECKBOXLIST:
                case self::INPUT_TYPE_RADIOLIST:
                    if ($data['input_type'] == self::INPUT_TYPE_DROPDOWNLIST) {
                        $data['input_type'] = 'dropDownList';
                    } elseif ($data['input_type'] == self::INPUT_TYPE_CHECKBOXLIST) {
                        $data['input_type'] = 'checkboxList';
                    } elseif ($data['input_type'] == self::INPUT_TYPE_RADIOLIST) {
                        $data['input_type'] = 'radioList';
                    }

                    // 候选值处理
                    $inputCandidateValue = [];
                    $rawInputCandidateValue = $data['input_candidate_value'];
                    if (!empty($rawInputCandidateValue)) {
                        foreach (explode(PHP_EOL, $rawInputCandidateValue) as $row) {
                            $row = explode(':', $row);
                            if (count($row) == 2 && !empty($row[0]) && !empty($row[1])) {
                                $inputCandidateValue[$row[0]] = $row[1];
                            }
                        }
                    }
                    $data['input_candidate_value'] = $inputCandidateValue;
                    break;

                default:
                    $data['input_type'] = 'textInput';
                    $data['input_candidate_value'] = [];
                    break;
            }
            $rules = self::getMetaRules($data['id']);
            $data['rules'] = $rules ?: [[$data['key'], 'safe']];
            $items[$data['key']] = $data;
        }

        return $items;
    }

    /**
     * 获取数据验证规则
     *
     * @param $objectName
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getRules($objectName)
    {
        $rules = [];
        $validators = Yii::$app->getDb()->createCommand('SELECT [[name]], [[options]] FROM {{%meta_validator}} WHERE [[meta_id]] IN (SELECT [[id]] FROM {{%meta}} WHERE [[object_name]] = :objectName)', [':objectName' => trim($objectName)])->queryAll();
        foreach ($validators as $validator) {
            $options = unserialize($validator['options']) ?: [];
            foreach ($options as $key => $value) {
                if (trim($value) == '') {
                    unset($options[$key]);
                }
            }
            $rules[$validator['name']] = $options ?: ['safe' => []];
        }

        return $rules;
    }

    /**
     * 获取 Meta 对象的验证规则
     *
     * @param integer $metaId
     * @return arrya
     */
    public static function getMetaRules($metaId)
    {
        $rules = [];
        $validators = Yii::$app->getDb()->createCommand('SELECT [[name]], [[options]] FROM {{%meta_validator}} WHERE [[meta_id]] = :metaId', [':metaId' => (int) $metaId])->queryAll();
        foreach ($validators as $validator) {
            $options = unserialize($validator['options']) ?: [];
            foreach ($options as $key => $value) {
                if (trim($value) == '') {
                    unset($options[$key]);
                }
            }
            $rules[$validator['name']] = $options;
        }

        return $rules;
    }

    /**
     * 保存 Meta 数据
     *
     * @param \yii\db\ActiveRecord $activeRecord
     * @param \yii\base\DynamicModel $dynamicModel
     * @param type $throwException
     * @return boolean|mixed
     * @throws \yii\base\ErrorException
     */
    public static function saveValues(\yii\db\ActiveRecord $activeRecord, \yii\base\DynamicModel $dynamicModel, $throwException = false)
    {
        try {
            $attributes = $dynamicModel->attributes;
            if (!$attributes) {
                return null;
            }
            $command = Yii::$app->getDb()->createCommand();
            $objectId = $activeRecord->getPrimaryKey();
            $metaList = (new \yii\db\Query())
                ->select('id')
                ->from('{{%meta}}')
                ->indexBy('key')
                ->where([
                    'key' => array_keys($attributes),
                    'object_name' => strtr($activeRecord->tableName(), ['{{%' => '', '}}' => '']),
                ])
                ->column();
            if (!$activeRecord->isNewRecord) {
                $command->delete('{{%meta_value}}', ['object_id' => $objectId, 'meta_id' => array_values($metaList)])->execute();
            }

            $batchInsertRows = [];
            foreach ($attributes as $key => $value) {
                if (!isset($metaList[$key]) || $value === null || $value == '') {
                    continue;
                }
                if (is_string($value)) {
                    $value = [$value];
                }
                $columns = [
                    'meta_id' => $metaList[$key],
                    'object_id' => $objectId,
                ];
                foreach ($value as $v) {
                    $columns['value'] = $v;
                    $batchInsertRows[] = $columns;
                }
            }

            if ($batchInsertRows) {
                $command->batchInsert('{{%meta_value}}', ['meta_id', 'object_id', 'value'], $batchInsertRows)->execute();
            }

            return true;
        } catch (\Exception $exc) {
            if ($throwException) {
                throw new \yii\base\ErrorException($exc->getMessage());
            } else {
                return false;
            }
        }
    }

    /**
     * 获取自定义字段内容值
     *
     * @param \yii\db\ActiveRecord $activeRecord
     * @param integer $objectId
     * @param array $keys 需要获取字段列表
     * @return array
     */
    public static function getValues(\yii\db\ActiveRecord $activeRecord, $objectId, $keys = array())
    {
        $values = [];
        foreach ($keys as $key) {
            $values[$key] = [
                'id' => null,
                'label' => null,
                'description' => null,
                'value' => null,
            ];
        }

        $where = [
            'object_name' => strtr($activeRecord->tableName(), ['{{%' => '', '}}' => ''])
        ];
        if ($keys) {
            $where['key'] = $keys;
        }
        $rawValues = (new \yii\db\Query())
            ->select(['m.id', 'm.key', 'm.label', 'm.description', 't.value'])
            ->from('{{%meta_value}} t')
            ->leftJoin('{{%meta}} m', '[[t.meta_id]] = [[m.id]]')
            ->where(['t.object_id' => (int) $objectId,])
            ->andWhere(['in', 't.meta_id', (new \yii\db\Query())->select(['id'])->from('{{%meta}}')->where($where)])
            ->all();
        foreach ($rawValues as $data) {
            $values[$data['key']] = [
                'id' => $data['id'],
                'label' => $data['label'],
                'description' => $data['description'],
                'value' => $data['value'],
            ];
        }

        return $values;
    }

    public static function getValue($objectName, $key, $objectId)
    {
        $value = null;
        $db = Yii::$app->getDb();
        $metaId = $db->createCommand('SELECT [[id]] FROM {{%meta}} WHERE [[object_name]] = :objectName AND [[key]] = :key', [':objectName' => strtolower(trim($objectName)), ':key' => trim($key)])->queryScalar();
        if ($metaId) {
            $value = $db->createCommand('SELECT [[value]] FROM {{%meta_value}} WHERE [[meta_id]] = :metaId AND [[object_id]] = :objectId', [':metaId' => $metaId, ':objectId' => (int) $objectId])->queryScalar() ?: null;
        }

        return $value;
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = $this->updated_at = time();
                $this->created_by = $this->updated_by = Yii::$app->getUser()->getId();
            } else {
                $this->updated_at = time();
                $this->updated_by = Yii::$app->getUser()->getId();
            }

            return true;
        } else {
            return false;
        }
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        $command = Yii::$app->getDb()->createCommand();
        if (!$insert) {
            $command->delete('{{%meta_validator}}', ['meta_id' => $this->id])->execute();
        }

        $batchInsertRows = [];
        foreach (is_array($this->validatorsList) ? $this->validatorsList : [] as $key => $item) {
            if (!isset($item['name'])) {
                // 未选择
                continue;
            }
            $columns = [
                'meta_id' => $this->id,
                'name' => $key,
                'options' => serialize(isset($item['options']) ? $item['options'] : [])
            ];
            $batchInsertRows[] = array_values($columns);
        }
        if ($batchInsertRows) {
            $command->batchInsert('{{%meta_validator}}', array_keys($columns), $batchInsertRows)->execute();
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        // 删除 meta 数据同时清理掉相关的验证规则以及保存的值
        $cmd = Yii::$app->getDb()->createCommand();
        $cmd->delete('{{%meta_validator}}', ['meta_id' => $this->id])->execute();
        $cmd->delete('{{%meta_value}}', ['meta_id' => $this->id])->execute();
    }

}
