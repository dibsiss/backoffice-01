<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends Sales_Controller {

   
    public function __construct() {
        parent::__construct();

        $this->load->database();
        $this->load->helper(array('form', 'url'));
        $this->load->library('grocery_CRUD');
        $this->load->model('general_model', 'general', true);
        $this->load->model('Master_model', 'master', true);
    }

    public function index(){
        echo "sales";
    }
}
