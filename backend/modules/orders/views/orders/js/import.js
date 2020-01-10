layui.config({
	base : "js/"
}).use(['form','layer','jquery','upload'],function(){
	var form = layui.form,
		layer = parent.layer === undefined ? layui.layer : parent.layer,
		$ = layui.jquery;

        layui.upload.render({
        elem: '#test10'
        ,exts: 'xls'
        ,url: 'import'
        ,done: function(res){
            layer.msg('成功导入'+res.count+'条!');
        }
    });

});
