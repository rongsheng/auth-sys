<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

class Login extends CI_Controller {
    public function __construct ( ) {
        parent::__construct();
        $this->load->library('masterpage');
    }

    public function index() {
        session_start();
        $this->masterpage->setMasterPage('master');
        $this->masterpage->addContentPage('login', 'content');
        $this->masterpage->show(array(
            'load_js' => '/static-assets/js/login',
            'static_less' => '/static-assets/less/login.less'
        ));
    }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
