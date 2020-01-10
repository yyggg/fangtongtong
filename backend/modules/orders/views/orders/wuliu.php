<?php
use backend\assets\LayuiAsset;
LayuiAsset::register($this);

$status = ['无轨迹', '已揽收', '在途中', '签收', '问题件'];
?>
<div class="user-view">
    <br>
    <?php if(!$data->Success):?>
        <p><h3>查询失败</h3></p>
    <?php else:?>
    <div class="layui-row">
        <div class="layui-col-xs3">
            <div class="grid-demo grid-demo-bg1">商品名称：<?= $order->goods_name; ?></div>
        </div>
        <div class="layui-col-xs3">
            <div class="grid-demo">运单号：<?= $order->logistic_code; ?></div>
        </div>
        <div class="layui-col-xs3">
            <div class="grid-demo grid-demo-bg1">物流公司：<?= $order->shipper_name; ?></div>
        </div>
        <div class="layui-col-xs3">
            <div class="grid-demo">物流状态：<span style="color: red;"><?= $status[$data->State]; ?></span></div>
        </div>
    </div>

    <ul class="layui-timeline">
        <?php foreach ($data->Traces as $v):?>
        <li class="layui-timeline-item">
            <i class="layui-icon layui-timeline-axis"></i>
            <div class="layui-timeline-content layui-text">
                <h3 class="layui-timeline-title"><?= $v->AcceptTime; ?></h3>
                <p>
                    <?= $v->AcceptStation; ?>
                </p>
            </div>
        </li>
        <?php endforeach;?>
    </ul>
    <?php endif;?>
</div>
