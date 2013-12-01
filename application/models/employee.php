<?php
class Employee extends CI_Model {
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
        $result = $this->db->query($sql, array($id));

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

    /**
     * This function returns basic user info based on the 
     * first name and last name.
     * NOTE: with the use of BINARY operator, first namr and 
     *       last name are case sensitive
     */
    function getUserAndType($id, $firstName, $lastName) {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException();
        }
        $sql = "SELECT e.emp_no, e.first_name, e.last_name, 
                       e.gender, e.hire_date, de.dept_no, d.dept_name,
                       IF(dm.dept_no IS NULL,false,true) as is_manager
                FROM employees AS e
                LEFT JOIN dept_manager as dm ON e.emp_no = dm.emp_no
                JOIN dept_emp AS de ON e.emp_no = de.emp_no
                JOIN departments AS d ON de.dept_no = d.dept_no 
                WHERE e.emp_no = ? AND BINARY e.first_name = ? AND BINARY e.last_name = ?;";
        $result = $this->db->query($sql, array($id, $firstName, $lastName));
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
                       MAX(t.to_date) as to_date
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
       
        $sql = "SELECT e.emp_no, e.first_name, e.last_name, e.gender,
                       t.title, e.hire_date, d.dept_name, e.birth_date, t.from_date, 
                       MAX(t.to_date) as to_date, d.dept_name, s.salary,
                       TIMESTAMPDIFF(YEAR, e.birth_date, CURRENT_DATE()) AS age,
                       TIMESTAMPDIFF(YEAR, e.hire_date, LEAST(t.to_date, CURRENT_DATE())) AS emp_year
                FROM departments AS d
                JOIN dept_emp as de ON d.dept_no = de.dept_no
                JOIN employees as e ON de.emp_no = e.emp_no
                JOIN titles as t ON e.emp_no = t.emp_no
                JOIN salaries as s ON e.emp_no = s.emp_no AND s.to_date = t.to_date
                WHERE e.emp_no = ? AND de.dept_no = ?
                ORDER BY e.first_name;";

        $query = $this->db->query($sql, array($userId, $deptNo));
  
        if ($query->num_rows() > 0) {
           $row = $query->row();
           return $row;
        } else {
            return false;
        }
    }
}