<?php
#doc
#	classname:	CI_Bill
#	scope:		PUBLIC
#
#/doc

class CI_Bill 
{

	function __construct ( )
	{
		$this->CI=&get_instance();
		$this->bill_config = $this->CI->config->item('bill');
		$this->bill_config['bgUrl'] = site_url('pay/bill');
	}
	
	public function get_link ($order)
	{
		$param="";		
		$link=$this->bill_config['gate'];
		
		$param=$this->appendParam("inputCharset",'1');
		//$param.=$this->appendParam("pageUrl",$this->bill_config['bgUrl']);
		$param.=$this->appendParam("bgUrl",$this->bill_config['bgUrl']);
		$param.=$this->appendParam("version",'v2.0');
		$param.=$this->appendParam("language",'1');
		$param.=$this->appendParam("signType",'4');
		$param.=$this->appendParam("merchantAcctId",$this->bill_config['merchantAcctId']);		
		$param.=$this->appendParam("orderId",$order->order_sn);
		$param.=$this->appendParam("orderAmount",round(($order->order_price+$order->shipping_fee-$order->paid_price)*100));
		$param.=$this->appendParam("orderTime",date('YmdHis'));
		$param.=$this->appendParam("productName",$order->order_sn);
		$param.=$this->appendParam("productNum","1");
		$param.=$this->appendParam("payType",'00');	
		$param.=$this->appendParam("redoFlag",'0');	
		$param=rtrim($param,'&');

		$priv_key = file_get_contents($this->bill_config['privatekey']);
		$pkeyid = openssl_get_privatekey($priv_key);
		// compute signature
		openssl_sign($param, $signMsg, $pkeyid,OPENSSL_ALGO_SHA1);
		// free the key from memory
		openssl_free_key($pkeyid);
	 	$signMsg = base64_encode($signMsg);	
		$signMsg=urlencode($signMsg);
		$link.="?{$param}&signMsg={$signMsg}";
		return $link;
	
	}
	
	public function verify ($get)
	{
		if($this->bill_config['merchantAcctId']!=$get['merchantAcctId']){
			return FALSE;
		}
		$param="";
		//生成加密串。必须保持如下顺序。
		$param=$this->appendParam("merchantAcctId",$get['merchantAcctId']);
		$param.=$this->appendParam("version",$get['version']);
		$param.=$this->appendParam("language",$get['language']);
		$param.=$this->appendParam("signType",$get['signType']);
		$param.=$this->appendParam("payType",$get['payType']);
		$param.=$this->appendParam("bankId",$get['bankId']);
		$param.=$this->appendParam("orderId",$get['orderId']);
		$param.=$this->appendParam("orderTime",$get['orderTime']);
		$param.=$this->appendParam("orderAmount",$get['orderAmount']);
		$param.=$this->appendParam("dealId",$get['dealId']);
		$param.=$this->appendParam("bankDealId",$get['bankDealId']);
		$param.=$this->appendParam("dealTime",$get['dealTime']);
		$param.=$this->appendParam("payAmount",$get['payAmount']);
		$param.=$this->appendParam("fee",$get['fee']);
		$param.=$this->appendParam("ext1",$get['ext1']);
		$param.=$this->appendParam("ext2",$get['ext2']);
		$param.=$this->appendParam("payResult",$get['payResult']);
		$param.=$this->appendParam("errCode",$get['errCode']);
		$param=rtrim($param,'&');
		
		$MAC=base64_decode($get['signMsg']);
		$cert = file_get_contents($this->bill_config['publickey']);
		$pubkeyid = openssl_get_publickey($cert); 
		$ok = openssl_verify($param, $MAC, $pubkeyid);
		if($ok!=1){
			return FALSE;
		}
		return $get['payResult']=='10';
	}
	
	private function appendParam($kq_na,$kq_va){
		if($kq_va == ""){
			$kq_va="";
		}else{
			return $kq_va=$kq_na.'='.$kq_va.'&';
		}
	}
	
	public function fail($order=NULL)
	{
		return "<result>0</result><redirecturl>".($order?site_url('order/info/'.$order->order_id):'')."</redirecturl>";
	}
	
	public function success($order=NULL)
	{
		return "<result>1</result><redirecturl>".($order?site_url('order/info/'.$order->order_id):'')."</redirecturl>";
	}

}
###