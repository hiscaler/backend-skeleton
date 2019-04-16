<?php

namespace app\modules\admin\modules\notice\models;

use app\models\BaseActiveRecord;
use app\models\Constant;
use Yii;
use yii\db\Query;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "{{%notice}}".
 *
 * @property int $id
 * @property int $category_id 所属分类
 * @property string $title 标题
 * @property string $description 描述
 * @property string $content 正文
 * @property int $enabled 激活
 * @property int $clicks_count 点击次数
 * @property int $published_at 发布时间
 * @property int $ordering 排序
 * @property int $view_permission 查看权限
 * @property int $created_at 添加时间
 * @property int $created_by 添加人
 * @property int $updated_at 更新时间
 * @property int $updated_by 更新人
 */
class Notice extends BaseActiveRecord
{

    /**
     * @var array 指定的会员
     */
    public $view_member_id_list = '';

    /**
     * 查看权限
     */
    const VIEW_PERMISSION_ALL = 0;
    const VIEW_PERMISSION_SPECIAL = 1;
    const VIEW_PERMISSION_BY_MEMBER_LEVEL = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notice}}';
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DELETE => self::OP_DELETE,
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['category_id', 'enabled', 'clicks_count', 'ordering', 'view_permission', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            ['view_permission', 'default', 'value' => self::VIEW_PERMISSION_ALL],
            ['view_permission', 'in', 'range' => array_keys(self::viewPermissionOptions())],
            [['title', 'content', 'published_at'], 'required'],
            ['published_at', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'published_at'],
            [['title', 'description', 'content'], 'trim'],
            [['description', 'content'], 'string'],
            [['title'], 'string', 'max' => 160],
            ['category_id', 'default', 'value' => 0],
            ['clicks_count', 'default', 'value' => 0],
            ['enabled', 'boolean'],
            ['enabled', 'default', 'value' => Constant::BOOLEAN_TRUE],
            ['published_at', 'datetime', 'format' => 'php:Y-m-d H:i:s', 'timestampAttribute' => 'published_at'],
            ['view_member_id_list', 'string'],
            ['view_member_id_list', 'trim'],
            ['view_member_id_list', 'required', 'when' => function ($model) {
                return $model->view_permission == self::VIEW_PERMISSION_SPECIAL;
            }],
            ['view_member_id_list', function ($attribute, $params) {
                if ($this->view_permission == self::VIEW_PERMISSION_SPECIAL) {
                    $memberIds = array_filter(array_unique(explode(',', $this->view_member_id_list)));
                    if ($memberIds) {
                        $n = (new Query())->from('{{%member}}')
                            ->where(['id' => $memberIds])
                            ->count();
                        $hasError = $n != count($memberIds);
                    } else {
                        $hasError = true;
                    }
                    if ($hasError) {
                        $this->addError($attribute, '允许的会员数据有误。');
                    } else {
                        $this->view_member_id_list = implode(',', $memberIds);
                    }
                }
            }],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'category_id' => '所属分类',
            'title' => '标题',
            'description' => '描述',
            'content' => '正文',
            'enabled' => '激活',
            'clicks_count' => '点击次数',
            'published_at' => '发布时间',
            'view_permission' => '查看权限',
            'view_member_id_list' => '允许查看的人员',
            'ordering' => '排序',
            'created_at' => '添加时间',
            'created_by' => '添加人',
            'updated_at' => '更新时间',
            'updated_by' => '更新人',
        ];
    }

    /**
     * 查看权限列表
     *
     * @return array
     */
    public static function viewPermissionOptions()
    {
        return [
            self::VIEW_PERMISSION_ALL => '所有人员',
            self::VIEW_PERMISSION_SPECIAL => '指定人员',
            self::VIEW_PERMISSION_BY_MEMBER_LEVEL => '会员等级',
        ];
    }

    // Events

    /**
     * @throws \yii\base\InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function afterFind()
    {
        parent::afterFind();
        if (!$this->isNewRecord) {
            $this->published_at = Yii::$app->getFormatter()->asDatetime($this->published_at);
        }
    }

    /**
     * @param $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->clicks_count = 0;
            }
            if (!$this->description) {
                $this->description = StringHelper::truncate(\yadjet\helpers\StringHelper::html2Text($this->content), 300, '');
            }

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
        // 允许查看的会员数据处理
        $db = \Yii::$app->getDb();
        $cmd = $db->createCommand();
        $memberIds = $this->view_permission == self::VIEW_PERMISSION_SPECIAL ? explode(',', $this->view_member_id_list) : [];
        if ($insert) {
            $insertMemberIds = $memberIds;
            $deleteIds = [];
        } else {
            $existMemberIds = $db->createCommand('SELECT [[member_id]] FROM {{%notice_permission}} WHERE [[notice_id]] = :noticeId', [
                ':noticeId' => $this->id,
            ])->queryColumn();
            $insertMemberIds = array_diff($memberIds, $existMemberIds);
            $deleteIds = array_diff($existMemberIds, $memberIds);
        }
        if ($insertMemberIds) {
            $rows = [];
            foreach ($insertMemberIds as $memberId) {
                $rows[] = [
                    'notice_id' => $this->id,
                    'member_id' => $memberId,
                ];
            }
            $cmd->batchInsert('{{%notice_permission}}', array_keys($rows[0]), $rows)->execute();
        }
        $deleteIds && $cmd->delete('{{%notice_permission}}', ['notice_id' => $this->id, 'member_id' => $deleteIds])->execute();
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        if ($this->view_permission == self::VIEW_PERMISSION_SPECIAL) {
            \Yii::$app->getDb()->createCommand()->delete('{{%notice_permission}}', [
                'notice_id' => $this->id
            ])->execute();
        }
    }

}
