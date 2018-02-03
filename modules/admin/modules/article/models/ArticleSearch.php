<?php

namespace app\modules\admin\modules\article\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ArticleSearch represents the model behind the search form of `app\modules\admin\modules\article\models\Article`.
 */
class ArticleSearch extends Article
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['alias', 'title'], 'safe'],
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
        $query = Article::find()->with(['creater', 'updater'])->asArray();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'alias' => SORT_ASC,
                ]
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'alias', $this->alias])
            ->andFilterWhere(['like', 'title', $this->title]);

        return $dataProvider;
    }
}
