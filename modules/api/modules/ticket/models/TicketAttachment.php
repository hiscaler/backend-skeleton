<?php

namespace app\modules\api\modules\ticket\models;

class TicketAttachment extends \app\modules\admin\modules\ticket\models\Ticket
{

    public function fields()
    {
        return [
            'id',
            'ticket_id',
            'path',
        ];
    }

}