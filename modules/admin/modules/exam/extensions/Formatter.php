<?php

namespace app\modules\admin\modules\exam\extensions;

use app\modules\admin\modules\exam\models\Question;
use app\modules\admin\modules\exam\models\QuestionBank;
use app\modules\admin\modules\exam\models\Score;

/**
 * Class Formatter
 *
 * @package app\modules\admin\modules\exam\extensions
 * @author hiscaler <hiscaler@gmail.com>
 */
class Formatter extends \app\modules\admin\extensions\Formatter
{

    /**
     * 题库状态
     *
     * @param $value
     * @return string|null
     */
    public function asQuestionBankStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = QuestionBank::statusOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * 试题类型
     *
     * @param $value
     * @return string|null
     */
    public function asQuestionType($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Question::typeOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * 试题状态
     *
     * @param $value
     * @return string|null
     */
    public function asQuestionStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Question::statusOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

    /**
     * 考试状态
     *
     * @param $value
     * @return string|null
     */
    public function asScoreStatus($value)
    {
        if ($value === null) {
            return $this->nullDisplay;
        }
        $options = Score::statusOptions();

        return isset($options[$value]) ? $options[$value] : null;
    }

}
