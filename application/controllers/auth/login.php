<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

class Login extends CI_Controller {
    public function index() {
        $this->load->view('login');
    }

    public function submit() {
        try {
            $input = FormInput::getGetInput(
                array(
                    array("field" => "u",
                        "label" => "username",
                        "rules" => "required|min_length[6]|max_length[20]"),
                    array("field" => "p",
                        "label" => "passowrd",
                        "rules" => "required|min_length[8]|max_length[15]")
                )
            );
            var_dump($input);
        } catch (ValidationException $e) {
            $data['json'] = json_encode(array(
                'status' => 'failed',
                'errorMsg' => 'User name'
            ));
            $this->load->view('json_view', $data);
        }
        return true;
    }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
