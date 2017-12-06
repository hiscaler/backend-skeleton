<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user_credit_log}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $operation
 * @property string $related_key
 * @property integer $credits
 * @property string $remark
 * @property integer $created_at
 * @property integer $created_by
 */
class UserCreditLog extends \yii\db\ActiveRecord
{

    /**
     * 积分类型
     */
    const OPERATION_USER_SIGNUP = 'user_signup'; // 用户注册
    const OPERATION_REFERRAL_SIGNUP = 'referral_signup'; // 推荐注册
    const OPERATION_MANUAL = 'manual'; // 手动添加

    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return '{{%user_credit_log}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'operation', 'credits', 'created_at'], 'required'],
            [['user_id', 'credits', 'created_at', 'created_by'], 'integer'],
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
            'user_id' => Yii::t('userCreditLog', 'User ID'),
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
            self::OPERATION_USER_SIGNUP => Yii::t('userCreditLog', 'Operation User Signup'),
            self::OPERATION_REFERRAL_SIGNUP => Yii::t('userCreditLog', 'Operation Referral Signup'),
            self::OPERATION_MANUAL => Yii::t('userCreditLog', 'Operation Manual'),
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
     * @param integer $userId
     * @param string $operation
     * @param integer $credits
     * @param string $relatedKey
     * @param string $remark
     * @param integer $posterId
     * @return boolean
     * @throws \Exception
     */
    public static function add($userId, $operation, $credits, $relatedKey = null, $remark = null, $posterId = null)
    {
        $userId = abs((int) $userId);
        $credits = (int) $credits;
        $operation = trim($operation);
        if (!$userId || $credits == 0 || !isset(self::operationOptions()[$operation])) {
            return false;
        }

        $db = Yii::$app->getDb();
        $userExists = $db->createCommand('SELECT COUNT(*) FROM {{%user}} WHERE [[id]] = :id', [':id' => $userId])->queryScalar();
        if ($userExists) {
            $transaction = $db->beginTransaction();
            try {
                $posterId = (int) $posterId;
                if (!$posterId) {
                    $user = Yii::$app->getUser();
                    $posterId = $user->isGuest ? 0 : $user->getId();
                }
                $columns = [
                    'user_id' => $userId,
                    'operation' => $operation,
                    'credits' => $credits,
                    'related_key' => trim($relatedKey),
                    'remark' => $remark,
                    'created_at' => time(),
                    'created_by' => $posterId,
                ];
                $result = $db->createCommand()->insert('{{%user_credit_log}}', $columns)->execute() ? true : false;
                if ($result) {
                    $sql = "UPDATE {{%user}} SET [[credits_count]] = [[credits_count]]";
                    $sql .= ($credits ? ' + ' : ' - ');
                    $sql .= abs($credits) . ' WHERE [[id]] = :id';
                    $db->createCommand($sql, [':id' => $userId])->execute();
                    User::fixUserGroup($userId);
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
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

}
