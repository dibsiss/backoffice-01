<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {

    public function __construct() {
        parent::__construct();
    }

    function index() {
        $this->session->set_userdata('testkey', 'testvalue');
        echo 'set session';
    }

    function getsess() {
        echo $this->session->userdata('testkey');
    }

}
