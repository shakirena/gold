<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Credit;

/**
 * CreditSearch represents the model behind the search form of `app\models\Credit`.
 */
class CreditPaymentSearch extends Credit
{
    /**
     * {@inheritdoc}
     */
	public $date_start;
    public $date_end;
    public function rules()
    {
        return [
            [['id', 'id_client', 'month'], 'integer'],
            [['product_name', 'date_constribution', 'date_create'], 'safe'],
            [['sum', 'fee', 'month_payment', 'debt','date_start','date_end'], 'number'],
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
        $query = Credit::find()->andWhere("debt>0")->orderBy("date_constribution ASC");

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
		$query->joinWith(['client']);
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
            'sum' => $this->sum,
            'fee' => $this->fee,
            'month' => $this->month,
            'month_payment' => $this->month_payment,
            'date_constribution' => $this->date_constribution,
         
            'debt' => $this->debt,
        ]);

        $query->andFilterWhere(['like', 'product_name', $this->product_name]);
  $query->andFilterWhere(['between','date_create',$this->date_start,$this->date_end]);
        return $dataProvider;
    }
}
