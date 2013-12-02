<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * Authentication Library
 *
 * This class is the root of the auth system. It provides
 * functionality to login/logout and get user's session data.
 */
class LibAuth {
    /**
     * register CodeIgniter framework instance
     */
	public function __construct() {
    	$this->CI =& get_instance();
	}

    /**
     * log in a user,
     * NOTE: retired or dismissed user will not be able to log in
     * @param  string $firstName
     * @param  string $lastName
     * @param  integer $password
     * @return boolean
     */
    public function login($firstName, $lastName, $password, &$notice = null) {
    	//load employee model to connect to database and fetch data
    	$this->CI->load->model('Employee');
        try {
            $result = $this->CI->Employee->getUserAndType($password, $firstName, $lastName);
            //if rows are found, then this user exists thus we record session data
            if (count($result)) {
                $user = $result[0];

                //check the service to_date, if it is a past time, that means, this user
                //is no longer serving the company, show a friendly warning
                if ($user->to_date != '9999-01-01') {
                    $notice = 'Your login access privilege has ended since '. $user->to_date;
                    return false;
                }

                //this user exists, log him/her in
                $this->CI->session->set_userdata('loggedIn', true);
                $this->CI->session->set_userdata('firstName', $user->first_name);
                $this->CI->session->set_userdata('lastName', $user->last_name);
                $this->CI->session->set_userdata('userId', intval($user->emp_no));
                $this->CI->session->set_userdata('isManager', $user->is_manager);
                $this->CI->session->set_userdata('deptNo', $user->dept_no);
                $this->CI->session->set_userdata('deptName', $user->dept_name);
                return true;
            } else {
                //otherwise 
                return false;
            }
        } catch (InvalidArgumentException $ex) {
            return false;
        } catch (DBException $ex) {
            trigger_error('AUTH/DB: ' . $ex->getMessage());
            return false;
        } catch (Exception $ex) {
            trigger_error('AUTH/ERROR: ' . $ex->getMessage());
            return false;
        }
    	
    }

    /**
     * logout the current user
     * @return boolean
     */
    public function logout() {
    	$this->CI->session->sess_destroy();
    	return true;
    }

    /**
     * check if user has logged in
     * @return boolean
     */
    public function hasLoggedIn() {
    	if ($this->CI->session->userdata('loggedIn') === true) {
		    return true;
		} else {
		    return false;
		}
    }

    /**
     * check if current user is the manager of a department
     * @return boolean
     */
    public function isManager() {
        if ($this->CI->session->userdata('isManager') == '1') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * get the user's id from session
     * @return [type]
     */
    public function getUserId() {
    	return $this->CI->session->userdata('userId');
    }

    /**
     * get user name from session
     * @return [type]
     */
    public function getUserName() {
        if ($this->hasLoggedIn()) {
            return $this->CI->session->userdata('firstName') . ' ' . $this->CI->session->userdata('lastName');
        } else {
            return false;
        }	
    }

    public function getFirstName() {
        return $this->CI->session->userdata('firstName');
    }

    public function getLastName() {
        return $this->CI->session->userdata('lastName');
    }

    public function getDepartment() {
        return $this->CI->session->userdata('deptName');
    }

    public function getDeptNo() {
        return $this->CI->session->userdata('deptNo');
    }

}