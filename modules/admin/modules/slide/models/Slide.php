<?php

namespace app\modules\admin\modules\slide\models;

use app\models\BaseActiveRecord;
use app\models\FileUploadConfig;
use app\models\Lookup;
use app\modules\admin\components\ApplicationHelper;
use yadjet\behaviors\ImageUploadBehavior;
use Yii;

/**
 * This is the model class for table "www_slide".
 *
 * @property integer $id
 * @property integer $category_id
 * @property string $title
 * @property string $url
 * @property string $url_open_target
 * @property string $picture_path
 * @property integer $ordering
 * @property integer $enabled
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 */
class Slide extends BaseActiveRecord
{

    const GROUP_KEY = 'm.models.slide.group';

    public $_fileUploadConfig;

    public function init()
    {
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::className2Id(), 'picture_path');
        parent::init();
    }

    /**
     * 链接打开方式
     */
    const URL_OPEN_TARGET_BLANK = '_blank';
    const URL_OPEN_TARGET_SLFE = '_self';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%slide}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'ordering', 'enabled', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'url'], 'required'],
            [['title'], 'string', 'max' => 60],
            [['url'], 'string', 'max' => 100],
            [['url_open_target'], 'string', 'max' => 6],
            [['url'], 'url', 'defaultScheme' => 'http'],
            ['category_id', 'default', 'value' => 0],
            ['picture_path', 'image',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
                'tooSmall' => Yii::t('app', 'The file "{file}" is too small. Its size cannot be smaller than {limit}.', [
                    'limit' => ApplicationHelper::friendlyFileSize($this->_fileUploadConfig['size']['min']),
                ]),
                'tooBig' => Yii::t('app', 'The file "{file}" is too big. Its size cannot exceed {limit}.', [
                    'limit' => ApplicationHelper::friendlyFileSize($this->_fileUploadConfig['size']['max']),
                ]),
            ],];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImageUploadBehavior::className(),
                'attribute' => 'picture_path',
                'thumb' => $this->_fileUploadConfig['thumb']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'category_id' => Yii::t('app', 'Group'),
            'group_name' => Yii::t('app', 'Group'),
            'title' => '名称',
            'url' => Yii::t('slide.model', 'URL'),
            'url_open_target' => '打开方式',
            'url_open_target_text' => '打开方式',
            'picture_path' => '图片',
            'ordering' => Yii::t('app', 'Ordering'),
            'enabled' => Yii::t('app', 'Enabled'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public static function urlOpenTargetOptions()
    {
        return [
            self::URL_OPEN_TARGET_BLANK => '新窗口',
            self::URL_OPEN_TARGET_SLFE => '当前窗口',
        ];
    }

    public function getUrl_open_target_text()
    {
        $options = self::urlOpenTargetOptions();

        return isset($options[$this->url_open_target]) ? $options[$this->url_open_target] : null;
    }

    public function getGroup_name()
    {
        return Lookup::getValue(static::GROUP_KEY);
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

}
