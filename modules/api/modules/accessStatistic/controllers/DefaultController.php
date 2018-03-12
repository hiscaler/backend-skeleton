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
        $columns = [
            'site_id' => $siteId,
            'ip' => $request->getUserIP(),
            'referrer' => $request->getReferrer() ?: '',
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
