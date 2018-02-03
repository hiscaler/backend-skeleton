<?php

namespace app\models;

use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%meta}}".
 *
 * @property integer $id
 * @property string $table_name
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
    const INPUT_TYPE_FILE = 5;

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
            [['table_name', 'key', 'label', 'description', 'input_type'], 'required'],
            [['return_value_type', 'enabled', 'created_by', 'created_at', 'updated_by', 'updated_at', 'deleted_by', 'deleted_at'], 'integer'],
            ['enabled', 'boolean'],
            [['table_name'], 'string', 'max' => 60],
            [['key'], 'string', 'max' => 30],
            [['key'], 'trim'],
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
            'table_name' => Yii::t('meta', 'Table Name'),
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
            self::INPUT_TYPE_FILE => '文件上传',
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
     * 获取对象的 Meta 数据
     *
     * @param \yii\db\ActiveRecord $activeRecord
     * @return array
     */
    public static function getItems(ActiveRecord $activeRecord)
    {
        $items = [];
        $rawData = (new Query())->select(['id', 'key', 'label', 'description', 'input_type', 'input_candidate_value', 'default_value'])
            ->from(static::tableName())
            ->where([
                'table_name' => strtr($activeRecord->tableName(), ['{{%' => '', '}}' => '']),
                'enabled' => Constant::BOOLEAN_TRUE,
            ])
            ->indexBy('id')
            ->all();

        $objectId = $activeRecord->isNewRecord ? null : $activeRecord->getPrimaryKey();
        if ($objectId) {
            $values = [];
            $rawValues = (new Query())
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

        $rawRules = [];
        $validators = (new Query())->select(['meta_id', 'name', 'options'])
            ->from('{{%meta_validator}}')
            ->where(['meta_id' => array_keys($rawData)])
            ->all();
        foreach ($validators as $validator) {
            $options = unserialize($validator['options']) ?: [];
            foreach ($options as $key => $value) {
                if (trim($value) == '') {
                    unset($options[$key]);
                }
            }
            !isset($rawRules[$validator['meta_id']]) && $rawRules[$validator['meta_id']] = [];
            $options && $rawRules[$validator['meta_id']][$validator['name']] = $options;
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

                case self::INPUT_TYPE_FILE:
                    $data['input_type'] = 'fileInput';
                    $data['input_candidate_value'] = [];
                    break;

                default:
                    $data['input_type'] = 'textInput';
                    $data['input_candidate_value'] = [];
                    break;
            }
            $data['rules'] = (isset($rawRules[$data['id']]) && $rawRules[$data['id']]) ? $rawRules[$data['id']] : ['safe' => []];
            $items[$data['key']] = $data;
        }

        return $items;
    }

    /**
     * 获取数据验证规则
     *
     * @param $tableName
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getRules($tableName)
    {
        $rules = [];
        $validators = Yii::$app->getDb()->createCommand('SELECT [[name]], [[options]] FROM {{%meta_validator}} WHERE [[meta_id]] IN (SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName)', [':tableName' => trim($tableName)])->queryAll();
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
     * @param $metaId
     * @return array
     * @throws \yii\db\Exception
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
     * @param ActiveRecord $activeRecord
     * @param \yii\base\DynamicModel $dynamicModel
     * @param bool $throwException
     * @return bool|null
     * @throws ErrorException
     * @throws \yii\db\Exception
     */
    public static function saveValues(\yii\db\ActiveRecord $activeRecord, \yii\base\DynamicModel $dynamicModel, $throwException = false)
    {
        $db = \Yii::$app->getDb();
        $command = $db->createCommand();
        $transaction = $db->beginTransaction();
        try {
            $attributes = $dynamicModel->attributes;
            if (!$attributes) {
                return null;
            }
            $objectId = $activeRecord->getPrimaryKey();
            $metaList = (new Query())
                ->select('id')
                ->from('{{%meta}}')
                ->indexBy('key')
                ->where([
                    'key' => array_keys($attributes),
                ])
                ->column();

            $batchInsertRows = [];
            $reservedMetaIds = [];
            $validators = $dynamicModel->validators;
            foreach ($attributes as $key => $value) {
                $isFile = false;
                foreach ($validators as $validator) {
                    if ($validator instanceof \yii\validators\FileValidator) {
                        $validatorAttributes = $validator->attributes;
                        foreach ($validatorAttributes as $attr) {
                            if ($key == $attr) {
                                $isFile = true;
                                break;
                            }
                        }
                    }
                }
                if ($isFile) {
                    $file = UploadedFile::getInstance($dynamicModel, $key);
                    if ($file) {
                        $directory = Yii::getAlias('@webroot');
                        $path = '/uploads/' . date('Ymd');
                        if (!is_dir($directory . $path)) {
                            FileHelper::createDirectory($directory . $path);
                        }
                        $filename = Yii::$app->getSecurity()->generateRandomString() . '.' . $file->getExtension();
                        $file->saveAs($directory . $path . '/' . $filename);
                        $value = $path . '/' . $filename;
                    } else {
                        if ($activeRecord->isNewRecord) {
                            continue;
                        } else {
                            $value = null;
                            $reservedMetaIds[] = $metaList[$key];
                        }
                    }
                } else {
                    $value = (string) $value;
                }
                if (!isset($metaList[$key]) || $value === '' || $value === null || (is_string($value) && trim($value) === '') || ($value == '' && !$isFile)) {
                    continue;
                }
                $batchInsertRows[] = [
                    'object_id' => $objectId,
                    'meta_id' => $metaList[$key],
                    'value' => $value,
                ];
            }

            if (!$activeRecord->isNewRecord) {
                $deleteMetaIds = array_values($metaList);
                if ($reservedMetaIds) {
                    $deleteMetaIds = array_diff($deleteMetaIds, $reservedMetaIds);
                }
                $condition = ['object_id' => $objectId];
                if ($deleteMetaIds) {
                    $condition['meta_id'] = $deleteMetaIds;
                }
                $command->delete('{{%meta_value}}', $condition)->execute();
            }

            if ($batchInsertRows) {
                $command->batchInsert('{{%meta_value}}', array_keys($batchInsertRows[0]), $batchInsertRows)->execute();
            }

            $transaction->commit();

            return true;
        } catch (\Exception $exc) {
            $transaction->rollBack();
            if ($throwException) {
                throw new ErrorException($exc->getMessage());
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
            'table_name' => strtr($activeRecord->tableName(), ['{{%' => '', '}}' => ''])
        ];
        if ($keys) {
            $where['key'] = $keys;
        }
        $rawValues = (new Query())
            ->select(['m.id', 'm.key', 'm.label', 'm.description', 't.value'])
            ->from('{{%meta_value}} t')
            ->leftJoin('{{%meta}} m', '[[t.meta_id]] = [[m.id]]')
            ->where(['t.object_id' => (int) $objectId])
            ->andWhere(['in', 't.meta_id', (new Query())->select(['id'])->from('{{%meta}}')->where($where)])
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

    public static function getValue($tableName, $key, $objectId)
    {
        $value = null;
        $db = Yii::$app->getDb();
        $metaId = $db->createCommand('SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName AND [[key]] = :key', [':tableName' => strtolower(trim($tableName)), ':key' => trim($key)])->queryScalar();
        if ($metaId) {
            $value = $db->createCommand('SELECT [[value]] FROM {{%meta_value}} WHERE [[meta_id]] = :metaId AND [[object_id]] = :objectId', [':metaId' => $metaId, ':objectId' => (int) $objectId])->queryScalar() ?: null;
        }

        return $value;
    }

    /**
     * 更新自定义表单数据值
     *
     * @param $tableName
     * @param $key
     * @param $objectId
     * @param $value
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function updateValue($tableName, $key, $objectId, $value)
    {
        $success = false;
        $db = Yii::$app->getDb();
        $metaId = $db->createCommand('SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName AND [[key]] = :key', [':tableName' => strtolower(trim($tableName)), ':key' => trim($key)])->queryScalar();
        if ($metaId) {
            $v = $db->createCommand('SELECT [[value]] FROM {{%meta_value}} WHERE [[meta_id]] = :metaId AND [[object_id]] = :objectId', [':metaId' => $metaId, ':objectId' => (int) $objectId])->queryScalar() ?: null;
            // @todo 验证 objectId 是否有效
            if ($v === null) {
                // insert
                $db->createCommand()->insert('{{%meta_value}}', [
                    'meta_id' => $metaId,
                    'object_id' => $objectId,
                    'value' => $value,
                ])->execute();
                $success = true;
            } else {
                // Update
                $db->createCommand()->update('{{%meta_value}}', [
                    'value' => $value,
                ], [
                    'meta_id' => $metaId,
                    'object_id' => $objectId
                ])->execute();
                $success = true;
            }
        }

        return $success;
    }

    /**
     * 增加自定义表单项目值
     *
     * @param $tableName
     * @param $key
     * @param $objectId
     * @param $value
     * @return int|null
     * @throws \yii\db\Exception
     */
    public static function increaseValue($tableName, $key, $objectId, $value)
    {
        $result = null;
        $db = Yii::$app->getDb();
        $metaId = $db->createCommand('SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName AND [[key]] = :key', [':tableName' => strtolower(trim($tableName)), ':key' => trim($key)])->queryScalar();
        if ($metaId) {
            $v = $db->createCommand('SELECT [[value]] FROM {{%meta_value}} WHERE [[meta_id]] = :metaId AND [[object_id]] = :objectId', [':metaId' => $metaId, ':objectId' => (int) $objectId])->queryScalar();
            // @todo 验证 objectId 是否有效
            if ($v === false) {
                // Insert
                $db->createCommand()->insert('{{%meta_value}}', [
                    'value' => (int) $value,
                    'meta_id' => $metaId,
                    'object_id' => $objectId
                ])->execute();
            } else {
                $value = intval($v) + (int) $value;
                // Update
                $db->createCommand()->update('{{%meta_value}}', [
                    'value' => $value,
                ], [
                    'meta_id' => $metaId,
                    'object_id' => $objectId
                ])->execute();
                $result = $value;
            }
        }

        return $result;
    }

    /**
     * 减少自定义表单项目值
     *
     * @param $tableName
     * @param $key
     * @param $objectId
     * @param $value
     * @return int|null
     * @throws \yii\db\Exception
     */
    public static function decreaseValue($tableName, $key, $objectId, $value)
    {
        $result = null;
        $db = Yii::$app->getDb();
        $metaId = $db->createCommand('SELECT [[id]] FROM {{%meta}} WHERE [[table_name]] = :tableName AND [[key]] = :key', [':tableName' => strtolower(trim($tableName)), ':key' => trim($key)])->queryScalar();
        if ($metaId) {
            $v = $db->createCommand('SELECT [[value]] FROM {{%meta_value}} WHERE [[meta_id]] = :metaId AND [[object_id]] = :objectId', [':metaId' => $metaId, ':objectId' => (int) $objectId])->queryScalar();
            // @todo 验证 objectId 是否有效
            if ($v === false) {
                // Insert
                $db->createCommand()->insert('{{%meta_value}}', [
                    'value' => (int) $value,
                    'meta_id' => $metaId,
                    'object_id' => $objectId
                ])->execute();
            } else {
                $value = intval($v) - (int) $value;
                // Update
                $db->createCommand()->update('{{%meta_value}}', [
                    'value' => $value,
                ], [
                    'meta_id' => $metaId,
                    'object_id' => $objectId
                ])->execute();
                $result = $value;
            }
        }

        return $result;
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

            // Fixed `return value type`
            switch ($this->input_type) {
                case self::INPUT_TYPE_TEXTAREA:
                case self::INPUT_TYPE_FILE:
                    $this->return_value_type = self::RETURN_VALUE_TYPE_STRING;
                    break;

                case self::INPUT_TYPE_DROPDOWNLIST:
                case self::INPUT_TYPE_CHECKBOXLIST:
                case self::INPUT_TYPE_RADIOLIST:
                    $this->return_value_type = self::RETURN_VALUE_TYPE_ARRAY;
                    break;
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
