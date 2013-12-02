<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once (APPPATH . 'models/dao/EmployeeDAO.php');

/**
 * This test file covers test cases for 
 *
 *  -- models/employee.php (Class Employee)
 *  -- models/dao/EmployeeDAO.php (Class EmployeeDAO)
 */
class EmployeeTest extends CI_Controller {
    public function __construct ( ) {
        parent::__construct();
        $this->load->library('unit_test');
        $this->unit->use_strict(TRUE);
    }

    private function setup() {
        /* in order to make this test available to all cases,
         * we need to manually insert a couple of data without committing
         */
        $this->db->trans_begin();

        //create a fake department
        $sql = "INSERT INTO departments(dept_no, dept_name) values('d999', '_Testing')";
        $this->db->query($sql);

        //create some new employees
        $sql = "INSERT INTO employees(emp_no, birth_date, first_name, last_name, gender, hire_date) values(999999999, '1953-01-01', 'Test1A', 'Test1B', 'F', '1986-06-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO employees(emp_no, birth_date, first_name, last_name, gender, hire_date) values(999999998, '1952-01-01', 'Test2A', 'Test2B', 'M', '1976-06-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO employees(emp_no, birth_date, first_name, last_name, gender, hire_date) values(999999997, '1951-01-01', 'Test3A', 'Tes3B', 'M', '1981-05-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO employees(emp_no, birth_date, first_name, last_name, gender, hire_date) values(999999996, '1950-01-01', 'Test4A', 'Test4B', 'M', '1979-12-23')";
        $this->db->query($sql);

        $sql = "INSERT INTO employees(emp_no, birth_date, first_name, last_name, gender, hire_date) values(999999995, '1949-01-01', 'Test5A', 'Test5B', 'M', '1986-06-05')";
        $this->db->query($sql);

        $sql = "INSERT INTO employees(emp_no, birth_date, first_name, last_name, gender, hire_date) values(999999994, '1979-01-01', 'Test6A', 'Test6B', 'F', '1988-01-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO employees(emp_no, birth_date, first_name, last_name, gender, hire_date) values(999999993, '1983-01-01', 'Test7A', 'Test7B', 'F', '1990-08-22')";
        $this->db->query($sql);

        //add employee to department for database for these users
        $sql = "INSERT INTO dept_emp(emp_no, dept_no, from_date, to_date) VALUES(999999999, 'd999', '1986-06-01', '9999-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO dept_emp(emp_no, dept_no, from_date, to_date) VALUES(999999998, 'd999', '1976-06-01', '9999-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO dept_emp(emp_no, dept_no, from_date, to_date) VALUES(999999997, 'd999', '1981-05-01', '9999-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO dept_emp(emp_no, dept_no, from_date, to_date) VALUES(999999996, 'd999', '1979-12-23', '2008-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO dept_emp(emp_no, dept_no, from_date, to_date) VALUES(999999995, 'd999', '1986-06-05', '9999-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO dept_emp(emp_no, dept_no, from_date, to_date) VALUES(999999994, 'd999', '1988-01-01', '9999-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO dept_emp(emp_no, dept_no, from_date, to_date) VALUES(999999993, 'd999', '1990-08-22', '9999-01-01')";
        $this->db->query($sql);

        //add titles to database for these users
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999999, 'Tester', '1986-06-01', '1989-07-05')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999999, 'Junior Tester', '1989-07-05', '1990-12-21')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999999, 'Senior Tester', '1990-12-21', '9999-01-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999998, 'Pilot', '1976-06-01', '1981-07-05')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999998, 'Junior Pilot', '1981-07-05', '1995-12-21')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999998, 'Senior Pilot', '1995-12-21', '9999-01-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999997, 'RocketLauncher', '1981-05-01', '1995-07-05')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999997, 'Junior RocketLauncher', '1995-07-05', '9999-01-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999996, 'Doctor', '1979-12-23', '1988-07-05')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999996, 'Junior Doctor', '1988-07-05', '2008-01-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999995, 'Musician', '1986-06-05', '9999-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999994, 'Speaker', '1988-01-01', '9999-01-01')";
        $this->db->query($sql);
        $sql = "INSERT INTO titles(emp_no, title, from_date, to_date) VALUES(999999993, 'Jedi', '1990-08-22', '9999-01-01')";
        $this->db->query($sql);

        //make Test3A.Test3B and Test7A.Test7B as department manager
        $sql = "INSERT INTO dept_manager(emp_no,dept_no,from_date,to_date) VALUES(999999997, 'd999', '1982-05-01', '9999-01-01')";
        $this->db->query($sql);

        $sql = "INSERT INTO dept_manager(emp_no,dept_no,from_date,to_date) VALUES(999999993, 'd999', '1972-03-28', '9999-01-01')";
        $this->db->query($sql);

        //and a retired manager Test4A.Test4B
        $sql = "INSERT INTO dept_manager(emp_no,dept_no,from_date,to_date) VALUES(999999996, 'd999', '1979-12-23', '2008-01-01')";
        $this->db->query($sql);

    }

    private function teardown() {
        $this->db->trans_rollback();
    }

    private function negativeGetSubordinateTests() {
        //Test 1: Test if manager can get all subordinate correctly
        $result = EmployeeDAO::getSubordinate($this->db, 999999999, 0, 20, null, null);

        $this->unit->run($result['hit'], 0, 'getSubordinateTest: general test', $result['hit']);
        $this->unit->run($result['total'], '0', 'getSubordinateTest: general test', $result['total']);
        $this->unit->run($result['start'], 0, 'getSubordinateTest: general test', $result['start']);
        $this->unit->run(count($result['data']), 0, 'getSubordinateTest: general test', count($result['data']));
    }

    private function positiveGetSubordinateTests() {
        //Test 1: Test if manager can get all subordinate correctly
        $result = EmployeeDAO::getSubordinate($this->db, 999999997, 0, 20, null, null);
        
        $this->unit->run($result['hit'], 7, 'getSubordinateTest: general test', $result['hit']);
        $this->unit->run($result['total'], '7', 'getSubordinateTest: general test', $result['total']);
        $this->unit->run($result['start'], 0, 'getSubordinateTest: general test', $result['start']);

        $data = $result['data'][0];
        $this->unit->run($data->emp_no, '999999999', 'getSubordinateTest: emp_no test', $data->emp_no);
        $this->unit->run($data->first_name, 'Test1A', 'getSubordinateTest: first_name test', $data->first_name);
        $this->unit->run($data->last_name, 'Test1B', 'getSubordinateTest: last_name test', $data->last_name);
        $this->unit->run($data->title, 'Senior Tester', 'getSubordinateTest: title test', $data->title);
        $this->unit->run($data->gender, 'F', 'getSubordinateTest:  gender test', $data->gender);
        $this->unit->run($data->from_date, '1990-12-21', 'getSubordinateTest:  from_date test', $data->from_date);
        $this->unit->run($data->to_date, '9999-01-01', 'getSubordinateTest:  to_date test', $data->to_date);
        $this->unit->run($data->hire_date, '1986-06-01', 'getSubordinateTest:  hire_date test', $data->hire_date);

        $data = $result['data'][1];
        $this->unit->run($data->emp_no, '999999998', 'getSubordinateTest: emp_no test', $data->emp_no);
        $this->unit->run($data->first_name, 'Test2A', 'getSubordinateTest: first_name test', $data->first_name);
        $this->unit->run($data->last_name, 'Test2B', 'getSubordinateTest: last_name test', $data->last_name);
        $this->unit->run($data->title, 'Senior Pilot', 'getSubordinateTest: title test', $data->title);
        $this->unit->run($data->gender, 'M', 'getSubordinateTest:  gender test', $data->gender);
        $this->unit->run($data->from_date, '1995-12-21', 'getSubordinateTest:  from_date test', $data->from_date);
        $this->unit->run($data->to_date, '9999-01-01', 'getSubordinateTest:  to_date test', $data->to_date);
        $this->unit->run($data->hire_date, '1976-06-01', 'getSubordinateTest:  hire_date test', $data->hire_date);

        $data = $result['data'][3];
        $this->unit->run($data->emp_no, '999999996', 'getSubordinateTest: emp_no test', $data->emp_no);
        $this->unit->run($data->first_name, 'Test4A', 'getSubordinateTest: first_name test', $data->first_name);
        $this->unit->run($data->last_name, 'Test4B', 'getSubordinateTest: last_name test', $data->last_name);
        $this->unit->run($data->title, 'Junior Doctor', 'getSubordinateTest: title test', $data->title);
        $this->unit->run($data->gender, 'M', 'getSubordinateTest:  gender test', $data->gender);
        $this->unit->run($data->from_date, '1988-07-05', 'getSubordinateTest:  from_date test', $data->from_date);
        $this->unit->run($data->to_date, '2008-01-01', 'getSubordinateTest:  to_date test', $data->to_date);
        $this->unit->run($data->hire_date, '1979-12-23', 'getSubordinateTest:  hire_date test', $data->hire_date);
    }

    private function setPositiveTests() {
        $this->positiveGetSubordinateTests();
    }

    private function setNegativeTests() {
        $this->negativeGetSubordinateTests();
    }

    /**
     * This test file tests the auth controller class
     */
    public function index()
    {
        $this->setup();

        /* ALL TEST CASES HERE */
        $this->setPositiveTests();
        $this->setNegativeTests();
        /* TEST CASES ENDS HERE */

        $this->teardown();
        echo $this->unit->report();
    }
}