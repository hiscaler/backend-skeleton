<?php

namespace app\models;

use Yii;
use yii\caching\DbDependency;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "{{%file_upload_config}}".
 *
 * @property integer $id
 * @property integer $type
 * @property string $model_name
 * @property string $attribute
 * @property string $extensions
 * @property integer $min_size
 * @property integer $max_size
 * @property integer $thumb_width
 * @property integer $thumb_height
 * @property integer $created_by
 * @property integer $created_at
 * @property integer $updated_by
 * @property integer $updated_at
 */
class FileUploadConfig extends BaseActiveRecord
{

    public $model_attribute;

    /**
     * Upload file types
     */
    const TYPE_FILE = 0;
    const TYPE_IMAGE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file_upload_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['type', 'model_attribute', 'extensions', 'min_size', 'max_size'], 'required'],
            ['model_name', 'match', 'pattern' => '/^[a-zA-Z\\\]+$/'],
            ['extensions', 'match', 'pattern' => '/^[a-z0-9,]+$/'],
            ['attribute', 'match', 'pattern' => '/^[a-zA-Z0-9_]+$/'],
            [['model_name', 'attribute'], 'unique', 'targetAttribute' => ['model_name', 'attribute']],
            [['type', 'min_size', 'max_size', 'thumb_width', 'thumb_height', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            ['min_size', 'default', 'value' => 100],
            ['max_size', 'default', 'value' => 200],
            ['max_size', 'checkMaxSize'],
            [['model_name', 'attribute', 'extensions'], 'string', 'max' => 255],
            ['model_attribute', 'safe'],
        ]);
    }

    public function checkMaxSize($attrirbute, $params)
    {
        if (!$this->hasErrors()) {
            if ($this->max_size < $this->min_size) {
                $this->addError('max_size', '文件最大值不能小于最小值。');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'type' => Yii::t('fileUploadConfig', 'Type'),
            'type_text' => Yii::t('fileUploadConfig', 'Type'),
            'model_attribute' => Yii::t('fileUploadConfig', 'Model Attribute'),
            'attribute' => Yii::t('fileUploadConfig', 'Attribute'),
            'extensions' => Yii::t('fileUploadConfig', 'Extensions'),
            'size' => Yii::t('fileUploadConfig', 'Size'),
            'min_size' => Yii::t('fileUploadConfig', 'Min Size'),
            'max_size' => Yii::t('fileUploadConfig', 'Max Size'),
            'thumb' => Yii::t('fileUploadConfig', 'Thumb'),
            'thumb_width' => Yii::t('fileUploadConfig', 'Thumb Width'),
            'thumb_height' => Yii::t('fileUploadConfig', 'Thumb Height'),
        ]);
    }

    public static function typeOptions()
    {
        return [
            self::TYPE_FILE => Yii::t('fileUploadConfig', 'File'),
            self::TYPE_IMAGE => Yii::t('fileUploadConfig', 'Image')
        ];
    }

    /**
     * 默认配置
     *
     * @return array
     */
    private static function defaultConfig()
    {
        return [
            'extensions' => null,
            'size' => [
                'min' => 1,
                'max' => 204800, // 200 KB
            ],
            'thumb' => [
                'generate' => false,
            ],
        ];
    }

    /**
     * 返回指定的上传配置（可以返回多个）
     *
     * @param array $pairs
     * @return array
     * @throws \yii\db\Exception
     */
    public static function getConfigs($pairs = [])
    {
        $cacheKey = static::class . __FUNCTION__;
        $cache = Yii::$app->getCache();
        $cacheData = $cache->get($cacheKey);
        if ($cacheData === false) {
            $configs = [];
            foreach ($pairs as $key => $value) {
                $configs[$key . '@' . $value] = self::defaultConfig();
            }
            $rawData = Yii::$app->getDb()->createCommand('SELECT [[type]], [[model_name]], [[attribute]], [[extensions]], [[min_size]], [[max_size]], [[thumb_width]], [[thumb_height]] FROM ' . static::tableName())->queryAll();
            foreach ($rawData as $data) {
                $key = $data['model_name'] . '@' . $data['attribute'];
                $configs[$key] = [
                    'extensions' => !empty($data['extensions']) ? $data['extensions'] : null,
                    'size' => [
                        'min' => (int) $data['min_size'],
                        'max' => (int) $data['max_size'],
                    ],
                    'thumb' => [
                        'generate' => false,
                    ],
                ];
                if ($data['type'] == self::TYPE_IMAGE && $data['thumb_width'] && $data['thumb_height']) {
                    $configs[$key]['thumb'] = [
                        'generate' => true,
                        'width' => (int) $data['thumb_width'],
                        'height' => (int) $data['thumb_height'],
                    ];
                }
            }

            $cache->set($cacheKey, $configs, 0, new DbDependency([
                'sql' => 'SELECT MAX(updated_at) FROM {{%file_upload_config}}',
            ]));

            return $configs;
        } else {
            return $cacheData;
        }
    }

    /**
     * 获取上传文件设置
     *
     * @param string $modelName
     * @param string $attribute
     * @return array
     */
    public static function getConfig($modelName, $attribute)
    {
        $configs = static::getConfigs();
        $key = "{$modelName}@{$attribute}";

        return isset($configs[$key]) ? $configs[$key] : static::defaultConfig();
    }

    /**
     * 获取有效模型名称列表
     *
     * @return array
     * @throws \yii\db\Exception
     */
    public static function validModelNames()
    {
        $names = [];
        $contentModels = ArrayHelper::getValue(Yii::$app->params, 'contentModules', []);
        $rawData = Yii::$app->getDb()->createCommand('SELECT DISTINCT([[model_name]]) FROM ' . static::tableName())->queryColumn();
        foreach ($rawData as $name) {
            if (isset($contentModels[$name]['label'])) {
                $text = Yii::t("app", $contentModels[$name]['label']);
            } else {
                $text = $name;
            }

            $names[$name] = $text;
        }

        return $names;
    }

    /**
     * 获取可设置上传设定的模型和字段属性名称列表
     *
     * @return array
     * @throws \yii\base\NotSupportedException
     */
    public static function modelAttributeOptions()
    {
        $options = [];
        $db = Yii::$app->getDb();
        $tablePrefix = $db->tablePrefix;
        $coreTables = Option::coreTables();
        foreach ($db->getSchema()->getTableSchemas() as $tableSchema) {
            $tableName = str_replace($tablePrefix, '', $tableSchema->name);
            if ($tableName == 'migration') {
                continue;
            }
            $modelName = Inflector::id2camel($tableName, '_');
            $moduleName = null;
            if (in_array($tableName, $coreTables)) {
                $isCore = true;
                $modelNamespace = "app\\models\\$modelName";
            } else {
                $isCore = false;
                $index = stripos($tableName, '_');
                if ($index === false) {
                    $moduleName = $modelName = $tableName;
                } else {
                    $moduleName = substr($tableName, 0, $index);
                    $modelName = substr($tableName, $index + 1);
                }
                $moduleName = strtolower($moduleName);
                $modelName = Inflector::id2camel($modelName, '_');
                $modelNamespace = "app\\modules\\admin\\modules\\$moduleName\\models\\$modelName";
            }
            try {
                $attributeLabels = Yii::createObject($modelNamespace)->attributeLabels();
                foreach ($tableSchema->columns as $name => $column) {
                    if ($column->type === 'string' && (in_array($name, ['avatar', 'icon', 'picture', 'pic', 'image', 'img']) || strpos($name, '_path') !== false)) {
                        $options[$modelNamespace . ':' . $name] = '「' . Yii::t($isCore ? 'model' : "$moduleName", Inflector::camel2words($modelName)) . '」' . (isset($attributeLabels[$name]) ? $attributeLabels[$name] : $name) . " ($name)";
                    }
                }
            } catch (\Exception $ex) {
            }
        }

        return $options;
    }

    // Events
    public function afterFind()
    {
        parent::afterFind();
        // Bit to KB
        $this->min_size /= 1024;
        $this->max_size /= 1024;

        $this->model_attribute = $this->model_name . ':' . $this->attribute;
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            // KB To Bit
            $this->min_size *= 1024;
            $this->max_size *= 1024;

            $modelAttribute = explode(':', $this->model_attribute);
            $this->model_name = $modelAttribute[0];
            $this->attribute = $modelAttribute[1];

            if ($this->type == self::TYPE_FILE) {
                $this->thumb_width = $this->thumb_height = null;
            }

            return true;
        } else {
            return false;
        }
    }

}
