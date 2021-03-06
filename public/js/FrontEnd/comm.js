function setHome(url){
        if (document.all) {
            document.body.style.behavior='url(#default#homepage)';
            document.body.setHomePage(url);
        }else{
            alert("抱歉,您的浏览器不支持自动设置页面为首页功能,请您手动在浏览器里设置该页面为首页!");
        }
 
}
function addBookmark(title,url) {
	try
    {
        window.external.addFavorite(url, title);
    }
    catch (e)
    {
        try
        {
            window.sidebar.addPanel(title, url, "");
        }
        catch (e)
        {
        	alert("抱歉,您所使用的浏览器无法完成此操作\n\n请使用Ctrl+D进行添加");
        }
    }
}

function getcookie(name) {
	var cookie_start = document.cookie.indexOf(name);
	var cookie_end = document.cookie.indexOf(";", cookie_start);
	return cookie_start == -1 ? '' : unescape(document.cookie.substring(cookie_start + name.length + 1, (cookie_end > cookie_start ? cookie_end : document.cookie.length)).replace(';', ''));
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
function setAreaCookie(name, val) {
	setcookie(name, val, 7000*24*3600);
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
//		if ($(this).val() == '输入关键字') {
//			$(this).val('');
//		}
	});
	$("#search_in").focusout(function() {
		if ($(this).val() == '') {
//			$(this).val('输入关键字');
		}
	});
	$(".alert").click(function() {
		$(this).hide();
	});
	$(".Menubox1 > ul > li").click(function(){
		$(this).addClass('hover').siblings().removeClass('hover');
		$("input[name='tab']").val($(this).attr('date-tab'));
	});
	$("#baidu_search_btn").click(function(){
		$("input[name='engine']").val('baidu_web');
		$("#form1").submit();
	});
	$("#google_search_btn").click(function(){
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
/* *
 * 调用此方法发送HTTP请求。
 *
 * @public
 * @param   {string}    url             请求的URL地址
 * @param   {mix}       params          发送参数
 * @param   {Function}  callback        回调函数
 * @param   {string}    transferMode     请求的方式，有"GET"和"POST"两种
 * @param   {string}    responseType    响应类型，有"JSON"、"XML"和"TEXT"三种
 * @param   {boolean}   asyn            是否异步请求的方式
 * @param   {boolean}   quiet           是否安静模式请求
 */
var Ajax = jQuery;
Ajax.call = function (url, params, callback, transferMode, responseType, asyn, quiet){
	if (url.indexOf('?') != -1) {
		url += '&t='+Math.random();
	} else {
		url += '?t='+Math.random();
	}
	this.ajax({
		url: url,
		async: asyn,
		data: params,
		type: transferMode,
		dataType: responseType,
		success: callback
	});	
}