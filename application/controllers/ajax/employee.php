<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

class Employee extends CI_Controller {
	public function __construct ( ) {
        parent::__construct();
    }

    public function getSubordinate() {
        if ($this->libauth->hasLoggedIn()) {
            session_start();

            try {
                $input = FormInput::getGetInput(
                    array(
                        array("field" => "p",
                            "label" => "Start Page",
                            "rules" => "trim|is_numeric"),
                        array("field" => "s",
                            "label" => "Size",
                            "rules" => "trim|is_numeric")
                    )
                );
            } catch (ValidationException $e) {
                $this->json->returnJSON(array(
                    'status' => 'failed',
                    'reason' => 'Invalid parameters.'
                ));
                return false;
            }

            //initialize parameters, if no input from user, set to default
            $userId = $this->libauth->getUserId();
            $startPage = isset($input['p']) ? (int)$input['p'] : $this->config->item('default_page_start');
            $size = isset($input['s']) ? (int)$input['s'] : $this->config->item('default_fetch_size');
            $column = isset($input['c']) ? $input['c'] : null;
            $keyword = isset($input['k']) ? $input['k'] : null;
            $result = $this->Employee_model->getSubordinate($userId, $startPage, $size, $column, $keyword);

            $this->json->returnJSON($result);
        } else {
            var_dump('NOT LOGGED IN.!!');
        }
    }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
