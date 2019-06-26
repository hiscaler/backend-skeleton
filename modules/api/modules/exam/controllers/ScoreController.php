<?php

namespace app\modules\api\modules\exam\controllers;

use app\modules\api\modules\exam\forms\ScoreForm;
use app\modules\api\modules\exam\models\Question;
use app\modules\api\modules\exam\models\Score;
use Yii;
use yii\db\Query;
use yii\helpers\Url;
use yii\web\Response;
use yii\web\ServerErrorHttpException;

/**
 * 考试成绩
 *
 * @package app\modules\api\modules\exam\controllers
 */
class ScoreController extends Controller
{

    public $modelClass = Score::class;

    /**
     * 提交考试成绩
     *
     * @return Response
     * @throws \yii\db\Exception
     */
    public function _actionSubmit()
    {
        $request = Yii::$app->getRequest();
        $success = false;
        $errorMessage = null;
        if ($request->isPost) {
            $beginDatetime = $request->post('beginDatetime');
            $bankId = (int) $request->post('bankId');
            $rawAnswers = $request->post('answers');
            if ($beginDatetime && $rawAnswers) {
                $answers = [];
                foreach ($rawAnswers as $answer) {
                    $answers[$answer['id']] = isset($answer['value']) ? $answer['value'] : null;
                }
                $where = ['id' => array_keys($answers)];
                if ($bankId) {
                    $where['question_bank_id'] = $bankId;
                }
                $questions = (new Query())
                    ->from('{{%exam_question}}')
                    ->where($where)
                    ->indexBy('id')
                    ->all();
                $totalCount = count($questions);
                $correctCount = 0;
                if (count($rawAnswers) == $totalCount) {
                    foreach ($questions as $key => $question) {
                        if ($question['type'] == Question::TYPE_MULTIPLE_CHOICE) {
                            $answer = explode(PHP_EOL, $question['answer']);
                            foreach ($answer as $kk => $vv) {
                                $answer[$kk] = trim($vv);
                            }
                        } else {
                            $answer = trim($question['answer']);
                        }
                        if ($answers[$key] == $answer) {
                            $correctCount++;
                        }
                    }
                    $columns = [
                        'member_id' => Yii::$app->getUser()->getId(),
                        'question_bank_id' => $bankId,
                        'total_questions_count' => $totalCount,
                        'correct_questions_count' => $correctCount,
                        'begin_datetime' => $beginDatetime,
                        'end_datetime' => time(),
                        'score' => $correctCount,
                    ];
                    $db = Yii::$app->getDb();
                    $cmd = $db->createCommand();
                    $columns = [
                        'begin_datetime' => $beginDatetime,
                        'end_datetime' => time(),
                        'questions_count' => $totalCount,
                        'last_answer_datetime' => time(),
                        'status' => Score::STATUS_FINISHED,
                        'score' => $correctCount,
                        'member_id' => 0,
                    ];
                    $cmd->insert('{{%exam_score}}', $columns)->execute();
                    $scoreId = $db->getLastInsertID();
                    $detailColumns = [];
                    foreach ($answers as $questionId => $answer) {
                        $detailColumns[] = [
                            'score_id' => $scoreId,
                            'bank_id' => $bankId,
                            'question_id' => $questionId,
                            'answer' => $answer,
                            'answer_datetime' => time(),
                            'status' => Score::STATUS_FINISHED,
                            'score' => 1,
                        ];
                    }
                    $cmd->insert('{{%exam_score_detail}}', $detailColumns)->execute();
                    $data = [
                        'redirectUrl' => Url::toRoute(['exam/score', 'id' => $scoreId], true),
                        'total' => $totalCount,
                        'correctCount' => $correctCount,
                        'score' => ceil($correctCount / $totalCount),
                    ];
                    if ($bankId) {
                        $db->createCommand('UPDATE {{%question_bank}} SET [[participation_times]] = [[participation_times]] + 1 WHERE [[id]] =:id', [':id' => $bankId])->execute();
                    }
                    $success = true;
                } else {
                    $errorMessage = '提交数据和当前题库数据不一致。';
                }
            } else {
                $errorMessage = '提交数据有误。';
            }
        } else {
            $errorMessage = '该方法仅接受 POST 请求。';
        }

        $responseBody = ['success' => $success];
        if ($success) {
            if (isset($data)) {
                $responseBody['data'] = $data;
            }
        } else {
            $responseBody['error']['message'] = $errorMessage;
        }

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => $responseBody,
        ]);
    }

    /**
     * 交卷
     *
     * @return ScoreForm
     * @throws ServerErrorHttpException
     * @throws \yii\base\InvalidConfigException
     */
    public function actionSubmit()
    {
        $model = new ScoreForm();
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            Yii::$app->getResponse()->setStatusCode(201);
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

}