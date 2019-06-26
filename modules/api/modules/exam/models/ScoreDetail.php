<?php

namespace app\modules\api\modules\exam\models;

class ScoreDetail extends \app\modules\admin\modules\exam\models\ScoreDetail
{

    public function fields()
    {
        return [
            'id',
            'score_id',
            'bank_id',
            'question_id',
            'answer',
            'answer_datetime',
            'status',
            'score',
        ];
    }

}