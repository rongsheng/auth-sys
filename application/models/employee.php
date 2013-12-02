<?php
require_once(APPPATH . '/models/dao/EmployeeDAO.php');

/**
 * Employee Model class contains data related to employee
 * and provides basic functionality in retrieving employee data
 */
class Employee extends CI_Model {
    public function __construct() {
        parent::__construct();
    }

    /**
     * get a user by his/her employee number
     * @param  integer $id the employee number
     * @return array of data
     */
    public function getById($id) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException();
        }

        $result = EmployeeDAO::getById($this->db, $id);
        return $result->result();
    }

    /**
     * This function returns basic user info based on the 
     * first name and last name.
     * NOTE: with the use of BINARY operator, first namr and 
     *       last name are case sensitive
     * @param integer $id the employee number
     * @param string $firstName
     * @param string $lastName
     */
    function getUserAndType($id, $firstName, $lastName) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException();
        }
        
        $result = EmployeeDAO::getUserAndType($this->db, $id, $firstName, $lastName);
        return $result->result();
    }

    /**
     * get all subordinate staff for a particular manager,
     * filters available on all columns.
     * @param  integer $managerId
     * @param  integer $startPage
     * @param  integer $size
     * @param  string $column
     * @param  string $keyword
     * @return array of data
     */
    function getSubordinate($managerId, $startPage, $size, $column, $keyword) {
        if (!(is_numeric($managerId) && is_numeric($startPage) && is_numeric($size))) {
            throw new InvalidArgumentException('Invalid argument, parameters needs to be a number.');
        }
        $this->db->trans_start();
        $data = EmployeeDAO::getSubordinate($this->db, $managerId, $startPage, $size, $column, $keyword);
        $this->db->trans_complete();
        return $data;
    }

    /**
     * fetch the user details. user's details can only be seen by user in the same department
     * Note: auth checking is not written into this query, user's from the same department can
     *       read each other's details, including themselves.
     * @param int $userId the user id
     * @param int $deptNo the department this user belongs to
     */
    function getDetails($userId, $deptNo) {
        if (!is_numeric($userId)) {
            throw new InvalidArgumentException('Invalid argument, parameters needs to be a number.');
        }
       
        $query = EmployeeDAO::getDetails($this->db, $userId, $deptNo);
        if ($query->num_rows() > 0) {
           $row = $query->row();
           return $row;
        } else {
            return false;
        }
    }
}