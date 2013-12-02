<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once (APPPATH . 'libraries/FormInput/FormInput.php');

class Main extends CI_Controller {

    public function __construct ( ) {
        parent::__construct();
    }

    /**
     * Index Page for this controller.
     *
     * Maps to the following URL
     *         http://example.com/index.php/welcome
     *    - or -  
     *         http://example.com/index.php/welcome/index
     *    - or -
     * Since this controller is set as the default controller in 
     * config/routes.php, it's displayed at http://example.com/
     *
     * So any other public methods not prefixed with an underscore will
     * map to /index.php/welcome/<method_name>
     * @see http://codeigniter.com/user_guide/general/urls.html
     */
    public function index() {
        if (!$this->libauth->hasLoggedIn()) {
            header('Location: /login');
        }

        $data = array(
            'isManager' => $this->libauth->isManager(),
            'firstName' => $this->libauth->getFirstName(),
            'lastName' => $this->libauth->getLastName(),
            'deptName' => $this->libauth->getDepartment(),
            'userId' => $this->libauth->getUserId()
        );

        $this->masterpage->setMasterPage('master');
        $load_view = $load_js = 'employee';
        if ($this->libauth->isManager()) {
            $load_view = $load_js = 'manager';
        }
        $this->masterpage->addContentPage($load_view, 'content', $data);
        $this->masterpage->show(array(
            'load_js' => '/static-assets/js/' . $load_js,
            'static_less' => '/static-assets/less/main.less'
        ));
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */