<?php
defined('BASEPATH') OR exit('No direct script access allowed');


	function json_output($status_header,$response)
	{
		$ci =& get_instance();
		$ci->output->set_content_type('application/json');
		$ci->output->set_status_header($status_header);
		$ci->output->set_output(json_encode($response));
	}

