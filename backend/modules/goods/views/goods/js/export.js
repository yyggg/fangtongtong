layui.config({
	base : "js/"
}).use(['form','layer','jquery'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		$ = layui.jquery;
    //时间选择
    layui.use('laydate', function() {
        var laydate = layui.laydate;
        laydate.render({
            elem: '#order_time'
            ,type: 'datetime'
            ,range: '到'
            ,trigger: 'click'
            ,format: 'yyyy-MM-dd HH:mm:ss'
        });
    });

});
