<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {
    public function index() {
        $this->load->view('login');
    }

    public function submit(){

        $name = $this->input->post('name');
        $is_ajax=$this->input->post('ajax');
        $data['main_content']='contact_form_thanks';

        if($is_ajax){
            $this->load->view($data['main_content']);
        }else{
            $this->load->view('includes/template', $data);
        }

    }
}

/* End of file login.php */
/* Location: ./application/controllers/login.php */