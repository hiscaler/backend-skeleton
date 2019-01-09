<?php

namespace app\modules\api\models;

use Yii;
use yii\data\ActiveDataProvider;

class LabelSearch extends Label
{

    public function rules()
    {
        return [
            [['name', 'alias'], 'string'],
            [['enabled'], 'boolean'],
        ];
    }

    /**
     * @param $params
     * @return ActiveDataProvider
     * @throws \Throwable
     */
    public function search($params)
    {
        $query = Label::find();
        $where = [];
        if ($identity = Yii::$app->getUser()->getIdentity()) {
            /* @var $identity Member */
            if ($identity->type !== Member::TYPE_ADMINISTRATOR) {
                $where = '1 = 0'; // 非管理员不能查看会员信息
            }
        }
        $where && $query->where($where);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['id' => SORT_DESC],
            ]
        ]);

        $this->load($params, '');
        if (!$this->validate()) {
            return $dataProvider; // If validation fails, just return the unfiltered list
        }

        $query->andFilterHaving([
            'name' => $this->name,
            'alias' => $this->alias,
            'enabled' => $this->enabled,
        ]);

        return $dataProvider;
    }

}