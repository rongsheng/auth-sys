<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

/**
 *  Employee_Service provides an api to the fetch employees' info.
 */
class Employee_Service extends CI_Controller {

    /**
     *  Get the detail information for a particular employee.
     *  In order to view the details, the requester must be in
     *  the same department as the user.
     */
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
            //user has not logged in, redirect to login page.
            header('Location: /login');
        }
    }

    /**
     *  Get the information of all subordinate staff for a particular manager.
     *  In order to view the details, the requester must be a manager.
     */
    public function getSubordinate() {
        if ($this->libauth->hasLoggedIn()) {
            //check this user is not a manager, return error
            if (!$this->libauth->isManager()) {
                $this->json->returnJSON(array(
                    'status' => 'failed',
                    'reason' => "You do not have the permission to view employees' details."
                ));
                return false;
            }

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

            $userId = $this->libauth->getUserId();
            //initialize parameters, if no input from user, set to default
            $startPage = isset($input['p']) ? (int)$input['p'] : $this->config->item('default_page_start');
            $size = isset($input['s']) ? (int)$input['s'] : $this->config->item('default_fetch_size');
            $column = isset($input['c']) ? $input['c'] : null;
            $keyword = isset($input['k']) ? $input['k'] : null;
            $result = $this->Employee->getSubordinate($userId, $startPage, $size, $column, $keyword);

            $this->json->returnJSON(array(
                'status' => 'success',
                'data' => $result
            ));
        } else {
            //user has not logged in, redirect to login page.
            header('Location: /login');
        }
    }
}

/* End of file employee_service.php */
/* Location: ./application/controllers/ajax/employee_service.php */
