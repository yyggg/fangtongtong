layui.config({
	base : "js/"
}).use(['form','layer','jquery','laydate'],function(){
	var laydate = layui.laydate;
	//日期时间选择器
	laydate.render({
		elem: '#order-time'
		,type: 'datetime'
	});
});
