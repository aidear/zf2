function setHome(url){
        if (document.all) {
            document.body.style.behavior='url(#default#homepage)';
            document.body.setHomePage(url);
        }else{
            alert("抱歉,您的浏览器不支持自动设置页面为首页功能,请您手动在浏览器里设置该页面为首页!");
        }
 
}
$(function() {
	$("#search_in").focusin(function() {
		if ($(this).val() == '输入关键字') {
			$(this).val('');
		}
	});
	$("#search_in").focusout(function() {
		if ($(this).val() == '') {
			$(this).val('输入关键字');
		}
	});
	$(".Menubox1 > ul > li").click(function(){
		$(this).addClass('hover').siblings().removeClass('hover');
		$("input[name='tab']").val($(this).attr('date-tab'));
	});
	$("#btn_baidu").click(function(){
		$("input[name='engine']").val('baidu_engine');
		$("#form1").submit();
	});
	$("#btn_google").click(function(){
		$("input[name='engine']").val('google');
		$("#form1").submit();
	});
});