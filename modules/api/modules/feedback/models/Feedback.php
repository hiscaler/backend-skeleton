<?php

namespace app\modules\api\modules\feedback\models;

use app\models\FileUploadConfig;
use yadjet\behaviors\ImageUploadBehavior;

/**
 * This is the model class for table "{{%feedback}}".
 *
 * @property int $id
 * @property int $category_id 分类
 * @property string $title 标题
 * @property string $username 姓名
 * @property string $tel 电话号码
 * @property string $mobile_phone 手机号码
 * @property string $email 邮箱
 * @property int $ip IP 地址
 * @property string $picture 图片
 * @property string $message 内容
 * @property string $response_message 回复内容
 * @property int $response_datetime 回复时间
 * @property int $enabled 激活
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Feedback extends \yii\db\ActiveRecord
{

    /**
     * @var string 文件上传字段
     */
    public $fileFields = 'picture';

    /**
     * @var array 文件上传设置
     */
    public $_fileUploadConfig;

    /**
     * @throws \yii\db\Exception
     */
    public function init()
    {
        parent::init();
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::class, 'picture_path');
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%feedback}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['category_id', 'ip', 'response_datetime', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['message'], 'required'],
            ['category_id', 'default', 'value' => 0],
            [['title', 'tel', 'mobile_phone', 'email', 'message', 'response_message'], 'trim'],
            ['email', 'email'],
            [['message', 'response_message'], 'string'],
            [['title'], 'string', 'max' => 100],
            [['username', 'tel'], 'string', 'max' => 20],
            [['mobile_phone'], 'string', 'max' => 11],
            [['email'], 'string', 'max' => 60],
            ['enabled', 'boolean'],
            ['picture', 'image',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
            ],
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'picture'
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => '分类',
            'title' => '标题',
            'username' => '姓名',
            'tel' => '电话号码',
            'mobile_phone' => '手机号码',
            'email' => '邮箱',
            'message' => '内容',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'username',
            'tel',
            'mobile_phone',
            'email',
            'ip',
            'picture' => function () {
                $picture = $this->picture;
                if ($picture) {
                    return \Yii::$app->getRequest()->getHostInfo() . $picture;
                } else {
                    return null;
                }
            },
            'message',
            'response_message',
            'response_datetime',
            'enabled' => function () {
                return boolval($this->enabled);
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            $userId = \Yii::$app->getUser()->getIsGuest() ? 0 : \Yii::$app->getUser()->getId();
            if ($insert) {
                $this->created_by = $this->updated_by = $userId;
                $this->created_at = $this->updated_at = time();
            } else {
                $this->updated_by = $userId;
                $this->updated_at = time();
            }

            return true;
        } else {
            return false;
        }
    }

}
