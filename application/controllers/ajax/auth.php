<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

class Auth extends CI_Controller {
    public function __construct ( ) {
        parent::__construct();
        $this->load->library('masterpage');
    }

    public function login() {
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
        //if not, return false directly
        if (strpos($input['u'], '.') === false) {
            $this->json->returnJSON(array(
                'status' => 'failed',
                'reason' => 'Invalid username or password'
            ));
            return false;
        } else {
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