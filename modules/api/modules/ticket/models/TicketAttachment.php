<?php

namespace app\modules\api\modules\ticket\models;

use app\modules\api\extensions\AppHelper;

class TicketAttachment extends \app\modules\admin\modules\ticket\models\Ticket
{

    public function fields()
    {
        return [
            'id',
            'ticket_id',
            'path' => function ($model) {
                return AppHelper::fixStaticAssetUrl($model->path);
            },
        ];
    }

}