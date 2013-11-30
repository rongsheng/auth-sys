<?php
class Employee_model extends CI_Model {
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
        $sql = "SELECT e.emp_no, e.first_name, e.last_name, e.gender,
                       e.hire_date, 
                FROM employees 
                WHERE dm.emp_no = ?
                GROUP BY e.emp_no
                ORDER BY e.emp_no;";
        $result = $this->db->query($sql, array($managerId));

        $query = $this->db->get_where('employees', array('emp_no' => $id));
        return $query->result();
    }

    function getUser($id, $firstName, $lastName) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException();
        }

        $query = $this->db->get_where('employees', array(
            'emp_no' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName
        ));
        return $query->result();
    }

    function getUserAndType($id, $firstName, $lastName) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException();
        }
        $sql = "SELECT e.emp_no, e.first_name, e.last_name, 
                       e.gender, e.hire_date, 
                       IF(dm.dept_no IS NULL,false,true) as is_manager
                FROM employees AS e
                LEFT JOIN dept_manager as dm ON e.emp_no = dm.emp_no
                WHERE e.emp_no = ?;";
        $result = $this->db->query($sql, array($id));
        return $result->result();
    }

    function get($start, $amount) {
        $query = $this->db->get('employees', $amount, $start);
        return $query->result();
    }

    function getSubordinate($managerId, $startPage, $size, $column, $keyword) {
        if (!(is_numeric($managerId) && is_numeric($startPage) && is_numeric($size))) {
            throw new InvalidArgumentException('Invalid argument, parameters needs to be a number.');
        }
        $this->db->trans_start();
        $searchMode = null;
        if (!$column && $keyword) {
            $searchMode = 'full-text';
        } else if ($column && $keyword) {
            $searchMode = 'column';
        }
        $sql = "SELECT SQL_CALC_FOUND_ROWS e.emp_no, e.first_name, e.last_name, e.gender,
                       t.title, e.hire_date, d.dept_name, t.from_date, 
                       MAX(t.to_date) as to_date, d.dept_name
                FROM dept_manager as dm
                JOIN dept_emp as de ON dm.dept_no = de.dept_no
                JOIN departments as d ON dm.dept_no = d.dept_no
                JOIN employees as e ON de.emp_no = e.emp_no
                JOIN titles as t ON e.emp_no = t.emp_no 
                WHERE dm.emp_no = ? " .
                ($searchMode == 'column' ? "AND {$column} LIKE '%{$keyword}%' " : " ") .
                "GROUP BY e.emp_no
                ORDER BY e.first_name
                LIMIT ? , ?;";
        
        if ($searchMode == 'column') {
            $column = $this->db->escape_like_str($column);
            $keyword = $this->db->escape_like_str($keyword);
            $result = $this->db->query($sql, array($managerId, $startPage * $size, $size));
        } if ($searchMode == 'full-text') {
            $result = $this->db->query($sql, array($managerId, $startPage * $size, $size));
        } else {
            $result = $this->db->query($sql, array($managerId, $startPage * $size, $size));
        }
        
        $calc = "SELECT FOUND_ROWS() AS total;";
        $count = $this->db->query($calc)->row();
        $this->db->trans_complete();
        return array(
            'data' => $result->result(),
            'hit' => $result->num_rows(),
            'total' => $count->total,
            'start' => $startPage
        );
    }
}