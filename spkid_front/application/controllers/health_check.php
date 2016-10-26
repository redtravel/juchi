<?php

/**
 * Description of check_db
 *
 * @author jasper
 */
class Health_check extends CI_Controller {

    function __construct() {
        parent::__construct();
    }

    function db_master() {
        $db_rw = $this->db;
        $query = $db_rw->query("SELECT 1 ");
        if ($query->num_rows() > 0)
            $this->disp(200);
        else
            $this->disp(417);
        die;
    }

    function db_slave() {
        $db_r = $this->load->database('default_r', TRUE, TRUE);
        $query = $db_r->query("SELECT 1 ");
        if ($query->num_rows() > 0)
            $this->disp(200);
        else
            $this->disp(417);
        die;
    }

    function index() {
        $db_rw = $this->db;
        $query = $db_rw->query("SELECT 1 ");
        if ($query->num_rows() < 1)
            $this->disp(417);

        $db_r = $this->load->database('default_r', TRUE, TRUE);
        $query = $db_r->query("SELECT 1 ");
        if ($query->num_rows() < 0)
            $this->disp(417);
        $this->disp(200);
    }

    function disp($arg) {
        set_status_header($arg);
        die;
    }

}
