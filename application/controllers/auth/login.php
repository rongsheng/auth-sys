<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (BASEPATH . '/libraries/FormInput/FormInput.php');

class Login extends CI_Controller {
    public function index() {
        $this->load->view('login');
    }

    public function submit() {
        $input = FormInput::getInput();
        return true;
    }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */