<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

/**
 *  Auth_Service provides an api to the auth system.
 *  This class atm only provides ajax endpoint to login.
 */
class Auth_Service extends CI_Controller {

    /**
     *  Login function that checks user's input and login for this user.
     */
    public function login() {
        //validate user's input
        try {
            $input = FormInput::getPostInput(
                array(
                    array("field" => "u",
                        "label" => "username",
                        "rules" => "required|min_length[6]|max_length[31]"),
                    array("field" => "p",
                        "label" => "passowrd",
                        "rules" => "required|min_length[5]")
                )
            );
        } catch (ValidationException $e) {
            $data['json'] = json_encode(array(
                'status' => 'failed',
                'reason' => 'Invalid username or password'
            ));
            $this->load->view('json_view', $data);
            return false;
        }

        //check that login username contains character '.'(dot),
        //if not, return false directly.
        if (strpos($input['u'], '.') === false) {
            $this->json->returnJSON(array(
                'status' => 'failed',
                'reason' => 'Invalid username or password'
            ));
            return false;
        } else {
            //user's input validated, log the user in.
            $userName = explode('.', $input['u']);
            $firstName = $userName[0];
            $lastName = $userName[1];
            $password = $input['p'];

            if ($this->libauth->login($firstName, $lastName, $password)) {
                $this->json->returnJSON(array(
                    'status' => 'success'
                ));
                return true;
            } else {
                $this->json->returnJSON(array(
                    'status' => 'failed',
                    'reason' => 'Invalid username or password'
                ));
                return false;
            }
        }
    }
}

/* End of file auth_service.php */
/* Location: ./application/controllers/ajax/auth_service.php */