<?php
//
//    ______         ______           __         __         ______
//   /\  ___\       /\  ___\         /\_\       /\_\       /\  __ \
//   \/\  __\       \/\ \____        \/\_\      \/\_\      \/\ \_\ \
//    \/\_____\      \/\_____\     /\_\/\_\      \/\_\      \/\_\ \_\
//     \/_____/       \/_____/     \/__\/_/       \/_/       \/_/ /_/
//
//   上海商创网络科技有限公司
//
//  ---------------------------------------------------------------------------------
//
//   一、协议的许可和权利
//
//    1. 您可以在完全遵守本协议的基础上，将本软件应用于商业用途；
//    2. 您可以在协议规定的约束和限制范围内修改本产品源代码或界面风格以适应您的要求；
//    3. 您拥有使用本产品中的全部内容资料、商品信息及其他信息的所有权，并独立承担与其内容相关的
//       法律义务；
//    4. 获得商业授权之后，您可以将本软件应用于商业用途，自授权时刻起，在技术支持期限内拥有通过
//       指定的方式获得指定范围内的技术支持服务；
//
//   二、协议的约束和限制
//
//    1. 未获商业授权之前，禁止将本软件用于商业用途（包括但不限于企业法人经营的产品、经营性产品
//       以及以盈利为目的或实现盈利产品）；
//    2. 未获商业授权之前，禁止在本产品的整体或在任何部分基础上发展任何派生版本、修改版本或第三
//       方版本用于重新开发；
//    3. 如果您未能遵守本协议的条款，您的授权将被终止，所被许可的权利将被收回并承担相应法律责任；
//
//   三、有限担保和免责声明
//
//    1. 本软件及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的；
//    2. 用户出于自愿而使用本软件，您必须了解使用本软件的风险，在尚未获得商业授权之前，我们不承
//       诺提供任何形式的技术支持、使用担保，也不承担任何因使用本软件而产生问题的相关责任；
//    3. 上海商创网络科技有限公司不对使用本产品构建的商城中的内容信息承担责任，但在不侵犯用户隐
//       私信息的前提下，保留以任何方式获取用户信息及商品信息的权利；
//
//   有关本产品最终用户授权协议、商业授权与技术服务的详细内容，均由上海商创网络科技有限公司独家
//   提供。上海商创网络科技有限公司拥有在不事先通知的情况下，修改授权协议的权力，修改后的协议对
//   改变之日起的新授权用户生效。电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和
//   等同的法律效力。您一旦开始修改、安装或使用本产品，即被视为完全理解并接受本协议的各项条款，
//   在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本
//   授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。
//
//  ---------------------------------------------------------------------------------
//
defined('IN_ECJIA') or exit('No permission resources.');

/**
 * 订单状态日志记录
 *
 */
class OrderStatusLog
{
    /**
     * 生成订单时状态日志
     * @param array $options
     * @return bool
     */
    public static function generate_order($options) {
    	$data = array(
    	'order_status'	=> RC_Lang::get('cart::shopping_flow.label_place_order'),
    	'order_id'		=> $options['order_id'],
    	'message'		=> '下单成功，订单号：'.$options['order_sn'],
    	'add_time'		=> RC_Time::gmtime(),
    	);
    	 RC_DB::table('order_status_log')->insert($data);
        return true;
    }

    /**
     * 生成订单同时提醒付款
     * @param array $options
     * @return bool
     */
    public static function remind_pay($options) {
		RC_DB::table('order_status_log')->insert(array(
			'order_status'	=> RC_Lang::get('cart::shopping_flow.unpay'),
			'order_id'		=> $options['order_id'],
			'message'		=> '请尽快支付该订单，超时将会自动取消订单',
			'add_time'		=> RC_Time::gmtime(),
		));
        return true;
    }
    
    /**
     * 订单付款成功时
     * @param array $options
     * @return bool
     */
    public static function order_paid($options) {
    	 RC_DB::table('order_status_log')->insert(array(
    	    'order_status'	=> RC_Lang::get('orders::order.ps.'.PS_PAYED),
    	    'order_id'		=> $options['order_id'],
    	    'message'		=> RC_Lang::get('orders::order.notice_merchant_message'),
    	    'add_time'		=> RC_Time::gmtime(),
	    ));
    	return true;
    }
    
    
    /**
     * 订单付款成功时同时通知商家
     * @param array $options
     * @return bool
     */
    public static function notify_merchant($options) {
    	 RC_DB::table('order_status_log')->insert(array(
    	    'order_status'	=> RC_Lang::get('cart::shopping_flow.merchant_process'),
    	    'order_id'		=> $options['order_id'],
    	    'message'		=> '订单已通知商家，等待商家处理',
    	    'add_time'		=> RC_Time::gmtime(),
	    ));
    	return true;
    }
    
    /**
     * 发货单入库
     * @param array $options
     * @return bool
     */
    public static function generate_delivery_orderInvoice($options) {
    	$data = array(
    			'order_status' 	=> RC_Lang::get('orders::order.ss.'.SS_PREPARING),
    			'order_id'   	=> $options['order_id'],
    			'message'		=> sprintf(RC_Lang::get('orders::order.order_prepare_message'), $options['order_sn']),
    			'add_time'     	=> RC_Time::gmtime()
    	);
    	RC_DB::table('order_status_log')->insert($data);
    	return true;
    }
    
    /**
     * 完成发货
     * @param array $options
     * @return bool
     */
    public static function delivery_ship_finished($options) {
    	$data = array(
    			'order_status'	=> RC_Lang::get('orders::order.ss.'.SS_SHIPPED),
    			'message'       => sprintf(RC_Lang::get('orders::order.order_send_message'), $options['order_sn']),
    			'order_id'    	=> $options['order_id'],
    			'add_time'    	=> RC_Time::gmtime(),
    	);
    	RC_DB::table('order_status_log')->insert($data);
    	return true;
    }
    
    /**
     * 订单确认收货
     * @param array $options
     * @return bool
     */
    public static function affirm_received($options) {
    	$order_status_data = array(
        		'order_status' => RC_Lang::get('orders::order.confirm_receipted'),
        		'order_id' 	   => $options['order_id'],
        		'message'	   => '宝贝已签收，购物愉快！',
        		'add_time'	   => RC_Time::gmtime()
        );
        RC_DB::table('order_status_log')->insert($order_status_data);
        
        $order_status_data = array(
        		'order_status' => RC_Lang::get('orders::order.order_finished'),
        		'order_id' 	   => $options['order_id'],
        		'message'	   => '感谢您在'.ecjia::config('shop_name').'购物，欢迎您再次光临！',
        		'add_time'	   => RC_Time::gmtime()
        );
        RC_DB::table('order_status_log')->insert($order_status_data);
    	return true;
    }
    
    /**
     * 取消订单
     * @param array $options
     * @return bool
     */
    public static function cancel($options) {
    	RC_DB::table('order_status_log')->insert(array(
	        'order_status'	=> RC_Lang::get('orders::order.order_cancel'),
	        'order_id'		=> $options['order_id'],
	        'message'		=> '您的订单已取消成功！',
	        'add_time'		=> RC_Time::gmtime(),
        ));
    	return true;
    }
    
    /**
     * 仅退款订单已处理
     * @param array $options
     * @return bool
     */
    public static function refund_order_process($options) {
    	if($options['status'] == 1) {
    		$message = '申请审核已通过';
    	} else {
    		$message ='申请审核未通过';
    	}
    	$data = array(
    			'order_status'	=> '订单退款申请已处理',
    			'message'       => $message,
    			'order_id'    	=> $options['order_id'],
    			'add_time'    	=> RC_Time::gmtime(),
    	);
    	RC_DB::table('order_status_log')->insert($data);
    	return true;
    }
    
    /**
     * 退货退款订单已处理
     * @param array $options
     * @return bool
     */
    public static function return_order_process($options) {
    	if($options['status'] == 1) {
    		$message = '申请审核已通过，请选择返回方式';
    	} else {
    		$message ='申请审核未通过';
    	}
    	$data = array(
    		'order_status'	=> '订单退货退款申请已处理',
    		'message'       => $message,
    		'order_id'    	=> $options['order_id'],
    		'add_time'    	=> RC_Time::gmtime(),
    	);
    	RC_DB::table('order_status_log')->insert($data);
    	return true;
    }
    
    /**
     * 订单确认收货处理
     * @param array $options
     * @return bool
     */
    public static function return_confirm_receive($options) {
    	if($options['status'] == 3) {
    		$message = '商家已确认收货，等价商家退款';
    	} else {
    		$message = '商家拒绝确认收货，理由：商品没有问题';
    	}
    	$data = array(
    		'order_status'	=> '确认收货处理',
    		'message'       => $message,
    		'order_id'    	=> $options['order_id'],
    		'add_time'    	=> RC_Time::gmtime(),
    	);
    	RC_DB::table('order_status_log')->insert($data);
    	return true;
    }
    
    /**
     * 订单退款到账处理
     * @param array $options
     * @return bool
     */
    public static function refund_payrecord($options) {
    	$data = array(
    		'order_status'	=> '退款到账',
    		'message'       => '您的退款'.$options['back_money'].'元，已退回至您的余额，请查收',
    		'order_id'    	=> $options['order_id'],
    		'add_time'    	=> RC_Time::gmtime(),
    	);
    	RC_DB::table('order_status_log')->insert($data);
    	return true;
    }
}
