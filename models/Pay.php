<?php
namespace app\models;

use app\models\Order;
use app\models\OrderDetail;
use app\models\Product;
use AlipayPay;

class Pay
{
    public static function alipay($orderid) {
        //商品总价
        $amount = Order::find()->where('orderid = :orderid', [':orderid' => $orderid])->one()->amount;
        if (!empty($amount)) {
            $alipay = new AlipayPay();
            $subject = "京西商城";
            $data = OrderDetail::find()->where('orderid = :orderid', [':orderid' => $orderid])->all();
            $body = "";
            foreach($data as $v) {
                $body .= Product::find()->where('id = :id', [':id' => $v['productid']])->one()->title . " - ";
            }
            $body .= "等商品";
            $showUrl = "http://meng.julyangel.cn";
            $html = $alipay->requestPay($orderid, $subject, $amount, $body, $showUrl);
            echo $html;
        }
    }
    
    public static function notify($data) {
        $alipay = new AlipayPay();
        //针对notify_url验证消息是否是支付宝发出的合法消息
        $verify_result = $alipay->verifyNotify();
        if ($verify_result) {
            //订单id
            $out_trade_no = $data['extra_common_param'];
            //支付宝交易号
            $trade_no = $data['trade_no'];
            //支付宝交易状态
            $trade_status = $data['trade_status'];
            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                $status = Order::PAYSUCCESS;
                $order_info = Order::find()->where('orderid = :orderid', [':orderid' => $out_trade_no])->one();
                if (empty($order_info)) {
                    return FALSE;
                }
                if ($order_info->status == Order::CHECKORDER) {
                    Order::updateAll(['status' => $status, 'tradeno' => $trade_no, 'tradetext' => json_encode($data)], 'orderid = :orderid', [':orderid' => $order_info->orderid]);
                } else {
                    return FALSE;
                }
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }
    

}