<?php
return [
    'user.passwordResetTokenExpire' => 3600,
    'user.accessTokenExpire' => 86400*30,

    'errCode' => [
        '0' => ['errCode' => 0],
        '10001' => ['errCode' => 10001, 'errMsg' => '无数据'],
        '20001' => ['errCode' => 20001, 'errMsg' => '服务器繁忙，请稍候再试'],
        '30001' => ['errCode' => 30001, 'errMsg' => '登录已过期'],
        '30030' => ['errCode' => 30030, 'errMsg' => '注册失败'],
        '30100' => ['errCode' => 30100, 'errMsg' => '用户未找到'],
    ],

    'pageSize' => 15,
    'sale_status' => ['售罄', '在售', '待售'],
    'down_payment_name' => ['零首付', '10万首付', '20万首付', '30万首付', '其他'],
    'property_type_name' => ['不限', '住宅', '别墅', '商业', '商铺', '写字楼'],
    'house_type' => ['不限', '一室', '二室', '三室', '四室', '五室以上'],
    'unit_price' => [
        ['name' => '不限'],
        ['name' => '1.5万以下', 'min' => -1, 'max' => 15000],
        ['name' => '1.5-2.5万', 'min' => 15000, 'max' => 25000],
        ['name' => '2.5万-3万', 'min' => 25000, 'max' => 30000],
        ['name' => '3-4.5万', 'min' => 30000, 'max' => 45000],
        ['name' => '4.5万以上', 'min' => 45000, 'max' => 99999999]
    ],
    'total_price' => [
        ['name' => '不限'],
        ['name' => '100万以下', 'min' => -1, 'max' => 100],
        ['name' => '100-150万', 'min' => 100, 'max' => 150],
        ['name' => '150万-200万', 'min' => 150, 'max' => 200],
        ['name' => '200-250万', 'min' => 200, 'max' => 250],
        ['name' => '250-300万', 'min' => 250, 'max' => 300],
        ['name' => '300-500万', 'min' => 300, 'max' => 500],
        ['name' => '500万以上', 'min' => 500, 'max' => 9999],
    ],
    'square_metre' => [
        ['name' => '不限'],
        ['name' => '60以下', 'min' => -1, 'max' => 60],
        ['name' => '60-80', 'min' => 60, 'max' => 80],
        ['name' => '80-100', 'min' => 80, 'max' => 100],
        ['name' => '100-120', 'min' => 100, 'max' => 120],
        ['name' => '125-150', 'min' => 125, 'max' => 150],
        ['name' => '150-200', 'min' => 150, 'max' => 200],
        ['name' => '200以上', 'min' => 200, 'max' => 999999],
    ],
    'open_time' => [
        ['name' => '不限'],
        ['name' => '本月开盘', 'min' => strtotime("first day of this month 00:00:00"), 'max' => strtotime("last day of this month 23:59:59")],
        ['name' => '未来一个月', 'min' => time(), 'max' => strtotime("+1 month")],
        ['name' => '未来三个月', 'min' => time(), 'max' => strtotime("+3 month")],
        ['name' => '未来半年', 'min' => time(), 'max' => strtotime("+6 month")],
        ['name' => '过去一个月', 'min' => time(), 'max' => strtotime("-1 month")],
        ['name' => '过去三个月', 'min' => time(), 'max' => strtotime("-3 month")],
        ['name' => '全部已开盘', 'min' => 1262505855, 'max' => time()],
    ],
    'notice_category' => ['系统通知', '分销消息通知', '奖励通知'],
    'sub_notice_category' => [
        ['公告', '升级顾问审核通知'],
        ['分销审核成功提醒', '分销核对通知提醒', '佣金到账通知'],
        ['任务奖励领取通知', '任务完成通知'],
    ],
    'task_type' => ['邀请好友注册', '邀请好友参加活动', '分享活动', '分享顾问点评']

];
