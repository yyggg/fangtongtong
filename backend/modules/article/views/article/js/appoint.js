layui.config({
	base : "js/"
}).use(['form','layer','jquery'],function(){

	var keys = window.parent.$("#grid").attr('data-ids');
	$('#order_ids').val(keys);

	$('.appoint').on('click',function () {
		$('.appoint').attr('disabled', true);
		$.post('appoint', {appoint_admin_id: $('#appoint_admin_id').val(), ids: $('#order_ids').val()}, function (res) {

			var index = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
			parent.layer.close(index); //再执行关闭
		});
	});
});
