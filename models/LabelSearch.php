<?php

namespace app\models;

use app\models\LabelSearch;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LabelSearchSearch represents the model behind the search form about `app\models\LabelSearch`.
 */
class LabelSearch extends Label
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enabled'], 'boolean'],
            [['alias', 'name'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = LabelSearch::find()->with(['creater', 'updater'])->asArray(true);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'alias' => SORT_ASC,
                ]
            ]
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'enabled' => $this->enabled,
        ]);

        $query->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'name', $this->name]);

        return $dataProvider;
    }

}
