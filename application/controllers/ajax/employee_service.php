<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

class Employee_Service extends CI_Controller {
	public function __construct ( ) {
        parent::__construct();
    }

    public function getDetails() {
        if ($this->libauth->hasLoggedIn()) {
            //check if this user is a manager of a department
            $isManager = $this->session->userdata('isManager');
            //get current user's department
            $departNo = $this->session->userdata('deptNo');

            if ($isManager) {
                //this user is manager, he has permission to read
                //user's details under his department
                try {
                    $input = FormInput::getGetInput(
                        array(
                            array("field" => "id",
                                "label" => "Employee Number",
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
                //get the user's details. $departNo is used to check if this manager
                //has permission to view employees under another department
                $userId = intval($input['id']);
                $result = $this->Employee->getDetails($userId, $departNo);

            } else {
                //this user does not have permission to read others' details,
                //always return his details.
                $userId = $this->libauth->getUserId();
                $result = $this->Employee->getDetails($userId, $departNo);
            }
            if ($result) {
                $this->json->returnJSON(array(
                    'status' => 'success',
                    'data' => $result
                ));
                return true;
            } else {
                $this->json->returnJSON(array(
                    'status' => 'failed',
                    'reason' => 'No data available'
                ));
                return false;
            }
        } else {
            var_dump('NOT LOGGED IN.!!');
        }
    }

    public function getSubordinate() {
        session_start();
        if ($this->libauth->hasLoggedIn()) {
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
            $result = $this->Employee->getSubordinate($userId, $startPage, $size, $column, $keyword);

            $this->json->returnJSON($result);
        } else {
            var_dump('NOT LOGGED IN.!!');
        }
    }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
