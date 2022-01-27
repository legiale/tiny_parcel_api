<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthenticationController extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('AuthModel','authenticator');
	}

	public function index(){
		echo "Tiny Parcel services. Please make your requests! <br/>";
		print_r("<pre>") ;
		print_r("* Create, Read, Update and Delete for parcel(s): 

GET /parcels/{id}

POST /parcels/

PUT /parcels/{id}

DELETE /parcels/{id}

* GET delivery price of (one or up to twenty) parcels -  request a single bulk price for all of them.

GET /prices");
		print_r("</pre>");;
		die();
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
