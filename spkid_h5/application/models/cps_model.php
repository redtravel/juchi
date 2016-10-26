<?php

/**
 * Cart_model
 */
class Cps_model extends CI_Model {

    public function add($order_id, $cps_params) {
        $this->load->driver("cache");
        $cpstag = $cps_params->tag;
        $cps_data = $this->cache->get("CACHE_CPS_DATA_" . $cpstag);
        if (empty($cps_data)) {
            $sql = "SELECT * FROM ty_cps c WHERE c.cps_sn = ? AND cps_status = 1 AND cps_start_time < now() AND cps_shut_time > now() LIMIT 1";
            $query = $this->db_r->query($sql, array($cpstag));
            $cps_data = $query->row_array();
            if (empty($cps_data)) {
                $cps_data = array("cps_status" => 0);
                $this->cache->save("CACHE_CPS_DATA_" . $cpstag, $cps_data, CACHE_TIME_COMMON);
                return;
            }
            $this->cache->save("CACHE_CPS_DATA_" . $cpstag, $cps_data, CACHE_TIME_COMMON);
        } else {
            if ($cps_data["cps_status"] != 1 || strtotime($cps_data["cps_start_time"]) > time() || strtotime($cps_data["cps_shut_time"]) < time()) {
                return;
            }
        }
        $dt = $cps_params->dt / 1000;
        if ((time() - $dt) / 86400 > $cps_data["cps_cookie_time"]) {
            return NULL;
        }
        $sql = "SELECT * FROM ty_order_info oi WHERE oi.order_id = ? LIMIT 1";
        $query = $this->db_r->query($sql, array($order_id));
        $order_info = $query->row_array();
        $_cd = json_decode($cps_data["cps_data"]);
        if (isset($_cd->user_name_tag)) {
            $cps_user_name = @$_cd->user_name_tag;
            $cps_user_name = @$cps_params->$cps_user_name;
        }
        if (empty($cps_user_name)) {
            $cps_user_name = "";
        }
        $sql = "INSERT INTO ty_cps_log (cps_id, user_id, cps_user_name, order_id, user_ip, cps_price, cps_time, cps_log_data)
                                    VALUES (?,      ?,       ?,        ?,        ?,       ?,         now(),    ?           )";
        $this->db->query($sql, array($cps_data["cps_id"], $order_info["user_id"], $cps_user_name, $order_info["order_id"], real_ip(), $order_info["order_price"], json_encode($cps_params)));
    }
    public function script($order_id, $cps_params) {
        $this->load->driver("cache");
        $cpstag = $cps_params->tag;
        $cps_data = $this->cache->get("CACHE_CPS_DATA_" . $cpstag);
        if (empty($cps_data)) {
            $sql = "SELECT * FROM ty_cps c WHERE c.cps_sn = ? AND cps_status = 1 AND cps_start_time < now() AND cps_shut_time > now() LIMIT 1";
            $query = $this->db_r->query($sql, array($cpstag));
            $cps_data = $query->row_array();
            if (empty($cps_data)) {
                $cps_data = array("cps_status" => 0);
                $this->cache->save("CACHE_CPS_DATA_" . $cpstag, $cps_data, CACHE_TIME_COMMON);
                return NULL;
            }
            $this->cache->save("CACHE_CPS_DATA_" . $cpstag, $cps_data, CACHE_TIME_COMMON);
        } else {
            if ($cps_data["cps_status"] != 1 || strtotime($cps_data["cps_start_time"]) > time() || strtotime($cps_data["cps_shut_time"]) < time()) {
                return NULL;
            }
        }
        $dt = $cps_params->dt / 1000;
        if ((time() - $dt) / 86400 > $cps_data["cps_cookie_time"]) {
            return NULL;
        }
        $cps_rtn_script = $cps_data["cps_rtn_script"];
        $cps_data = json_decode($cps_data["cps_data"]);
        $sql = "SELECT * FROM ty_order_info oi WHERE oi.order_id = ? LIMIT 1";
        $query = $this->db_r->query($sql, array($order_id));
        $order_info = $query->row_array();
        if (empty($order_info)) {
            return NULL;
        }
        $sql = "SELECT * FROM ty_order_product op INNER JOIN ty_product_info USING(product_id) INNER JOIN ty_product_category USING(category_id) WHERE op.order_id = ? ";
        $query = $this->db_r->query($sql, array($order_id));
        $order_products = $query->result_array();
        $order_goods = $order_products;
        $sql = "SELECT sum(payment_money) AS payment_money 
                FROM ty_order_payment op
                INNER JOIN ty_payment_info pi
                USING(pay_id)
                WHERE pi.is_discount = 1
                AND op.order_id = ?
                ;";
        $query = $this->db_r->query($sql, array($order_id));
        $discount = $query->row_array();
        $discount = $discount["payment_money"];
        if (empty($order_goods)) {
            return NULL;
        }
        $script_src = null;
        @eval($cps_rtn_script);
        if (isset($script_src)) {
            return "<script type='text/javascript' src='$script_src' ></script>";
        }
        return null;
    }

}