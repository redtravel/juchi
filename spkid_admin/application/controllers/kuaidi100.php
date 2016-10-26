<?php
#doc
#	classname:	Kuaidi100
#	scope:		PUBLIC
#
#/doc
class Kuaidi100 extends CI_Controller
{
    public function __construct ()
	{
		parent::__construct();
        $this->time = date('Y-m-d H:i:s');
        $this->load->model('order_shipping_status_model');
	}
    /**
     * 接收快递100推送来的消息
     * @param type $id
     */
    public function call_back(){
        $json = $this->input->post('param');
        $body = json_decode($json,true);
        $lastResult = $body['lastResult'];
        $state = $lastResult['state'];
        $com = $lastResult['com'];
        $nu = $lastResult['nu'];
        $order_shipping = $this->order_shipping_status_model->filter(array('company'=>$com,'invoice_no'=>$nu));
        if($order_shipping){
            $id = $order_shipping->id;
            $update['shipping_detail']=json_encode($lastResult);
            $update['update_date']=$this->time;
            $update['invoice_state']=$state;
            $this->order_shipping_status_model->update($update,$id);
            echo '{"result":"true","returnCode":"200","message":"success"}';
            return;
        }else{
            echo '{"result":"false","returnCode":"200","message":"not found"}';
            return;
        }
        echo '{"result":"false","returnCode":"200","message":"unknown reason"}';
    }
}