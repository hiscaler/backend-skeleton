<?php

namespace app\modules\admin\modules\finance\models;

use app\helpers\Config;
use app\models\FileUploadConfig;
use app\models\Member;
use Exception;
use yadjet\behaviors\ImageUploadBehavior;
use Yii;
use yii\db\Expression;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "{{%finance}}".
 *
 * @property int $id
 * @property int $type 类型
 * @property int $money 金额
 * @property int $balance 余额
 * @property int $source 来源
 * @property string $remittance_slip 汇款凭单
 * @property string $related_key 关联业务
 * @property int $status 状态
 * @property string $remark 备注
 * @property int $member_id 会员
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Finance extends \yii\db\ActiveRecord
{

    /**
     * @var bool 是否处理后续的业务逻辑
     */
    public $call_business_process = true;

    /**
     * @var string 文件上传字段
     */
    public $fileFields = 'remittance_slip';

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
        $this->_fileUploadConfig = FileUploadConfig::getConfig(static::class, 'remittance_slip');
    }

    /**
     * 类型选项
     */
    const TYPE_INCOME = 0; // 入账
    const TYPE_DISBURSE = 1; // 支出
    const TYPE_REFUND = 10; // 退款

    /**
     * 来源选项
     */
    const SOURCE_NONE = 0;
    const SOURCE_CASH = 1;
    const SOURCE_WXPAY = 2;
    const SOURCE_ALIPAY = 3;
    const SOURCE_BANK = 4;
    const SOURCE_OTHER = 100;

    /**
     * 状态选项
     */
    const STATUS_PENDING = 0;
    const STATUS_VALID = 1;
    const STATUS_INVALID = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%finance}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'source', 'balance', 'status', 'member_id', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            ['type', 'default', 'value' => self::TYPE_INCOME],
            ['type', 'in', 'range' => array_keys(self::typeOptions())],
            ['source', 'default', 'value' => self::SOURCE_CASH],
            ['source', 'in', 'range' => array_keys(self::sourceOptions())],
            ['money', 'integer', 'min' => 1],
            [['money', 'member_id'], 'required'],
            [['remark'], 'trim'],
            [['remark'], 'string'],
            [['related_key'], 'string', 'max' => 60],
            ['remittance_slip', 'image',
                'extensions' => $this->_fileUploadConfig['extensions'],
                'minSize' => $this->_fileUploadConfig['size']['min'],
                'maxSize' => $this->_fileUploadConfig['size']['max'],
            ],
        ];
    }

    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            [
                'class' => ImageUploadBehavior::class,
                'attribute' => 'remittance_slip',
                'thumb' => $this->_fileUploadConfig['thumb']
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'type' => '类型',
            'money' => '金额',
            'balance' => '余额',
            'source' => '来源',
            'remittance_slip' => '汇款凭单',
            'related_key' => '关联业务',
            'status' => '状态',
            'remark' => '备注',
            'member_id' => '会员',
            'member.username' => '会员',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    /**
     * 类型选项
     *
     * @return array
     */
    public static function typeOptions()
    {
        return [
            self::TYPE_INCOME => "入账",
            self::TYPE_DISBURSE => '支出',
            self::TYPE_REFUND => "退款",
        ];
    }

    /**
     * 来源选项
     *
     * @return array
     */
    public static function sourceOptions()
    {
        return [
            self::SOURCE_NONE => '无',
            self::SOURCE_CASH => '现金',
            self::SOURCE_WXPAY => '微信',
            self::SOURCE_ALIPAY => '支付宝',
            self::SOURCE_BANK => '银行',
            self::SOURCE_OTHER => '其他',
        ];
    }

    /**
     * 状态选项
     *
     * @return array
     */
    public static function statusOptions()
    {
        return [
            self::STATUS_PENDING => '待处理',
            self::STATUS_VALID => '有效',
            self::STATUS_INVALID => '无效',
        ];
    }

    /**
     * 所属会员
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::class, ['id' => 'member_id']);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->type != self::TYPE_INCOME) {
                $this->money = -$this->money;
            }
            $userId = Yii::$app->getUser()->getId() ?: 0;
            if ($insert) {
                $this->balance = 0;
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

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $db = Yii::$app->getDb();
            $money = abs($this->money);
            $availableMoney = (int) $db->createCommand("SELECT [[available_money]] FROM {{%member}} WHERE [[id]] = :id", [':id' => $this->member_id])->queryScalar();
            $columns = [];
            if ($this->type == self::TYPE_INCOME) {
                $columns['total_money'] = new Expression("[[total_money]] + $money");
                $availableMoney = $availableMoney + $money;
            } else {
                $availableMoney = $availableMoney - $money;
            }
            $columns['available_money'] = $availableMoney;

            $cmd = $db->createCommand();
            $cmd->update('{{%member}}', $columns, ['id' => $this->member_id])->execute();
            $cmd->update('{{%finance}}', [
                'balance' => $availableMoney
            ], ['id' => $this->id])->execute();

            if ($this->call_business_process && ($class = Config::get("business.finance.business.class")) && class_exists($class)) {
                try {
                    call_user_func_array([new $class(), 'process'], [$insert, $changedAttributes, $this]);
                } catch (Exception $e) {
                    Yii::error($e->getMessage());
                }
            }
        }
    }

    public function afterDelete()
    {
        parent::afterDelete();
        if ($file = $this->remittance_slip) {
            if (($file = Yii::getAlias('@webroot' . $file)) && file_exists($file)) {
                FileHelper::unlink($file);
            }
        }
    }

}
