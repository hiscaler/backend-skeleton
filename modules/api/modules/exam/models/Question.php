<?php

namespace app\modules\api\modules\exam\models;

use app\modules\api\modules\exam\extensions\Formatter;
use Yii;

class Question extends \app\modules\admin\modules\exam\models\Question
{

    public function fields()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->getFormatter();

        return [
            'id',
            'question_bank_id',
            'type',
            'status',
            'status_formatted' => function ($model) use ($formatter) {
                return $formatter->asQuestionStatus($model->source);
            },
            'content',
            'options',
            'answer',
            'resolve',
        ];
    }

    public function extraFields()
    {
        return ['questions'];
    }

}