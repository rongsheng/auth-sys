<?php
/**
 * EmployeeDAO class contains RAW SQL script to fetch data from database
 * All inputs are presumed to be validated.
 */
class EmployeeDAO {
	/**
     * get a user by his/her employee number
     * @param  integer $id the employee number
     * @return array of data
     */
    public static function getById($db, $id) {
        $sql = "SELECT e.emp_no, e.first_name, e.last_name, e.gender,
                       e.hire_date
                FROM employees AS e
                WHERE e.emp_no = ?";
        $result = $db->query($sql, array($id));
        return $result;
    }

    /**
     * get user and type from db
     * @param integer $id the employee number
     * @param string $firstName
     * @param string $lastName
     */
    public static function getUserAndType($db, $id, $firstName, $lastName) {
    	$sql = "SELECT e.emp_no, e.first_name, e.last_name, 
                       e.gender, e.hire_date, de.dept_no, d.dept_name,
                       IF(dm.dept_no IS NULL,false,true) as is_manager
                FROM employees AS e
                LEFT JOIN dept_manager as dm ON e.emp_no = dm.emp_no
                JOIN dept_emp AS de ON e.emp_no = de.emp_no
                JOIN departments AS d ON de.dept_no = d.dept_no 
                WHERE e.emp_no = ? AND BINARY e.first_name = ? AND BINARY e.last_name = ?;";
        $result = $db->query($sql, 
        	array($id, $firstName, $lastName)
        );
        return $result;
    }
    
    /**
     * get all subordinate staff for a particular manager,
     * filters and pagination available as parameters
     * @param  integer $managerId
     * @param  integer $startPage
     * @param  integer $size
     * @param  string $column
     * @param  string $keyword
     * @return array of data
     */
    public static function getSubordinate($db, $managerId, $startPage, $size, $column, $keyword) {
    	$db->trans_start();
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
            $column = $db->escape_like_str($column);
            $keyword = $db->escape_like_str($keyword);
            $result = $db->query($sql, array($managerId, $startPage * $size, $size));
        } if ($searchMode == 'full-text') {
            $result = $db->query($sql, array($managerId, $startPage * $size, $size));
        } else {
            $result = $db->query($sql, array($managerId, $startPage * $size, $size));
        }
        
        $calc = "SELECT FOUND_ROWS() AS total;";
        $count = $db->query($calc)->row();
        $db->trans_complete();

        return array(
            'data' => $result->result(),
            'hit' => $result->num_rows(),
            'total' => $count->total,
            'start' => $startPage
        );
    }

    /**
     * fetch the user details within a department
     * @param int $userId the user id
     * @param int $deptNo the department this user belongs to
     */
    public static function getDetails($db, $userId, $deptNo) {
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

        $query = $db->query($sql, array($userId, $deptNo));
        return $query;
    }
}