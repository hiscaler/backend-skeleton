<?php

namespace app\modules\api\modules\exam\forms;

use app\modules\api\modules\exam\models\Question;
use app\modules\api\modules\exam\models\Score;
use app\modules\api\modules\exam\models\ScoreDetail;
use Yii;
use yii\db\Query;

/**
 * 成绩添加表单
 *
 * @package app\modules\api\modules\exam\forms
 * @author hiscaler <hiscaler@gmail.com>
 */
class ScoreForm extends Score
{

    public $answers = [];

    private $_answers = [];

    public function rules()
    {
        return array_merge(parent::rules(), [
            ['answers', 'required'],
            ['answers', function ($attribute, $params) {
                $rawAnswers = $this->answers;
                if (!is_array($rawAnswers)) {
                    $this->addError($attribute, '试卷内容有误。');
                } else {
                    $answers = []; // k => v 格式
                    foreach ($rawAnswers as $answer) {
                        if (isset($answer['id'])) {
                            $answers[$answer['id']] = isset($answer['value']) ? $answer['value'] : null;
                        }
                    }
                    if (count($rawAnswers) != count($this->answers)) {
                        $this->addError($attribute, '试卷提交格式不正确。');
                    } else {
                        $questions = (new Query())
                            ->from('{{%exam_question}}')
                            ->where(['id' => array_keys($answers)])
                            ->indexBy('id')
                            ->all();

                        if (count($questions) == count($answers)) {
                            $this->_answers = $answers;
                        } else {
                            $this->addError($attribute, "试卷提交格式不正确。");
                        }
                    }
                }
            }],
        ]);
    }

    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($insert) {
                $this->questions_count = count($this->_answers);
            }

            return true;
        } else {
            return false;
        }
    }

    /**
     * @param bool $insert
     * @param array $changedAttributes
     * @throws \yii\db\Exception
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        if ($insert) {
            $totalScore = 0;
            $questions = (new Query())
                ->select(['id', 'type', 'answer'])
                ->from('{{%exam_question}}')
                ->where(['id' => array_keys($this->_answers)])
                ->indexBy('id')
                ->all();
            foreach ($questions as $key => $question) {
                $correct = false;
                if ($question['type'] == Question::TYPE_MULTIPLE_CHOICE) {
                    $answer = explode(PHP_EOL, $question['answer']);
                    foreach ($answer as $kk => $vv) {
                        $answer[$kk] = trim($vv);
                    }
                } else {
                    $answer = trim($question['answer']);
                }
                if ($this->_answers[$key] == $answer) {
                    $correct = true;
                    $totalScore++;
                }
                $detail = new ScoreDetail();
                $detail->score_id = $this->id;
                $detail->question_id = $key;
                $detail->score = $correct ? 1 : 0;
                $detail->save();
            }
            $totalScore && Yii::$app->getDb()->createCommand()->update('{{%exam_score}}', ['score' => $totalScore], ['id' => $this->id])->execute();
        }
    }

}