<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Month;

/**
 * MonthSearch represents the model behind the search form of `app\models\Month`.
 */
class MonthSearch extends Month
{
    /**
     * {@inheritdoc}
     */
	public $date_start;
    public $date_end;
    public function rules()
    {
        return [
            [['id', 'id_credit'], 'integer'],
            [['sum'], 'number'],
            [['note', 'date','date_start','date_end'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Month::find();

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
		if ( $this->date_start)  $this->date_start=$this->date_start." 00:00:00";
        if ($this->date_end)  $this->date_end=$this->date_end." 23:59:59";
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'id_credit' => $this->id_credit,
            'sum' => $this->sum,
        ]);

        $query->andFilterWhere(['like', 'note', $this->note]);
		$query->andFilterWhere(['between','date',$this->date_start,$this->date_end]);
        return $dataProvider;
    }
}
