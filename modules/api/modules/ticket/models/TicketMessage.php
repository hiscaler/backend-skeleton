<?php

namespace app\modules\api\modules\ticket\models;

class TicketMessage extends \app\modules\admin\modules\ticket\models\TicketMessage
{

    public function fields()
    {
        return [
            'id',
            'ticket_id',
            'type',
            'content',
            'parent_id',
            'member_id',
            'reply_user_id',
            'reply_username',
            'created_at',
        ];
    }

    public function extraFields()
    {
        return ['ticket'];
    }

}