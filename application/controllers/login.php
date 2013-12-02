<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

    /**
     *  Index Page for Login controller.
     *  Login controller display the login (box) view
     *  if users have not logged in or redirect them 
     *  to the application directly.
     */
    public function index() {
        //check if user has logged in, if true, redirect them to main
        if ($this->libauth->hasLoggedIn()) {
            header('Location: /main');
        }

        //output login view
        $this->masterpage->setMasterPage('master');
        $this->masterpage->addContentPage('login', 'content');
        $this->masterpage->show(array(
            'static_js' => '/static-assets/js/login',
            'build_js' => '/build-assets/js/login',
            'less' => '/static-assets/less/login.less',
            'css' => '/build-assets/css/login.css',
            'ENVIRONMENT' => ENVIRONMENT
        ));
    }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */