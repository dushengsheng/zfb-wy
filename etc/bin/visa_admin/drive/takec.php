<?php

//author：Minet
//site：http://www.minet.cc
//version：1.0.0
//update：2017-12-24 08:00:00
class takec {

    //手动请求
    function request() {
        $mysql = functions::open_mysql();
        $orderId = intval(functions::request('id'));
        //判断是否是自己的订单
        $take = $mysql->query("takes", "id={$orderId}");
        $take = $take[0];
        //查询用户
        $users = $mysql->query("users", "id={$take['userid']}");
        if (!is_array($take))
            functions::json(7001, '订单错误');
        if ($take['state'] == 2)
            functions::json(7003, '该订单已是成功状态');
        $paytime = time();
        $orderNo = "AdminReback_" . $take['num'];
        $insert = $mysql->insert('orders', array(
            'land_id' => $take['land_id'],
            'userid' => $take['userid'],
            'num' => $take['num'],
            'money' => $take['money'],
            'remark' => $take['mark'],
            'payc' => $take['payc'],
            'order_time' => $paytime,
            'api_state' => 1,
            'http' => '还未请求',
            'request_time' => 0,
            'payment' => 0,
            'takes_id' => $take['id'],
            'orderNo' => $orderNo,
            'type' => $take['type'],
            'agentid' => $take['agentid'],
            'version' => $take['version'],
            'sending_times' => $take['sending_times'] + 1
        ));
        if ($insert > 0) {
            $update = $mysql->update('takes', array('pay_time' => $paytime, 'state' => 2, 'orderNo' => $orderNo, 'sending_times' => $take['sending_times'] + 1), "id={$take['id']}");
            if ($update > 0) {
                //创建订单
                //exit('订单处理成功');
                //更新二维码状态为空闲状态
                if ($take['qr_type'] != 2 && $take['qr_type'] != 3) {
                    $tradeRemark = $take['mark'];
                    $row_count = $mysql->update("qrcode_link", array('state' => 1), "mark='{$tradeRemark}'");
                }
                if ($take['payc'] == 3) {
                    $qrcode_info = $take['info'];
                    $row_count = $mysql->delete("qrcode_link", "info='{$qrcode_info}'");
                }
                //更新收款账号额度
                $land = $mysql->query("land", "id={$take['land_id']} and userid={$take['userid']}");
                $land = $land[0];
                if (floatval($land['requota']) != -1) {
                    $new_quota = floatval($land['quota']) + floatval($take['money']);
                    //如果额度用完，将收款账户状态更改为"2"（数据库任务将恢复该状态）
                    if ($new_quota < floatval($land['requota'])) {
                        $mysql->update("land", array('quota' => $new_quota), "id={$land['id']} and userid={$land['userid']}");
                    } else {
                        $mysql->update("land", array('quota' => $land['requota'], 'status' => 2), "id={$land['id']} and userid={$land['userid']}");
                    }
                }
                $order = $mysql->query("orders", "num={$take['num']} and userid={$take['userid']}");
//                if (!empty($order[0]['agentid'])) {
//                    $poundage = functions::get_orderFee($order[0]['userid'], $order[0]['payc']);
//                    $agent_poundage = functions::get_agentFee($order[0]['agentid'], $order[0]['payc']);
//                } else {
//                    $poundage = functions::get_orderFee($order[0]['userid'], $order[0]['payc']);
//                    $agent_poundage = 0;
//                }
                //$row = functions::api('reback')->request($mysql, $order[0]['id'], $poundage, $users[0], $agent_poundage);
                $row = functions::api('reback')->request($mysql, $order[0]['id'], $users[0]);
                if ($row) {
                    functions::json(200, '请求成功');
                } else {
                    functions::json(7002, '请求失败,请重试');
                }
            } else {
                functions::json(7004, '系统错误,原因:写入订单失败!');
            }
        } else {
            functions::json(7005, '系统错误,原因:更新订单数据失败');
        }
    }

}
