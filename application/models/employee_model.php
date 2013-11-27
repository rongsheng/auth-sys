<?php
class Employee_model extends CI_Model {

    $id = null;
    $birthDate = null;
    $firstName = null;
    $lastName = null;
    $gender = null;
    $hireDate = null;

    function __construct() {
        parent::__construct();
    }
    
    /**
     * validate the data vatality of this object
     */
    function validate() {
        if (empty($id) || empty($birthDate) || empty($firstName) ||
            empty($lastName) || empty($gender) || empty($hireDate)) {
            //throw exception
            throw new Exception('Invalid Employee model, insufficient data.');
        }
    }

    function insert($data) {
        $result = $this->db->insert('employees', $data);

        if($result){
            return true;
        } else {
            $msg = $this->db->_error_message();
            trigger_error($msg);
            return false;
        }
    }

    function update($data) {
        $this->db->update('employees', $this, array('emp_no' => $_POST['id']));
    }

    function getById($id) {
        $query = $this->db->get_where('employees', array('emp_no' => $id));
        return $query->result();
    }

    function get($start, $amount) {
        $query = $this->db->get('employees', $amount, $start);
        return $query->result();
    }
}