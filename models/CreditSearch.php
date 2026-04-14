<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Credit;

/**
 * CreditSearch represents the model behind the search form of `app\models\Credit`.
 */
class CreditSearch extends Credit
{
    /**
     * {@inheritdoc}
     */
	public $date_start;
    public $date_end;
	public $date_start1;
    public $date_end1;
    public function rules()
    {
        return [
            [['id', 'id_client', 'month', 'id_user', 'id_store'], 'integer'],
            [['product_name', 'date_constribution', 'date_create','date_start','date_end','date_start1','date_end1'], 'safe'],
            [['sum', 'fee', 'month_payment', 'debt'], 'number'],
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
        $query = Credit::find();

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
            'id_client' => $this->id_client,
			'id_store' => $this->id_store,
            'sum' => $this->sum,
            'fee' => $this->fee,
            'month' => $this->month,
            'month_payment' => $this->month_payment,
            'id_user' => $this->id_user,
            'debt' => $this->debt,
        ]);
		$query->andFilterWhere(['between','date_create',$this->date_start,$this->date_end]);
		$query->andFilterWhere(['between','date_constribution',$this->date_start1,$this->date_end1]);
        $query->andFilterWhere(['like', 'product_name', $this->product_name]);

        return $dataProvider;
    }
}
