<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthModel extends CI_Model {

	private static $auth_key = "tiny_parcel_auth";

	public function check_auth_client() : bool {
		$auth_key  = $this->input->get_request_header('Auth-Key', true);
		return $auth_key === self::$auth_key;
	}

	public function do_login (string $username, string $password) : array
	{
		$query_customer_info = $this->db->select('password, customer_id')
			->from('customers')
			->where('username',$username)
			->get()->row();

		if(empty($query_customer_info)){
			return array('status' => 204,'message' => 'Can not find any customer under this username.');
		}

		$hashed_password = $query_customer_info->password;
		$customer_id  = $query_customer_info->customer_id;

		if($hashed_password !== md5($password)){
			return array('status' => 204,'message' => 'Wrong password.');
		}

		$last_login = date('Y-m-d H:i:s');
		$token = bin2hex(random_bytes(16));
		$expired_at = date("Y-m-d H:i:s", strtotime('+12 hours'));

		$this->db->trans_start();

		$this->db->where('customer_id',$customer_id)->update('customers',['last_login' => $last_login]);

		$this->db->where('customer_id',$customer_id)->delete('authentication_tokens');

		$this->db->insert('authentication_tokens',['customer_id' => $customer_id,'token' => $token,'expired_at' => $expired_at]);

		if ($this->db->trans_status() === false){
			$this->db->trans_rollback();
			return array('status' => 500,'message' => 'Internal server error.');
		}

		$this->db->trans_commit();
		return array('status' => 200,'message' => 'Successfully logged in.','id' => $customer_id, 'token' => $token);
	}

	public function do_logout()
	{
		$users_id  = $this->input->get_request_header('User-ID', true);
		$token = $this->input->get_request_header('Authorization', true);
		$this->db->where('customer_id',$users_id)->where('token',$token)->delete('authentication_tokens');
		return array('status' => 200,'message' => 'Successfully logout.');
	}

	public function authenticate() : array
	{
		$customer_id  = $this->input->get_request_header('User-ID', true);
		$token    = $this->input->get_request_header('Authorization', true);

		$auth_info  = $this->db->select('expired_at')
			->from('authentication_tokens')
			->where('customer_id',$customer_id)
			->where('token',$token)
			->get()->row();

		if(empty($auth_info)){
			return array('status' => 401,'message' => 'Unauthorized.');
		}

		if($auth_info->expired_at < date('Y-m-d H:i:s')){
			return array('status' => 401,'message' => 'Your session has expired.');
		}

		//re-new the token expiry
		$res = $this->db->where('customer_id',$customer_id)
			->where('token',$token)
			->update('authentication_tokens',['expired_at' => date("Y-m-d H:i:s", strtotime('+12 hours'))]);

		if(!$res){
			return array('status' => 500,'message' => 'Internal server error.');
		}

		return array('status' => 200,'message' => 'Authorized.');
	}

}
