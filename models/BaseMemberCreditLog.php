<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%member_credit_log}}".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $operation
 * @property string $related_key
 * @property integer $credits
 * @property string $remark
 * @property integer $created_at
 * @property integer $created_by
 */
class BaseMemberCreditLog extends \yii\db\ActiveRecord
{

    /**
     * 积分类型
     */
    const OPERATION_MEMBER_REGISTER = 'member.register'; // 会员注册
    const OPERATION_REFERRAL_REGISTER = 'referral.register'; // 推荐注册
    const OPERATION_MEMBER_LOGIN = 'member.login'; // 会员登录
    const OPERATION_MANUAL = 'manual'; // 手动添加

    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return '{{%member_credit_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'operation', 'credits'], 'required'],
            [['member_id', 'credits', 'created_at', 'created_by'], 'integer'],
            ['credits', function ($attribute, $params) {
                if ($this->credits == 0) {
                    $this->addError($attribute, "积分值错误。");
                }
            }],
            [['operation', 'remark'], 'trim'],
            [['remark'], 'string'],
            [['operation'], 'string', 'max' => 20],
            [['related_key'], 'string', 'max' => 60],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('userCreditLog', 'ID'),
            'member_id' => Yii::t('userCreditLog', 'User ID'),
            'operation' => Yii::t('userCreditLog', 'Operation'),
            'operation_formatted' => Yii::t('userCreditLog', 'Operation'),
            'related_key' => Yii::t('userCreditLog', 'Related Key'),
            'credits' => Yii::t('userCreditLog', 'Credits'),
            'remark' => Yii::t('userCreditLog', 'Remark'),
            'created_at' => Yii::t('userCreditLog', 'Created At'),
            'created_by' => Yii::t('userCreditLog', 'Created By'),
        ];
    }

    /**
     * 积分类型选择
     *
     * @return array
     */
    public static function operationOptions()
    {
        $default = [
            static::OPERATION_MEMBER_REGISTER => Yii::t('memberCreditLog', 'Member Register'),
            static::OPERATION_REFERRAL_REGISTER => Yii::t('memberCreditLog', 'Referral Register'),
            static::OPERATION_MEMBER_LOGIN => Yii::t('memberCreditLog', 'Member Login'),
            static::OPERATION_MANUAL => Yii::t('memberCreditLog', 'Manual'),
        ];
        // 自定义积分类型 @todo 从语言文件中获取相应的定义
        $custom = [];

        return array_merge($custom, $default);
    }

    public function getOperation_formatted()
    {
        $options = self::operationOptions();

        return isset($options[$this->operation]) ? $options[$this->operation] : null;
    }

    /**
     *  添加积分记录
     *
     * @param integer $memberId
     * @param string $operation
     * @param integer $credits
     * @param string $relatedKey
     * @param string $remark
     * @param integer $posterId
     * @return boolean
     * @throws \Exception
     */
    public static function add($memberId, $operation, $credits, $relatedKey = null, $remark = null, $posterId = null)
    {
        $memberId = abs((int) $memberId);
        $credits = (int) $credits;
        $operation = trim($operation);
        if (!$memberId || $credits == 0 || !isset(self::operationOptions()[$operation])) {
            return false;
        }

        $db = Yii::$app->getDb();
        $memberExists = $db->createCommand('SELECT COUNT(*) FROM {{%member}} WHERE [[id]] = :id', [':id' => $memberId])->queryScalar();
        if ($memberExists) {
            $transaction = $db->beginTransaction();
            try {
                $posterId = (int) $posterId;
                if (!$posterId) {
                    $user = Yii::$app->getUser();
                    $posterId = $user->getIsGuest() ? 0 : $user->getId();
                }
                $columns = [
                    'member_id' => $memberId,
                    'operation' => $operation,
                    'credits' => $credits,
                    'related_key' => trim($relatedKey),
                    'remark' => $remark,
                    'created_at' => time(),
                    'created_by' => $posterId,
                ];
                $result = $db->createCommand()->insert('{{%member_credit_log}}', $columns)->execute() ? true : false;
                if ($result) {
                    $op = $credits ? ' + ' : ' - ';
                    $credits = abs($credits);
                    $db->createCommand("UPDATE {{%member}} SET [[total_credits]] = [[total_credits]] $op $credits, [[available_credits]] = [[available_credits]] $op $credits WHERE [[id]] = :id", [':id' => $memberId])->execute();
                    Member::updateGroup($memberId);
                }
                $transaction->commit();

                return $result;
            } catch (\Exception $e) {
                $transaction->rollBack();
                throw $e;
            }
        } else {
            return false;
        }
    }

    public function getCreater()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    // Events
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->created_at = time();
                $this->created_by = \Yii::$app->getUser()->getId();
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
            $credits = $this->credits;
            $op = $credits ? ' + ' : ' - ';
            $credits = abs($credits);
            \Yii::$app->getDb()->createCommand("UPDATE {{%member}} SET [[total_credits]] = [[total_credits]] $op $credits, [[available_credits]] = [[available_credits]] $op $credits WHERE [[id]] = :id", [':id' => $this->member_id])->execute();
            Member::updateGroup($this->member_id);
        }
    }

}
