<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Logout extends CI_Controller {
	/**
	 *  Index Page for Logout controller.
     *  Logout controller clears the user session data,
     *  and redirect user to login page.
     */
    public function index() {
        $this->libauth->logout();
        header('Location: /login');
    }
}

/* End of file logout.php */
/* Location: ./application/controllers/logout.php */