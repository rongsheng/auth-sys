<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');
require_once (APPPATH . 'controllers/ajax/auth.php');

class AuthControllerTest extends Auth {
    public function __construct ( ) {
        parent::__construct();
        $this->load->library('unit_test');
        $this->unit->use_strict(TRUE);
    }

    private function setPositiveTests() {
        //Test 1: General test using Kyoichi Maliniak
        //NOTE: we cannot check the view in CI, so we are only checking the return type
        $_POST['u'] = 'Kyoichi.Maliniak';
        $_POST['p'] = '10005';
        $this->unit->run($this->login(), true, 'AuthController: positive login test');

        
    }

    private function setNegativeTests() {
        $_POST['u'] = 'kyoichi.maliniak';
        $_POST['p'] = '10005';
        $this->unit->run($this->login(), false, 'AuthController: negative login test');

        $_POST['u'] = 'Kyoichi.maliniak';
        $_POST['p'] = '10005';
        $this->unit->run($this->login(), false, 'AuthController: negative login test');

        $_POST['u'] = '';
        $_POST['p'] = '10005';
        $this->unit->run($this->login(), false, 'AuthController: negative login test');

        $_POST['u'] = 'Kyoichi.Maliniak';
        $_POST['p'] = '';
        $this->unit->run($this->login(), false, 'AuthController: negative login test');
    }

    /**
     * This test file tests the auth controller class
     */
    public function index()
    {
        $this->setPositiveTests();
        $this->setNegativeTests();

        //reset the header as JSON view has polluted
        $this->output->set_header('Content-Type: text/html; charset=utf-8');
        echo $this->unit->report();
    }
}