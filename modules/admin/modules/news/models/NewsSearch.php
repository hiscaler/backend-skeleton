<?php

namespace app\modules\admin\modules\news\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\admin\modules\news\models\News;

/**
 * NewsSearch represents the model behind the search form of `app\modules\admin\modules\news\models\News`.
 */
class NewsSearch extends News
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id', 'is_picture_news', 'enabled', 'enabled_comment', 'comments_count', 'published_at', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['title', 'short_title', 'keywords', 'description', 'author', 'source', 'source_url', 'picture_path'], 'safe'],
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
        $query = News::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'is_picture_news' => $this->is_picture_news,
            'enabled' => $this->enabled,
            'enabled_comment' => $this->enabled_comment,
            'comments_count' => $this->comments_count,
            'published_at' => $this->published_at,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'short_title', $this->short_title])
            ->andFilterWhere(['like', 'keywords', $this->keywords])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'author', $this->author])
            ->andFilterWhere(['like', 'source', $this->source])
            ->andFilterWhere(['like', 'source_url', $this->source_url])
            ->andFilterWhere(['like', 'picture_path', $this->picture_path]);

        return $dataProvider;
    }
}
