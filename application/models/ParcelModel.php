<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ParcelModel extends CI_Model {

	const QUOTE_PER_WEIGHT = 5;
	const QUOTE_PER_VOLUME = 1000;
	const QUOTE_PER_VALUE = 0.3;

	const CALC_BY_WEIGHT_INDEX = 1;
	const CALC_BY_VOLUME_INDEX = 2;
	const CALC_BY_VALUE_INDEX = 3;
	const arr_pricing_models = array(
		self::CALC_BY_WEIGHT_INDEX =>"BY_WEIGHT",
		self::CALC_BY_VOLUME_INDEX =>"BY_VOLUME",
		self::CALC_BY_VALUE_INDEX =>"BY_VALUE"
	);

	public int $parcel_customer_id;
	public string $parcel_name;
	public float $parcel_weight;
	public float $parcel_volume;
	public float $parcel_declared_value;
	public string $parcel_chosen_model;
	public float $parcel_quote;

	public function create_parcel(array $new_parcel_data, int $customer_id) : array
	{
		$this->parcel_customer_id = $customer_id;
		$this->parcel_name = $new_parcel_data['parcel_name'];
		$this->parcel_weight = $new_parcel_data['parcel_weight'];
		$this->parcel_volume = $new_parcel_data['parcel_volume'];
		$this->parcel_declared_value = $new_parcel_data['parcel_declared_value'];

		$calculated_pricing_model = $this->get_optimal_pricing_model($new_parcel_data);

		$this->parcel_chosen_model = $calculated_pricing_model['chosen_model'];
		$this->parcel_quote  = $calculated_pricing_model['parcel_quote'];

		$res =  $this->db->insert('parcels', $this);
		if(!$res){
			return array('status' => 500,'message' => 'Internal server error.');
		}

		return array('status' => 201,'message' => 'Parcel data has been saved.');
	}

	public function get_parcel( int $customer_id, int $parcel_id = null) : array{
		if(empty($customer_id)) return array();

		$this->db->select('*')
			->from('parcels')
			->where('parcel_customer_id',$customer_id);

		if($parcel_id) $this->db->where('parcel_id',$parcel_id);

		return $this->db->order_by('parcel_id','desc')->get()->result();
	}

	public function update_parcel(int $customer_id ,int $parcel_id, array $new_parcel_data): array
	{
		$calculated_pricing_model = $this->get_optimal_pricing_model($new_parcel_data);

		$this->parcel_name = $new_parcel_data['parcel_name'];
		$this->parcel_weight = $new_parcel_data['parcel_weight'];
		$this->parcel_volume = $new_parcel_data['parcel_volume'];
		$this->parcel_declared_value = $new_parcel_data['parcel_declared_value'];
		$this->parcel_chosen_model = $calculated_pricing_model['chosen_model'];
		$this->parcel_quote  = $calculated_pricing_model['parcel_quote'];

		$res = $this->db->where('parcel_customer_id',$customer_id)
			->where('parcel_id',$parcel_id)
			->update('parcels', $this);
		if(!$res){
			return array('status' => 500,'message' => 'Internal server error.');
		}
		return array('status' => 201,'message' => 'Parcel data has been updated.');
	}

	public function delete_parcel(int $parcel_id, int $customer_id) : array
	{
			$this->db->where('parcel_customer_id',$customer_id)
				->where('parcel_id',$parcel_id)
				->delete('parcels');

			return array('status' => 200,'message' => 'Parcel data has been deleted.');
	}

	private function get_optimal_pricing_model( array $parcel_data) : array{

		$arr_quotes[self::CALC_BY_WEIGHT_INDEX] =  self::QUOTE_PER_WEIGHT * $parcel_data['parcel_weight'];
		$arr_quotes[self::CALC_BY_VOLUME_INDEX] =  self::QUOTE_PER_VOLUME * $parcel_data['parcel_volume'];
		$arr_quotes[self::CALC_BY_VALUE_INDEX] =  self::QUOTE_PER_VALUE * $parcel_data['parcel_declared_value'];

		$optimal_pricing_model_index = array_keys($arr_quotes, max($arr_quotes));

		return array(
			'parcel_quote' => $arr_quotes[$optimal_pricing_model_index[0]],
			'chosen_model' => self::arr_pricing_models[$optimal_pricing_model_index[0]]
		);
	}

}
