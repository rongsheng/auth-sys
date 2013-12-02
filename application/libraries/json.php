<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * JSON controller that helps to output a json object to view
 */
class JSON {

	public function __construct() {
    	$this->CI =& get_instance();
	}

	/**
	 * return a json object
	 * @param  array $array the array data
	 * @return boolean
	 */
    public function returnJSON($array) {
    	$data['json'] = json_encode($array);
        $this->CI->load->view('json_view', $data);
        return true;
    }
}