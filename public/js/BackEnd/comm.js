//$(function(){
//    $(".alert").alert();
//});

//验证表单
//显示错误
var showError = function(jQDom , message){
    var jQDom = jQDom.parents('.input-group');
    jQDom.addClass('error').append("<span class='help-inline'>" + message + "</span>");
};

//字数限制
var StringLength = function(jQDom , maxLength){
    var count = maxLength - jQDom.val().length;
    if(count < 0){
        jQDom.parent().find(".help-inline").remove();
        jQDom.after("<span class='red help-inline'>&nbsp;&nbsp;&nbsp;&nbsp;已超出(" 
                + (0 - count) + ")个字,前台可能会换行</span>");
        return;
    }
    jQDom.parent().find(".help-inline").remove();
    jQDom.after("<span class='help-inline'>&nbsp;&nbsp;&nbsp;&nbsp;还可以输入(" 
            + count + ")个字,超出前台可能会换行</span>");
};

var diyConfirm = function(name , url, obj){
	if ($("input[type='radio'][name='select']:checked").size() == 0) {
		alert('请先选择一个条目');
		return false;
	}
	var id = $("input[type='radio'][name='select']:checked").val();
	var url = $(obj).attr('_href')+id;
	$(obj).attr('href', url);
    $('<div class="modal fade"><div class="modal-dialog"><div class="modal-content"><div class="modal-body">是否' + 
             name + '?</div><div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">关闭</button><a href=' 
             + url +' class="btn btn-primary">'
             + name +'</a></div></div></div></div>').modal({keyboard: false});
};

$(function(){
	$(".alert button.close").click(function() {
		$(this).parents('.alert').slideUp();
	});
	$("table.table tbody tr").mouseover(function() {
		$(this).css('background-color', '#E2E9EA');
	});
	$("table.table tbody tr").mouseout(function() {
		$(this).css('background-color', '#fff');
	});
	$(".t_right .btn-edit").click(function() {
		if ($("input[type='radio'][name='select']:checked").size() == 0) {
			alert('请先选择一个条目');
			return false;
		}
		var id = $("input[type='radio'][name='select']:checked").val();
		var url = $(this).attr('_href')+id;
		$(this).attr('href', url);
	});
});