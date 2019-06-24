<?php

namespace app\modules\admin\modules\ticket\models;

use Yii;
use yii\helpers\FileHelper;

/**
 * This is the model class for table "{{%ticket_attachment}}".
 *
 * @property int $id
 * @property int $ticket_id 所属工单
 * @property string $path 附件地址
 */
class TicketAttachment extends \yii\db\ActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket_attachment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'path'], 'required'],
            [['ticket_id'], 'integer'],
            [['path'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => '所属工单',
            'path' => '附件地址',
        ];
    }

    // Events
    public function afterDelete()
    {
        parent::afterDelete();
        FileHelper::unlink(Yii::getAlias('@webroot') . $this->path);
    }

}
