<?php

namespace app\modules\api\modules\exam\models;

class Score extends \app\modules\admin\modules\exam\models\Score
{

    public function fields()
    {
        return [
            'id',
            'flag',
            'begin_datetime',
            'end_datetime',
            'questions_count',
            'last_answer_datetime',
            'status',
            'score',
            'member_id',
        ];
    }

}