<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Main extends CI_Controller {
    /**
     * Render Index Page for main controller. This display the 
     * view that shows the employee / manager information.
     */
    public function index() {
        if (!$this->libauth->hasLoggedIn()) {
            header('Location: /login');
        }

        //prepare data array that loads into view
        $data = array(
            'isManager' => $this->libauth->isManager(),
            'firstName' => $this->libauth->getFirstName(),
            'lastName' => $this->libauth->getLastName(),
            'deptName' => $this->libauth->getDepartment(),
            'userId' => $this->libauth->getUserId()
        );

        $this->masterpage->setMasterPage('master');
        //by default, load the employee view
        $load_view = $load_js = 'employee';
        //if the user is a manager, load the manager view
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

/* End of file main.php */
/* Location: ./application/controllers/main.php */