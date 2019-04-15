<?php

namespace app\modules\api\modules\ticket\models;

class Ticket extends \app\modules\admin\modules\ticket\models\Ticket
{

    public function fields()
    {
        return [
            'id',
            'category_id',
            'title',
            'description',
            'confidential_information',
            'mobile_phone',
            'email',
            'status',
            'status' => function ($model) {
                $options = Ticket::statusOptions();

                return isset($options[$model->status]) ? $options[$model->status] : null;
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

    public function extraFields()
    {
        return ['category', 'attachments'];
    }

}