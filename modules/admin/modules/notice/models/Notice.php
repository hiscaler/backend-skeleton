<?php

namespace app\modules\admin\modules\notice\models;

use app\models\BaseWithLabelActiveRecord;
use app\models\Constant;
use app\models\Member;
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
class Notice extends BaseWithLabelActiveRecord
{

    /**
     * @var string 指定的会员
     */
    public $view_member_username_list = '';

    /**
     * @var string 指定的会员等级
     */
    public $view_member_type_list = '';

    /**
     * 查看权限
     */
    const VIEW_PERMISSION_ALL = 0;
    const VIEW_PERMISSION_SPECIAL = 1;
    const VIEW_PERMISSION_BY_MEMBER_TYPE = 2;

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
            self::SCENARIO_DEFAULT => self::OP_ALL,
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
            ['view_member_username_list', 'string'],
            ['view_member_username_list', 'trim'],
            ['view_member_username_list', 'required', 'when' => function ($model) {
                return $model->view_permission == self::VIEW_PERMISSION_SPECIAL;
            }, 'whenClient' => "function (attribute, value) {
                return $('#notice-view_permission').val() == 1;
            }"],
            ['view_member_username_list', function ($attribute, $params) {
                if ($this->view_permission == self::VIEW_PERMISSION_SPECIAL) {
                    $usernameList = array_filter(array_unique(explode(',', $this->view_member_username_list)));
                    if ($usernameList) {
                        $n = (new Query())
                            ->from('{{%member}}')
                            ->where(['username' => $usernameList])
                            ->count();
                        $hasError = $n != count($usernameList);
                    } else {
                        $hasError = true;
                    }
                    if ($hasError) {
                        $this->addError($attribute, '允许的会员数据有误。');
                    } else {
                        $this->view_member_username_list = implode(',', $usernameList);
                    }
                } else {
                    $this->view_member_username_list = '';
                }
            }],
            ['view_member_type_list', 'safe'],
            ['view_member_type_list', 'required', 'when' => function ($model) {
                return $model->view_permission == self::VIEW_PERMISSION_BY_MEMBER_TYPE;
            }, 'whenClient' => "function (attribute, value) {
                if ($('#notice-view_permission').val() == 2) {
                    return $('input[name=Notice\\\[view_member_type_list\\\]\\\[\\\]]:checked').length <= 0;
                }

                return false;
            }"],
            ['view_member_type_list', function ($attribute, $params) {
                if ($this->view_permission == self::VIEW_PERMISSION_BY_MEMBER_TYPE) {
                    $memberTypes = $this->view_member_type_list;
                    if (is_array($memberTypes) && $memberTypes) {
                        $validMemberTypes = Member::typeOptions();
                        $hasError = false;
                        foreach ($memberTypes as $type) {
                            if (!isset($validMemberTypes[$type])) {
                                $hasError = true;
                                break;
                            }
                        }
                    } else {
                        $hasError = true;
                    }
                    if ($hasError) {
                        $this->addError($attribute, '允许的会员等级有误。');
                    }
                } else {
                    $this->view_member_type_list = [];
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
            'view_member_username_list' => '允许会员帐号',
            'view_member_type_list' => '允许会员等级',
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
            self::VIEW_PERMISSION_BY_MEMBER_TYPE => '会员等级',
        ];
    }

    /**
     * 阅读数据
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRead()
    {
        return $this->hasOne(NoticeView::class, ['notice_id' => 'id'])->where([
            'member_id' => \Yii::$app->getUser()->getId(),
        ]);
    }

    // Events

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
        switch ($this->view_permission) {
            case self::VIEW_PERMISSION_SPECIAL:
                $xids = (new Query())
                    ->select(['id'])
                    ->from('{{%member}}')
                    ->where(['username' => explode(',', $this->view_member_username_list)])
                    ->column();
                break;

            case self::VIEW_PERMISSION_BY_MEMBER_TYPE:
                $xids = $this->view_member_type_list;
                break;

            default:
                $xids = [];
                break;
        }
        if ($insert) {
            $insertXids = $xids;
            $deleteXids = [];
        } else {
            $existXids = $db->createCommand('SELECT [[xid]] FROM {{%notice_permission}} WHERE [[notice_id]] = :noticeId', [
                ':noticeId' => $this->id,
            ])->queryColumn();
            $insertXids = array_diff($xids, $existXids);
            $deleteXids = array_diff($existXids, $xids);
        }
        if ($insertXids) {
            $rows = [];
            foreach ($insertXids as $memberId) {
                $rows[] = [
                    'notice_id' => $this->id,
                    'xid' => $memberId,
                ];
            }
            $cmd->batchInsert('{{%notice_permission}}', array_keys($rows[0]), $rows)->execute();
        }
        $deleteXids && $cmd->delete('{{%notice_permission}}', ['notice_id' => $this->id, 'xid' => $deleteXids])->execute();
    }

    /**
     * @throws \Throwable
     * @throws \yii\db\Exception
     */
    public function afterDelete()
    {
        parent::afterDelete();
        $cmd = \Yii::$app->getDb()->createCommand();
        if ($this->view_permission == self::VIEW_PERMISSION_SPECIAL) {
            $cmd->delete('{{%notice_permission}}', [
                'notice_id' => $this->id
            ])->execute();
        }
        $cmd->delete('{{%notice_view}}', [
            'notice_id' => $this->id
        ])->execute();
    }

}
