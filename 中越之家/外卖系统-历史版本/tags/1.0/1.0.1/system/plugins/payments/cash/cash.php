<?php
/**
 * Copy Right IJH.CC
 * Each engineer has a duty to keep the code elegant
 * $Id: tenpay.php 9343 2015-03-24 07:07:00Z youyi $
 */

class Payment_Cash
{
    
	private $gateway = '';
	private $transport = 'https';
	private $sign_type = 'MD5';

	//支付接口配置信息
    private $config = array();
    //订单信息
    private $order = array();

	public function __construct($cfg)
    {
        $this->config = $cfg;
        $this->_parameter = array();
        $this->_parameter['return_url'] = $cfg['return_url'];
        $this->_parameter['notify_url'] = $cfg['notify_url'];           
    }

    public function build_app($params)
    {
        $parameter = array(
            'order_id'       => $params['order_id'],
            'amount'     => sprintf("%01.2f", $params['amount']),
            'trade_no'  => $params['trade_no'],
        );
        return $parameter;
    }
	
	public function build_url($params)
    {	
        $parameter = array(
            'order_id'       => $params['order_id'],
            'order_ids'      => $params['order_ids'],
            'amount'         => sprintf("%01.2f", $params['amount']),
            'trade_no'       => $params['trade_no'],
            'code'       => $params['code']
        );
        return $parameter;
    }

    public function build_form($params)
    {      
        $parameter = array(
            'order_id'       => $params['order_id'],
            'amount'     => sprintf("%01.2f", $params['amount']),
            'trade_no'  => $params['trade_no'],
        );
        return $parameter;
    }


	public function return_verify()
    {

        return array('code'=>'cash','trade_no'=>$log['out_trade_no'], 'amount'=>$log['amount']);
    }

	public function notify_verify()
    {
        return array('code'=>'cash','trade_no'=>$log['out_trade_no'], 'amount'=>$log['amount']);
    }

    public function money_verify()
    {

    }

    public function query($params, &$trade_status='WAITPAY')
    {
        return false;
    }

    public function refund($input, &$msg='SUCCESS')
    {
        $msg = '直接退回余额';
        return false;
    }

    public function notify_success($success=true)
    {
        if($success){
            echo "success";exit;
        }else{
            echo "fail";exit;
        }
    }


    protected function _logs($log)
    {
        $key = 'payment-money-'.date('Ymd');
        K::M('system/logs')->log($key, $log);
    }       
}
