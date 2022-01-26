<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ParcelController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('AuthModel','authenticator');
		$this->load->model('ParcelModel','parcel');
	}

	public function create()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method !== 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$checked_request_res = $this->checkSecuredRequest();
		if($checked_request_res['status'] !== 200){
			json_output( $checked_request_res['status'], $checked_request_res);
			return;
		}

		$customer = $this->input->get_request_header('User-ID', true);

		$params = $this->input->post();
		$parcel_data_validation = $this->validateParcelData($params);
		if($parcel_data_validation['status'] !== 200){
			json_output($parcel_data_validation['status'],$parcel_data_validation);
			return;
		}

		$resp = $this->parcel->create_parcel($params,intval($customer));
		json_output($resp['status'],$resp);
	}

	public function detail($parcel_id)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method !== 'GET' || empty($this->uri->segment(2)) || !is_numeric($this->uri->segment(2))){
			json_output(400, array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$checked_request_res = $this->checkSecuredRequest();
		if($checked_request_res['status'] !== 200){
			json_output( $checked_request_res['status'], $checked_request_res);
			return;
		}

		$customer = $this->input->get_request_header('User-ID', true);

		$resp = $this->parcel->get_parcel($customer, $parcel_id);
		json_output(200,$resp);
	}

	public function update($parcel_id)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method !== 'PUT' || empty($this->uri->segment(2)) || !is_numeric($this->uri->segment(2))){
			json_output(400, array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$checked_request_res = $this->checkSecuredRequest();
		if($checked_request_res['status'] !== 200){
			json_output( $checked_request_res['status'], $checked_request_res);
			return;
		}

		$customer = $this->input->get_request_header('User-ID', true);

		$target_parcel = $this->parcel->get_parcel($customer, $parcel_id);
		if(empty($target_parcel)){
			json_output(400,array('status' => 400,'message' => 'The requested parcel can not be found under this customer.'));
			return;
		}

		//get data from PUT request
		$arr_updated_parcel_data = json_decode(file_get_contents('php://input'), true);
		$parcel_data_validation = $this->validateParcelData($arr_updated_parcel_data);
		if($parcel_data_validation['status'] !== 200){
			json_output($parcel_data_validation['status'],$parcel_data_validation);
			return;
		}

		$resp = $this->parcel->update_parcel($customer, $parcel_id, $arr_updated_parcel_data);
		json_output($resp['status'],$resp);
	}

	public function delete($parcel_id)
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method !== 'DELETE' || empty($this->uri->segment(2)) || !is_numeric($this->uri->segment(2))){
			json_output(400, array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$is_authorized_client = $this->authenticator->check_auth_client();
		if(!$is_authorized_client){
			json_output(403,array('status' => 403,'message' => 'Bad request.'));
			return;
		}

		$auth_res = $this->authenticator->authenticate();
		if($auth_res['status'] !== 200){
			json_output( $auth_res['status'], $auth_res);
			return;
		}

		$customer = $this->input->get_request_header('User-ID', true);
		if(!$customer || !is_numeric($customer)){
			json_output(400,array('status' => 400,'message' => 'Bad request'));
			return;
		}

		$response = $this->parcel->delete_parcel($parcel_id , $customer);
		json_output($response['status'],$response);
	}

	public function getTotalPrice(){
		$method = $_SERVER['REQUEST_METHOD'];
		if($method !== 'GET' || !isset($_GET['parcelIds'])){
			json_output(400, array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$checked_request_res = $this->checkSecuredRequest();
		if($checked_request_res['status'] !== 200){
			json_output( $checked_request_res['status'], $checked_request_res);
			return;
		}

		$list_parcel_ids= explode(',',$this->input->get('parcelIds'));
		$arr_invalid_ids = array_filter($list_parcel_ids,function ($parcel_id){
			return !is_numeric($parcel_id);
		});
		if(!empty($arr_invalid_ids)){
			json_output(400, array('status' => 400,'message' => 'Bad request. Invalid value of requested IDs'));
			return;
		}

		//only allow up to 20 parcels per request
		if(count($list_parcel_ids) > 20){
			$list_parcel_ids = array_slice($list_parcel_ids, 0, 20);
		}

		$customer = $this->input->get_request_header('User-ID', true);
		$result = array();
		$total_price = 0;
		foreach($list_parcel_ids as $parcel_id){
			$target_parcel = $this->parcel->get_parcel($customer, $parcel_id);
			if(!empty($target_parcel)){
				$target_parcel = reset($target_parcel);
				$result[]  = (array)$target_parcel;

				$total_price+= $target_parcel->parcel_quote;
			}

		}
		$arr_result = array(
			"total_price" => $total_price,
			"parcels_data" => $result,
		);
		json_output(200, array('status' => 200,'message' => 'Valid.', 'data'=>$arr_result));

	}

	// ================================================
	private function validateParcelData($parcel_data) : array {

		if (!isset($parcel_data['parcel_name']) || empty($parcel_data['parcel_name'])) {
			return array('status' => 400,'message' =>  'Parcel name must not be empty');
		}
		if (!isset($parcel_data['parcel_weight']) || is_null($parcel_data['parcel_weight']) || !is_numeric($parcel_data['parcel_weight'])) {
			return array('status' => 400,'message' =>  'Invalid declared parcel weight!');
		}
		if (!isset($parcel_data['parcel_volume']) || is_null($parcel_data['parcel_volume']) || !is_numeric($parcel_data['parcel_volume'])) {
			return array('status' => 400,'message' =>  'Invalid declared parcel volume!');
		}
		if (!isset($parcel_data['parcel_declared_value'])
			|| is_null($parcel_data['parcel_declared_value'])
			|| !is_numeric($parcel_data['parcel_declared_value'])) {
			return array('status' => 400,'message' =>  'Invalid declared parcel value!');
		}
		return array('status' => 200,'message' =>  'Valid parcel data.');
	}

	private function checkSecuredRequest() : array{
		$is_authorized_client = $this->authenticator->check_auth_client();
		if(!$is_authorized_client){
			return array('status' => 403,'message' => 'Bad request. Unauthorized.');
		}

		$auth_res = $this->authenticator->authenticate();
		if($auth_res['status'] !== 200){
			return $auth_res;
		}

		$customer = $this->input->get_request_header('User-ID', true);
		if(!$customer || !is_numeric($customer)){
			return array('status' => 400,'message' => 'Bad request.');
		}

		return array('status' => 200,'message' => 'Valid request');
	}
}
