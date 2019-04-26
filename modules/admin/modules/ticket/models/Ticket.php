<?php

namespace app\modules\admin\modules\ticket\models;

use app\models\Category;
use yadjet\validators\MobilePhoneNumberValidator;
use Yii;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "{{%ticket}}".
 *
 * @property int $id
 * @property int $category_id 问题类型
 * @property string $title 标题
 * @property string $description 问题描述
 * @property string $confidential_information 机密信息
 * @property string $mobile_phone 手机号码
 * @property string $email 邮箱
 * @property int $status 状态
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Ticket extends \yii\db\ActiveRecord
{

    /**
     * 状态
     */
    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_WAITING_EVALUATION = 2;
    const STATUS_FINISHED = 3;
    const STATUS_CLOSED = 4;

    /**
     * @var array 附件列表
     */
    public $attachment_list = [];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'description', 'confidential_information', 'mobile_phone', 'email'], 'trim'],
            [['description'], 'required'],
            [['description', 'confidential_information'], 'string'],
            [['title', 'email'], 'string', 'max' => 100],
            ['email', 'email'],
            [['mobile_phone'], 'string', 'max' => 12],
            ['mobile_phone', MobilePhoneNumberValidator::class],
            ['status', 'default', 'value' => self::STATUS_PENDING],
            ['status', 'in', 'range' => array_keys(self::statusOptions())],
            ['attachment_list', 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'category_id' => '问题类型',
            'title' => '标题',
            'description' => '问题描述',
            'confidential_information' => '机密信息',
            'mobile_phone' => '手机号码',
            'email' => '邮箱',
            'status' => '状态',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    /**
     * @return array 状态选项
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '待处理',
            self::STATUS_PROCESSING => '处理中',
            self::STATUS_WAITING_EVALUATION => '待评价',
            self::STATUS_FINISHED => '已完成',
            self::STATUS_CLOSED => '已关闭',
        ];
    }

    /**
     * 所属问题类型
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id'])
            ->select(['id', 'alias', 'name', 'short_name', 'icon', 'description']);
    }

    /**
     * 工单附件
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAttachments()
    {
        return $this->hasMany(TicketAttachment::class, ['ticket_id' => 'id']);
    }

    /**
     * 消息
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(TicketMessage::class, ['ticket_id' => 'id'])
            ->orderBy(['id' => SORT_DESC]);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->status = self::STATUS_PENDING;
                $this->created_at = $this->updated_at = time();
                $this->created_by = $this->updated_by = \Yii::$app->getUser()->getId();
            } else {
                $this->updated_at = time();
                $this->updated_by = \Yii::$app->getUser()->getId();
            }

            $this->title = StringHelper::truncate($this->description, 100, '');

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\base\Exception
     * @throws \yii\db\Exception
     * @throws \Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $files = UploadedFile::getInstancesByName('attachment_list');
            if ($files) {
                $rows = [];
                $url = '/uploads/' . date('Ymd');
                $saveDir = Yii::getAlias('@webroot') . $url;
                if (!file_exists($saveDir)) {
                    FileHelper::createDirectory($saveDir);
                }
                foreach ($files as $file) {
                    if ($file) {
                        $path = $url . '/' . \yadjet\helpers\StringHelper::generateRandomString() . '.' . $file->getExtension();
                        if ($file->saveAs($path)) {
                            $rows[] = [
                                'ticket_id' => $this->id,
                                'path' => $path,
                            ];
                        }
                    }
                }
                if ($rows) {
                    \Yii::$app->getDb()->createCommand()->batchInsert('{{%ticket_attachment}}', array_keys($rows[0]), $rows)->execute();
                }
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        TicketAttachment::deleteAll(['ticket_id' => $this->id]);
    }

}
