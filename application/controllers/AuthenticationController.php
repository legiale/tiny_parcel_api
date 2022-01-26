<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthenticationController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('AuthModel','authenticator');
	}

	public function login()
	{
		$request_method = $_SERVER['REQUEST_METHOD'];

		if($request_method !== 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$is_authorized_client = $this->authenticator->check_auth_client();
		if(!$is_authorized_client){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$username = $this->input->post('username');
		$password = $this->input->post('password');

		$response = $this->authenticator->do_login($username, $password);
		json_output($response['status'],$response);
		return;
	}

	public function logout()
	{
		$method = $_SERVER['REQUEST_METHOD'];
		if($method !== 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
			return;
		}

		$is_authorized_client = $this->authenticator->check_auth_client();

		if($is_authorized_client){
			$response = $this->authenticator->do_logout();
			json_output($response['status'],$response);

		}
	}
	
}
