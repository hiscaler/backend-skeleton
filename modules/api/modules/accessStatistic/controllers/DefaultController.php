<?php

namespace app\modules\api\modules\accessStatistic\controllers;

use Yii;
use yii\web\Response;

/**
 * /api/feedback/default
 *
 * @author hiscaler <hiscaler@gmail.com>
 */
class DefaultController extends Controller
{

    public $modelClass = 'app\modules\api\modules\accessStatistic\models\AccessStatisticSiteLog';

    /**
     * 搜集统计数据
     *
     * @param $siteId
     * @return Response
     * @throws \yii\db\Exception
     */
    public function actionSubmit($siteId)
    {
        $request = Yii::$app->getRequest();
        $db = \Yii::$app->getDb();

        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $browser = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/MSIE/i', $browser)) {
                $browser = 'MSIE';
            } elseif (preg_match('/Firefox/i', $browser)) {
                $browser = 'Firefox';
            } elseif (preg_match('/Chrome/i', $browser)) {
                $browser = 'Chrome';
            } elseif (preg_match('/Safari/i', $browser)) {
                $browser = 'Safari';
            } elseif (preg_match('/Opera/i', $browser)) {
                $browser = 'Opera';
            } else {
                $browser = 'Other';
            }
        } else {
            $browser = null;
        }
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            $os = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/win/i', $os)) {
                $os = 'Windows';
            } elseif (preg_match('/mac/i', $os)) {
                $os = 'MAC';
            } elseif (preg_match('/linux/i', $os)) {
                $os = 'Linux';
            } elseif (preg_match('/unix/i', $os)) {
                $os = 'Unix';
            } elseif (preg_match('/bsd/i', $os)) {
                $os = 'BSD';
            } else {
                $os = 'Other';
            }
        } else {
            $os = null;
        }

        $columns = [
            'site_id' => $siteId,
            'ip' => $request->getUserIP(),
            'referrer' => $request->getReferrer() ?: '',
            'browser' => $browser,
            'browser_lang' => $request->getPreferredLanguage(),
            'os' => $os,
            'access_datetime' => time(),
        ];
        $db->createCommand()->insert('{{%access_statistic_site_log}}', $columns)->execute();

        return new Response([
            'format' => Response::FORMAT_JSON,
            'data' => [
                'success' => true,
            ]
        ]);
    }

}
