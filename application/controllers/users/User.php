<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller {

    /**
     * User Controller.
     *
     * Provides functionality related to user.
     *
     */
    public function index()
    {
        $this->load->view('welcome_message');
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */