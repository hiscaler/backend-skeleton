<?php

namespace app\modules\api\modules\notice\models;

use app\modules\api\extensions\yii\data\ActiveWithStatisticsDataProvider;
use app\modules\api\models\Member;
use yii\data\ActiveDataProvider;
use yii\db\Query;

class NoticeSearch extends Notice
{

    /**
     * @var null|string 是否已经读取消息
     */
    public $read = null;

    public function rules()
    {
        return [
            [['title', 'read'], 'string'],
            [['title', 'read'], 'trim'],
            [['category_id', 'enabled'], 'integer'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        /* @var $member Member */
        $member = \Yii::$app->getUser()->getIdentity();
        $condition = [
            'OR',
            ['view_permission' => Notice::VIEW_PERMISSION_ALL],
            "[[view_permission]] = :viewSpecialPermission AND [[id]] IN (SELECT [[notice_id]] FROM {{%notice_permission}} WHERE [[xid]] = :memberId)",
            "[[view_permission]] = :viewMemberTypePermission AND [[id]] IN (SELECT [[notice_id]] FROM {{%notice_permission}} WHERE [[xid]] = :type)"
        ];
        $query = Notice::find()->with(['read'])
            ->where($condition, [
                ':viewSpecialPermission' => Notice::VIEW_PERMISSION_SPECIAL,
                ':memberId' => $member->id,
                ':viewMemberTypePermission' => Notice::VIEW_PERMISSION_BY_MEMBER_TYPE,
                ':type' => $member->type,
            ]);

        $dataProvider = new ActiveWithStatisticsDataProvider([
            'query' => $query,
            'statistics' => function ($models, $query) {
                $statFunc = function ($items) {
                    $totalCount = $hasReadCount = 0;
                    foreach ($items as $item) {
                        $totalCount++;
                        if ($item->read) {
                            $hasReadCount++;
                        }
                    };

                    return [
                        'total' => $totalCount,
                        'has_read' => $hasReadCount,
                        'unread' => $totalCount - $hasReadCount,
                    ];
                };

                /* @var $query Query */
                $items = $query
                    ->select(['id'])
                    ->offset(null)
                    ->limit(null)
                    ->where('')
                    ->orderBy([])
                    ->all();

                return [
                    'current' => $statFunc($models),
                    'all' => $statFunc($items),
                ];
            },
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider; // If validation fails, just return the unfiltered list
        }

        $query->andFilterWhere([
            'category_id' => $this->category_id,
            'enabled' => $this->enabled,
        ]);

        if (($read = strtolower($this->read)) && in_array($read, ['y', 'n'])) {
            $query->andWhere([$read == 'y' ? "IN" : "NOT IN", 'id', (new Query())
                ->select('notice_id')
                ->from('{{%notice_view}}')
                ->where(['member_id' => \Yii::$app->getUser()->getId()])
            ]);
        }

        $query->andFilterWhere(['LIKE', 'title', $this->title]);

        return $dataProvider;
    }

}