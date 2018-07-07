<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Gudang_Controller extends CI_Controller {

    function __construct() {
        parent ::__construct();
        $this->CI = & get_instance();

        if (!$this->access->is_login_gudang()) {
            redirect('login/superadmin');
        }
    }

    function is_login_gudang() {
        return $this->access->is_login_gudang();
    }

}

class Superadmin_Controller extends CI_Controller {

    function __construct() {
        parent ::__construct();
        if (!$this->access->is_login_superadmin()) {
            redirect('login/superadmin');
        }
    }

    function is_login_superadmin() {
        return $this->access->is_login_superadmin();
    }

}

class Gudang_accounting_Controller extends CI_Controller {

    function __construct() {
        parent ::__construct();
        if (!$this->is_login_accounting()) {
            redirect('login/superadmin');
        }
    }

    function is_login_accounting() {
        $login = $this->access->is_login_gudang();
        if ($login) {
            return $this->access->is_Gudang_accounting();
        } else {
            return false;
        }
    }

}

class Gudang_admin_Controller extends CI_Controller {

    function __construct() {
        parent ::__construct();
        if (!$this->is_login_admin()) {
            redirect('login/superadmin');
        }
    }

    function is_login_admin() {
        $login = $this->access->is_login_gudang();
        if ($login) {
            return $this->access->is_Gudang_admin();
        } else {
            return false;
        }
    }

}

class Kepala_toko_Controller extends CI_Controller {

    function __construct() {
        parent ::__construct();
        if (!$this->is_login_toko()) {
            redirect('login/superadmin');
        }
    }

    function is_login_toko() {
        $login = $this->access->is_login_toko();
        if ($login) {
            return $this->access->is_kepala_toko();
        } else {
            return false;
        }
    }

}

class Sales_Controller extends CI_Controller {

    function __construct() {
        parent ::__construct();
        if (!$this->is_login_sales()) {
            redirect('login/superadmin');
        }
    }

    function is_login_sales() {
        $login = $this->access->is_login_toko();
        if ($login) {
            return $this->access->is_sales();
        } else {
            return false;
        }
    }

}

class Kasir_Controller extends CI_Controller {

    function __construct() {
        parent ::__construct();
        if (!$this->is_login_kasir()) {
            redirect('login/superadmin');
        }
    }

    function is_login_kasir() {
        $login = $this->access->is_login_toko();
        if ($login) {
            return $this->access->is_kasir();
        } else {
            return false;
        }
    }

}

class MY_Controller extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

}
