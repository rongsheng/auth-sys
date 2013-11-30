<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class JSON {

	public function __construct() {
    	$this->CI =& get_instance();
	}

    public function returnJSON($array) {
    	$data['json'] = json_encode($array);
        $this->CI->load->view('json_view', $data);
        return true;
    }
}