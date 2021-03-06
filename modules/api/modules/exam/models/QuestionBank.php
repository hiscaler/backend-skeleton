<?php

namespace app\modules\api\modules\exam\models;

use app\modules\api\modules\exam\extensions\Formatter;
use Yii;

class QuestionBank extends \app\modules\admin\modules\exam\models\QuestionBank
{

    public function fields()
    {
        /* @var $formatter Formatter */
        $formatter = Yii::$app->getFormatter();

        return [
            'id',
            'name',
            'description',
            'icon' => function ($model) use ($formatter) {
                return $formatter->asAssetFullPath($model->icon);
            },
            'questions_count',
            'participation_times',
            'status',
            'status_formatted' => function ($model) use ($formatter) {
                return $formatter->asQuestionBankStatus($model->source);
            },
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
        ];
    }

    /**
     * 试题
     *
     * @return \yii\db\ActiveQuery
     */
    public function getQuestions()
    {
        return $this->hasMany(Question::class, ['question_bank_id' => 'id']);
    }

    public function extraFields()
    {
        return ['questions'];
    }

}