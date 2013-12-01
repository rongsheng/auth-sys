<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'libraries/FormInput/FormInput.php');

class LibAuthTest extends CI_Controller {
    public function __construct ( ) {
        parent::__construct();
        $this->load->library('unit_test');
        $this->unit->use_strict(TRUE);
    }

    private function setPositiveTests() {
        //Test 1: General test using Kyoichi Maliniak
        $firstName = 'Kyoichi';
        $lastName = 'Maliniak';
        $password = 10005;
        $result = $this->libauth->login($firstName, $lastName, $password);
        $this->unit->run($result, true, 'LibAuth: positive login test');

        $this->unit->run($this->session->userdata('loggedIn'), true, 'LibAuth: check loggedIn session');  
        $this->unit->run($this->session->userdata('firstName'), 'Kyoichi', 'LibAuth: check firstName session', $this->session->userdata('firstName'));
        $this->unit->run($this->session->userdata('lastName'), 'Maliniak', 'LibAuth: check lastName session', $this->session->userdata('lastName'));
        $this->unit->run($this->session->userdata('userId'), 10005, 'LibAuth: check userId session', $this->session->userdata('userId')); 
        $this->unit->run($this->session->userdata('isManager'), '0', 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->unit->run($this->session->userdata('deptNo'), 'd003', 'LibAuth: check deptNo session', $this->session->userdata('deptNo'));
        $this->unit->run($this->session->userdata('deptName'), 'Human Resources', 'LibAuth: check deptName session', $this->session->userdata('deptName'));

        $this->unit->run($this->libauth->hasLoggedIn(), true, 'LibAuth: check hasLoggedIn function');
        $this->unit->run($this->libauth->getUserId(), 10005, 'LibAuth: check getUserId function', $this->libauth->getUserId());
        $this->unit->run($this->libauth->getUserName(), 'Kyoichi Maliniak', 'LibAuth: check getUserName function', $this->libauth->getUserName());

        $this->session->sess_destroy();

        $this->unit->run($this->session->userdata('loggedIn'), false, 'LibAuth: check loggedIn session after loggout');
        $this->unit->run($this->session->userdata('firstName'), false, 'LibAuth: check firstName session after loggout');

        //Test 2: General Test using Tonny Butterworth
        $firstName = 'Tonny';
        $lastName = 'Butterworth';
        $password = 111692;
        $result = $this->libauth->login($firstName, $lastName, $password);
        $this->unit->run($result, true, 'LibAuth: positive login test');

        $this->unit->run($this->session->userdata('loggedIn'), true, 'LibAuth: check loggedIn session');  
        $this->unit->run($this->session->userdata('firstName'), 'Tonny', 'LibAuth: check firstName session', $this->session->userdata('firstName'));
        $this->unit->run($this->session->userdata('lastName'), 'Butterworth', 'LibAuth: check lastName session', $this->session->userdata('lastName'));
        $this->unit->run($this->session->userdata('userId'), 111692, 'LibAuth: check userId session', $this->session->userdata('userId')); 
        $this->unit->run($this->session->userdata('isManager'), '1', 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->unit->run($this->session->userdata('deptNo'), 'd009', 'LibAuth: check deptNo session', $this->session->userdata('deptNo'));
        $this->unit->run($this->session->userdata('deptName'), 'Customer Service', 'LibAuth: check deptName session', $this->session->userdata('deptName'));

        $this->unit->run($this->libauth->hasLoggedIn(), true, 'LibAuth: check hasLoggedIn function');
        $this->unit->run($this->libauth->getUserId(), 111692, 'LibAuth: check getUserId function', $this->libauth->getUserId());
        $this->unit->run($this->libauth->getUserName(), 'Tonny Butterworth', 'LibAuth: check getUserName function', $this->libauth->getUserName());

        $this->session->sess_destroy();

        $this->unit->run($this->session->userdata('loggedIn'), false, 'LibAuth: check loggedIn session after loggout');
        $this->unit->run($this->session->userdata('firstName'), false, 'LibAuth: check firstName session after loggout');

        //Test 3: Hardcore isManager Tests
        $result = $this->libauth->login('Isamu', 'Legleitner', 110114);
        $this->unit->run($result, true, 'LibAuth: positive login test');
        $this->unit->run($this->session->userdata('isManager'), '1', 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->session->sess_destroy();

        $result = $this->libauth->login('Leon', 'DasSarma', 110567);
        $this->unit->run($result, true, 'LibAuth: positive login test');
        $this->unit->run($this->session->userdata('isManager'), '1', 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->session->sess_destroy();

        $result = $this->libauth->login('Hilary', 'Kambil', 111534);
        $this->unit->run($result, true, 'LibAuth: positive login test');
        $this->unit->run($this->session->userdata('isManager'), '1', 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->session->sess_destroy();

        $result = $this->libauth->login('Marlo', 'Kandlur', 10998);
        $this->unit->run($result, true, 'LibAuth: positive login test');
        $this->unit->run($this->session->userdata('isManager'), '0', 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->session->sess_destroy();

        $result = $this->libauth->login('Maren', 'Veeraraghavan', 102245);
        $this->unit->run($result, true, 'LibAuth: positive login test');
        $this->unit->run($this->session->userdata('isManager'), '0', 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->session->sess_destroy();
    }

    private function setNegativeTests() {
        $this->session->sess_destroy();

        //Test 1: Invalid User Name
        $result = $this->libauth->login('ABC', 'DEFG', 110114);
        $this->unit->run($result, false, 'LibAuth: negative login test');

        $this->unit->run($this->session->userdata('loggedIn'), false, 'LibAuth: check loggedIn session');  
        $this->unit->run($this->session->userdata('firstName'), false, 'LibAuth: check firstName session', $this->session->userdata('firstName'));
        $this->unit->run($this->session->userdata('lastName'), false, 'LibAuth: check lastName session', $this->session->userdata('lastName'));
        $this->unit->run($this->session->userdata('userId'), false, 'LibAuth: check userId session', $this->session->userdata('userId')); 
        $this->unit->run($this->session->userdata('isManager'), false, 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->unit->run($this->session->userdata('deptNo'), false, 'LibAuth: check deptNo session', $this->session->userdata('deptNo'));
        $this->unit->run($this->session->userdata('deptName'), false, 'LibAuth: check deptName session', $this->session->userdata('deptName'));

        $this->unit->run($this->libauth->hasLoggedIn(), false, 'LibAuth: check hasLoggedIn function');
        $this->unit->run($this->libauth->getUserId(), false, 'LibAuth: check getUserId function', $this->libauth->getUserId());
        $this->unit->run($this->libauth->getUserName(), false, 'LibAuth: check getUserName function', $this->libauth->getUserName());

        $this->session->sess_destroy();

        //Test 2: Invalid Password
        $result = $this->libauth->login('Maren', 'Veeraraghavan', 102235);
        $this->unit->run($result, false, 'LibAuth: negative login test');

        $this->unit->run($this->session->userdata('loggedIn'), false, 'LibAuth: check loggedIn session');  
        $this->unit->run($this->session->userdata('firstName'), false, 'LibAuth: check firstName session', $this->session->userdata('firstName'));
        $this->unit->run($this->session->userdata('lastName'), false, 'LibAuth: check lastName session', $this->session->userdata('lastName'));
        $this->unit->run($this->session->userdata('userId'), false, 'LibAuth: check userId session', $this->session->userdata('userId')); 
        $this->unit->run($this->session->userdata('isManager'), false, 'LibAuth: check isManager session', $this->session->userdata('isManager'));
        $this->unit->run($this->session->userdata('deptNo'), false, 'LibAuth: check deptNo session', $this->session->userdata('deptNo'));
        $this->unit->run($this->session->userdata('deptName'), false, 'LibAuth: check deptName session', $this->session->userdata('deptName'));

        $this->unit->run($this->libauth->hasLoggedIn(), false, 'LibAuth: check hasLoggedIn function');
        $this->unit->run($this->libauth->getUserId(), false, 'LibAuth: check getUserId function', $this->libauth->getUserId());
        $this->unit->run($this->libauth->getUserName(), false, 'LibAuth: check getUserName function', $this->libauth->getUserName());

        $this->session->sess_destroy();

        //Test 3: Empty Input cases
        $result = $this->libauth->login('', 'Veeraraghavan', 102245);
        $this->unit->run($result, false, 'LibAuth: negative login test');
        $this->session->sess_destroy();

        $result = $this->libauth->login('Maren', '', 102245);
        $this->unit->run($result, false, 'LibAuth: negative login test');
        $this->session->sess_destroy();

        $result = $this->libauth->login('', '', 102245);
        $this->unit->run($result, false, 'LibAuth: negative login test');
        $this->session->sess_destroy();

        $result = $this->libauth->login(null, null, 102245);
        $this->unit->run($result, false, 'LibAuth: negative login test');
        $this->session->sess_destroy();

        //Test 4: Uppercase and Lowercase tests
        $result = $this->libauth->login('Maren', 'Veeraraghavan', 102245);
        $this->unit->run($result, true, 'LibAuth: negative login test');
        $this->session->sess_destroy();

        $result = $this->libauth->login('maren', 'veeraraghavan', 102245);
        $this->unit->run($result, false, 'LibAuth: negative login test');
        $this->session->sess_destroy();

        $result = $this->libauth->login('MAREN', 'VEERARAGHAVAN', 102245);
        $this->unit->run($result, false, 'LibAuth: negative login test');
        $this->session->sess_destroy();

        $result = $this->libauth->login('maRenN', 'VeEraRAghAvAN', 102245);
        $this->unit->run($result, false, 'LibAuth: negative login test');
        $this->session->sess_destroy();
    }

    /**
     * This test file tests the libauth class
     */
    public function index()
    {
        
        $this->setPositiveTests();
        $this->setNegativeTests();

        echo $this->unit->report();
    }
}