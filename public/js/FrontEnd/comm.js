function setHome(url){
        if (document.all) {
            document.body.style.behavior='url(#default#homepage)';
            document.body.setHomePage(url);
        }else{
            alert("抱歉,您的浏览器不支持自动设置页面为首页功能,请您手动在浏览器里设置该页面为首页!");
        }
 
}
function getcookie(name) {
	var cookie_start = document.cookie.indexOf(name);
	var cookie_end = document.cookie.indexOf(";", cookie_start);
	return cookie_start == -1 ? '' : unescape(document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length)));
}
function setcookie(cookieName, cookieValue, seconds, path, domain, secure) {
	var expires = new Date();
	expires.setTime(expires.getTime() + seconds);
	document.cookie = escape(cookieName) + '=' + escape(cookieValue)
	+ (expires ? '; expires=' + expires.toGMTString() : '')
	+ (path ? '; path=' + path : '/')
	+ (domain ? '; domain=' + domain : '')
	+ (secure ? '; secure' : '');
}
var z_loc = '';
z_loc = getcookie('z_loc_a');
if (z_loc == '') {
	z_loc = getcookie('z_loc_c');
}
if (z_loc == '') {
	z_loc = getcookie('z_loc_p');
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
	$("#badu_btn").click(function(){
		$("input[name='engine']").val('baidu_web');
		$("#form1").submit();
	});
	$("#google_btn").click(function(){
		$("input[name='engine']").val('google');
		$("#form1").submit();
	});
	$("button.close").click(function() {
		$(this).parents('.alert').slideUp();
	});
});
function isUniqueUserName(user) {
	var rs = false;
	$.ajax({
		type:'POST',
		url:'/ajax?s=user',
		data:'name='+user,
		dataType:'json',
		cache:false,
		async:false,
		success:function(s) {
			if (s.code == 0) {
				rs = true;
			}
		}
	});
	return rs;
}