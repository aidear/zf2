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
	var id = '';
	var data = '';
	var key = '';
	if ($("input[type='checkbox'][name='select']:checked").size() == 0) {
		alert('请先选择一个条目');
		return false;
	} else if ($("input[type='checkbox'][name='select']:checked").size() >= 1) {
		key = $("input[type='checkbox'][name='select']:checked").attr('data-key');
		$("input[type='checkbox'][name='select']:checked").each(function() {
			id += (id == '') ? $(this).val() : ','+$(this).val();
			data += (data == '') ? $(this).attr('data-value') : ', '+$(this).attr('data-value');
		});
	}
	var url = $(obj).attr('_href')+id;
	$(obj).attr('href', url);
//    $('<div class="modal fade"><div class="modal-dialog"><div class="modal-content"><div class="modal-body">是否' + 
//             name +' '+ key+' 为'+data+'的条目?</div><div class="modal-footer"><button type="button" class="btn btn-success" data-dismiss="modal">关闭</button><a href=' 
//             + url +' class="btn btn-delete">'
//             + name +'</a></div></div></div></div>').modal({keyboard: false});
	$('<div>是否' + 
            name +' '+ key+' 为<strong>'+data+'</strong>的条目?</div>').dialog({title:'提示',buttons: [ { text: "删除", click: function() {
            location.href=url;
   			 $( this ).dialog( "close" ); 
			} } ]});
};
var filter = {
		filterForm:"frmFilter",
		fields:'<select name="select_field">'
			 +'<option value="UserName" vtype="string" ctype="text">用户名</option>'
			 +'</select>',
		rel:'<select name="relationship"><option value="1">与</option><option value="2">或</option><option value="3">非</option></select>',
		opt:['<select name="select_opt"><option value="than">大于</option><option value="lthan">小于</option><option value="nequal">等于</option><option value="sthan">小于等于</option><option value="bthan">大于等于</option></select>',
             '<select name="select_opt"><option value="in">包含</option><option value="eq">等于</option></select>',
             '<input type="radio" value="0" name="filter_value">保密<input type="radio" value="1" name="filter_value">男<input type="radio" value="2" name="filter_value">女'],
		init:function(fields) {
			filter.setFields(fields);
			filter.fieldBind();
			filter.addCond();
			filter.delCond();
			$("#"+filter.filterForm+" .filter_opt").html(filter.opt[1]);
		},
		setFields: function(fields) {
			var options = '<select name="select_field">';
			for(var i in fields) {
				options += '<option value="'+fields[i].name+'" vtype="'+fields[i].vtype+'"'
				+' ctype="'+fields[i].ctype+'">'+fields[i].html+'</option>';
			}
			options += '</select>';
			filter.fields = options;
			$("#"+filter.filterForm+" .filter_label").html(options);
		},
		fieldBind:function() {
			$("#frmFilter").delegate(".filter_label select[name='select_field']", 'change',function(){
				var selv = $(this).val();
				if (selv) {
					var vtype = $(this).find("option:selected").attr('vtype');
					var ctype = $(this).find("option:selected").attr('ctype');
					switch (vtype) {
					case 'string':
						$(this).parents('.filter_label').next().html(filter.opt[1]);
						$(this).parents('.filter_label').nextAll('.filter_val').html('<input type="text" name="filter_value" value="" />');
						break;
					case 'int':
						if (ctype == 'radio') {
							$(this).parents('.filter_label').nextAll('.filter_val').html(filter.opt[2]);
							$(this).parents('.filter_label').next().html('');
						} else {
							$(this).parents('.filter_label').next().html(filter.opt[0]);
							$(this).parents('.filter_label').nextAll('.filter_val').html('<input type="text" name="filter_value" value="" />');
						}
						break;
						default:
							break;
					}
				}
		    });
		},
		addCond:function() {
			$(".condition_opt .add_c").bind('click', function(){
				$('<div class="filter_item">'
						 +'<div class="filter_rel">'
						 +filter.rel
						 +'</div>'
						 +'<div class="filter_label">'
						 +filter.fields
						 +'</div>'
						 +'<div class="filter_opt">'
						 +filter.opt[1]
						 +'</div>'
						 +'<div class="filter_val"><input value="" type="text" name="filter_value" /></div>'
						 +'<div class="condition_opt"><span class="del_c"> — </span></div>'
						 +'<div style="clear: both;">&nbsp;</div>'
						 +'</div>').appendTo("#frmFilter");
		    });
		},
		delCond:function() {
			$("#frmFilter").delegate(".condition_opt .del_c", 'click', function(){
				$(this).parents("div.filter_item").remove();
		    });
		},
		getQuery: function() {
			var select_fields = '';
			$("select[name='select_field'] option:selected").each(function(){
				select_fields += (select_fields == '' ? $(this).val() : '|'+$(this).val());
			});
			var select_opts = '';
			$(".filter_opt").each(function(){
				var opt_tmp = $(this).find("select[name='select_opt'] option:selected").val();
				
				opt_tmp = (typeof opt_tmp != 'undefined') ? opt_tmp : 'eq';
				select_opts += (select_opts == '' ? opt_tmp : '|'+opt_tmp);
			});
			var filter_vals = '';
			$(".filter_val").each(function(){
				if ($(this).has("input[name='filter_value']")) {
					var $input = $(this).find("input[name='filter_value']");
					if ($input.attr('type') == 'radio') {
						filter_vals += (filter_vals == '' ? $(this)
						.find("input[name='filter_value']:checked").val() : 
							'|'+$(this).find("input[name='filter_value']:checked").val());
					} else {
						filter_vals += (filter_vals == '' ? $input.val() : '|'+$input.val());
					}
				}
			});
			var relationship = '';
			$("select[name='relationship']").each(function(){
				relationship += (relationship == '' ? $(this).val() : '|'+$(this).val());
			});
			var query = "s_fields="+select_fields+"&s_opts="+select_opts+"&s_vals="+filter_vals+"&s_rels="+relationship;
			if ($.trim($("input[name='k']").val()) != '') {
				query += "&k="+$.trim($("input[name='k']").val());
			}
			return query;
		}
};
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
$(function(){
	$(".btn-return").attr("href", 'javascript:;');
	$(".btn-return").click(function(){history.go(-1);});
	$("html").click(function(e){
		e = window.event || e;
		if (!$(e.srcElement || e.target).is('#RobPub1Editor *,#table1 *')) {
			$("#RobPub1Editor").hide();
			$('.RobPub1BlueBorder').removeClass('RobPub1BlueBorder');
		}
	});
	$(".alert button.close").click(function() {
		$(this).parents('.alert').hide();
	});
	$(".alert").click(function() {
		$(this).hide();
	});
	$("table.table tbody tr").mouseover(function() {
		if (!$("td input[type='checkbox']",$(this)).is(":checked")) {
			$(this).css('background-color', '#E2E9EA');
		}
	});
	$("table.table tbody tr").mouseout(function() {
		if ($("td input[type='checkbox']",$(this)).is(":checked")) {
			$(this).css('background-color', '#f0f0f0');
		} else {
			$(this).css('background-color', '#fff');
		}
	});
	$(".box-header .btn-edit").click(function() {
		if ($(this).attr('editMult')) {
			if ($("input[type='checkbox'][name='select']:checked").size() == 0) {
				alert('请先选择一个条目');
				return false;
			}
			var id = '';
			$("input[type='checkbox'][name='select']:checked").each(function() {
				id += (id == '') ? $(this).val() : ','+$(this).val();
			});
		} else {
			if ($("input[type='checkbox'][name='select']:checked").size() == 0) {
				alert('请先选择一个条目');
				return false;
			} else if ($("input[type='checkbox'][name='select']:checked").size() > 1) {
				alert('一次只能选择一个条目进行编辑');
				return false;
			}
			var id = $("input[type='checkbox'][name='select']:checked").val();
		}
		
		var url = $(this).attr('_href')+id;
		$(this).attr('href', url);
	});
//	$("input[type='reset']").click(function(){
//		history.go(-1);
//	});
	$("input[type='checkbox'][name='select_all']").click(
		function() {
			if ($(this).is(':checked')) {
				$("input[type='checkbox'][name='select']").prop('checked', this.checked);
			} else {
				$("input[type='checkbox'][name='select']").removeAttr('checked');
			}
			chkBg();
		}
	);
	function chkBg() {
		var ids = '';
		$("input[type='checkbox'][name='select']").each(function(){
			if ($(this).is(':checked')) {
				$(this).parents('tr').css('background-color', '#f0f0f0');
				ids += ','+$(this).val();
			} else {
				$(this).parents('tr').css('background-color', '#fff');
			}
		});
		setcookie('memory_id_list', ids, 24*3600);
	}
	$("table.table tr.row").each(function(){
		$("td,th", $(this)).eq(0).click(function(e){
			e = window.event || e;
			var obj = e.srcElement || e.target;
			if ($(obj).is('td') || $(obj).is('th')) {
				$(this).find("input[type='checkbox']").trigger('click');
			}
			chkBg();
		});
	});
	$("table.table tr.row").each(function(){
		$("td,th", $(this)).eq(1).click(function(e){
			e = window.event || e;
			var obj = e.srcElement || e.target;
			if ($(obj).is('td') || $(obj).is('th')) {
				$(this).prev().find("input[type='checkbox']").trigger('click');
			}
			chkBg();
		});
	});
	$(".input-group input").focusin(function(){
		if ($(this).parents('.input-group').hasClass('error')) {
			$(this).parents('.input-group').removeClass('error');//alert($(this).next().html());
			$(this).nextAll('.help-inline').remove();
		}
	});
	$(".btn-submit").click(function(){
		$("input[type='submit'][class='button-element']").trigger('click');
	});
	$(".btn-reset").click(function(){
		$("input[type='reset'][class='button-element']").trigger('click');
	});
	$("input[name='k'], input[name='name']").focusout(function() {
		if ($(this).val() == '') {
			$(this).next("button[type='submit']").trigger('click');
		}
	});
});
(function($){
    var getJSON  = this.getJSON = function( url, data, callback ){

          if ( $.isFunction( data ) ) {
                callback = data;
                data = undefined;
          }
          var callBack = callback || false;
          return $.ajax({
                type: 'GET',
                url: url,
                data: data,
                success: function(data){
                      if( data.code ==-412 ) {alert('err');
//                            systemMsg.display('登录超时,请重新登录', 1000, function(){
//                                  location.href = '/';
//                                  return false;
//                            });

                            return false;
                      }else if( data.code ==-401 ) {alert('e');
//							 systemMsg.display('未分配此权限，请联系管理员', 1000, function(){
//                                  location.href = '/';
//                                  return false;
//                            });

                            return false;
						}
                     
                      if( callBack ) return callBack(data);
                },
                dataType: 'JSON'
          });
    }
})(jQuery);
(function(){
	var Editable = this.Editable = function(options, table) {
		this.options = options;
		this.table = table;
		this.init();
		this.updateData();
	};
	Editable.prototype = {
		init:function() {
			var obj = $('#'+this.options.tableID);
            var colHead = obj.children().find('th[editable]');  //Find object by attr editable
            if(colHead.size() <=0 ) return false;

            var that = this;
            
            colHead.each(function(){
            	var key = $(this).attr('key');     //attr key

                //Find elements under current value of key
                var objEdit = that.table.children().find('td[key="'+key+'"]');
                if(objEdit.size() <=0)  return false;


                //if there's no any args , then return;
                if(that.options.editableParam[key] ==undefined ) return false;

                //Also need setup element's type and group options
                //Reset Element value
//                objEdit.each(function(){
//                      var type = that.options.editableParam[key]['type'];
//                      if(type =='text' ) return;
//
//                      var oriHTML = $(this).html();
//                      var newHtml = that.options.editableParam[key]['group'][oriHTML];
//                      if(type=='img') $(this).html('<img src="'+newHtml+'" />');
//                      else $(this).html(newHtml);
//                });
              //Temp Editor
                var edt =that.createEditor();

                if($(this).attr('editable') == 'false') return;
                //Bind Click Event;
                //Click Editable Element to do edit
                objEdit.each(function(){
                      $(this).unbind('click').unbind('dblclick');
                      var parent = $(this).parent();
                      var options  = that.options.editableParam;
                      var myOptions = that.options.editableParam[key];
                      var type = myOptions['type'];

                      if(type == 'img'){
//                            var obj = $(this).find('img');
//                            if(obj.size() <= 0) return false;
//
//
//
//                            obj.click(function(){
//
//                                  var finder = '',valNew='';
//                                  var objImg = $(this);
//                                  $.each(myOptions['group'],function(i,v){
//                                        if(objImg.attr('src') != v) {
//                                              finder =v;
//                                              valNew =i;
//                                        }
//                                  });
//
//                                  $(this).attr('src',finder);
//
//                                  var param = "field="+key+
//                                  '&value='+encodeURI(valNew,'UTF-8')+'&primary_value='
//                                  +encodeURI(parent.attr('primary_value'),'UTF-8');
//
//                                  var table = myOptions.table||options.table;
//
//                                  param += '&primary_key='+options.primary_key+'&table='+table;
//
//                                  getJSON(options.postUrl,param,function(json){
//                                	  if (json.data == -1) {
//                                   		  systemMsg.display(json.msg,6000);
//                                   	  }
//                                  });
//                                  return false;
//                            });
                      }else{
                            $(this).click(function(){
                                  //Remove red border of  last marked element
                                  $('.RobPub1BlueBorder').removeClass('RobPub1BlueBorder');

                                  //Mark with red border;
                                  $(this).addClass('RobPub1BlueBorder');
                            }).click(function(){
                                  //Clear Tipper
                                  $('#RobPub1EditTipper').html('').hide();
//                                  //Set this primary value
//                                  //If already setup by options group,then use it ,by default
                                  $('#RobPub1HidPV').val( parent.attr('primary_value'));
                                  if(myOptions['field'] != undefined)
                                        $('#RobPub1HidPK').val(myOptions['field']);
                                  //Else use current column name
                                  else $('#RobPub1HidPK').val(key);

                                  //Mutiplt table support
                                  if(myOptions['table'] != undefined)
                                        $('#RobPub1HidTBL').val(myOptions['table']);
                                  else $('#RobPub1HidTBL').val(options['table']);
                                  //Reset tempeditor's css style and clear inner html
//                                  edt.children('#RobPub1EdtContent').html('').end()
//                                  .css('top',$(this).offset().top+30).css('left',$(this).offset().left+3);
                                  var w = parseInt($(this).width() + 16);
                                  var h = parseInt($(this).height() + 16);
                                  edt.children('#RobPub1EdtContent').html('').end()
                                  .css('top',$(this).offset().top).css('left',$(this).offset().left);

//                                  var children = $(this).children('.rec');
                                  //Get children innerhtml
                                  var oriObj = $(this);
                                  var oriHTML = $(this).html();

                                  //Diffrent type,diffrent way to edit
                                  //Support: text,radio,select
                                  switch(myOptions['type']){

                                        //Start of radio
                                        case 'radio':

                                              $.each(myOptions['group'],function(i,v){
                                                    if( oriHTML == v ) checked='checked';
                                                    else checked='';
                                                    edt.children('#RobPub1EdtContent').
                                                    append('<input '+checked+' type="radio" val="'+v+'" name="'+key+'" value="'+i+'">'+v);
                                              });
                                              edt.children('#RobPub1EdtContent').width(w+'px').height(h+'px');
                                              break;
                                        //End of radio

                                        //type of text
                                        case 'text':
                                        	  if (oriObj.find('a').size()>0) {
                                        		  oriHTML = oriObj.find('a').html(); 
                                        	  }
                                              var obj = $('<input autocomplete="off" type="text" value="'+oriHTML+'" name="'+key+'" />');
                                              obj.width(w+'px').height(h+'px');
                                              edt.children('#RobPub1EdtContent').html(obj);
                                              edt.children('input[type="text"]').focus();
                                              break;
                                        //type of select
                                        case 'select':
                                              var obj = $('<select name="'+key+'"></select>');

                                              getJSON(myOptions['loadUrl'],function(data){

                                                    $.each(data,function(i,v){
                                                          if(oriHTML == v['name'])
                                                                obj.append('<option selected value="'+v['value']+'">'+v['name']+'</option>');
                                                          else
                                                                obj.append('<option value="'+v['value']+'">'+v['name']+'</option>');
                                                    });
                                              });
                                              edt.children('#RobPub1EdtContent').html(obj);
                                              break;
                                  }
                                  edt.hide().fadeIn();

                            });
                      }
                });

            });
		},
        createEditor:function(){
            var edt = $('<div id="RobPub1Editor" style="z-Index:99999"></div>');
            var RobPub1EdtContent = $('<div id="RobPub1EdtContent"></div>');
            var RobPub1EditTipper = $('<div id="RobPub1EditTipper"></div>');
            var btnEdt = $('<input type="button" id="RobPub1ColEditor" value="更新"/>');
            var btnCalcel = $('<input type="button" value="取消"/>');
            btnCalcel.click(function(){
                  edt.hide();
                  $('.RobPub1BlueBorder').removeClass('RobPub1BlueBorder');
            });
            var btnO = $('<span stle="float:left;"></span>');
            edt.append(RobPub1EdtContent).append(btnO.append(btnEdt).append(btnCalcel)).append(RobPub1EditTipper);
            
//            var warp1 = $('<div></div>');
            var warp1 = $('<span></span>');
            warp1.append('<input type="hidden" id="RobPub1HidPV" value="" />');
            warp1.append('<input type="hidden" id="RobPub1HidPK" value="" />');
            warp1.append('<input type="hidden" id="RobPub1HidTBL" value="" />');
            warp1.appendTo(edt);
            if($('#RobPub1Editor').size() !=0) return $('#RobPub1Editor');
            edt.appendTo(  $('body') );
            return edt;
      },
      updateData:function(){
    	  var that = this;
          $('#RobPub1ColEditor').click(function(){
                var s = $('#RobPub1EdtContent').find(':input');
                var value = '';
                if(s.size() !=1){
                      value=$('#RobPub1EdtContent').find(':checked').val();
                      var html = $('#RobPub1EdtContent').find(':checked').attr('val');
                      if(s.attr('vt') != undefined){
                            html = '<img src="'+html+'" />';
                      }
                      $('.RobPub1BlueBorder').html( html ).removeClass('RobPub1BlueBorder');
                } else {
                      if(s.is('select')){
                            value = s.val();
                            $('.RobPub1BlueBorder').html(s.children('option:selected').html()).removeClass('RobPub1BlueBorder');
                      }else{

                            if(that.options.editableParam[s.attr('name')]['reg'] !=undefined){

                                  var reg = that.options.editableParam[s.attr('name')]['reg'];

                                  if(! reg.test(s.val())){
                                        s.addClass('errorMsg');
                                        $('#RobPub1EditTipper').html(that.options.editableParam[s.attr('name')]['tip'] || '您输入的内容不符合规范!').show();
                                        s.focus();
                                        return false;
                                  }else {
                                        s.removeClass('errorMsg');
                                        $('#RobPub1EditTipper').html('').hide();
                                  }
                            }else{
                                  if(! s.val() ){
                                        s.addClass('errorMsg');
                                        $('#RobPub1EditTipper').html('您不能输入空文本!').show();
                                        s.focus();
                                        return false;
                                  }else{
                                        s.removeClass('errorMsg');
                                        $('#RobPub1EditTipper').html('').hide();
                                  }
                            }

//                            $('.RobPub1BlueBorder').html(s.val()).removeClass('RobPub1BlueBorder');
                            value = s.val();
                      }

                }
                var table = $('#RobPub1HidTBL').val() || that.options.editableParam.table;
                var param = "field="+$('#RobPub1HidPK').val()+'&value='+encodeURI(value,'UTF-8')+'&primary_value='+encodeURI($('#RobPub1HidPV').val(),'URTF-8');
                param += '&primary_key='+that.options.editableParam.primary_key+'&table='+table;
                getJSON(that.options.editableParam.postUrl,param,function(json){
                	if (json.code == 1) {
                		$('.RobPub1BlueBorder').removeClass('RobPub1BlueBorder');
                		alert(json.msg);
                	} else {
                		$('.RobPub1BlueBorder').html(s.val()).removeClass('RobPub1BlueBorder');
                	}
//                	if (json.data == -1) {
//                 		systemMsg.display(json.msg,6000);
//                 	}
                });
                $('#RobPub1Editor').hide();
          });
      }
	}
})();
(function(){
	var Area = this.Area = function(options) {
		this.options = options;
		this.init();
	};
	Area.prototype = {
		init : function() {
			var that = this;
			$(this.options.prov).bind('change', this.baseBind($(this.options.prov), $(this.options.city), '城市'));
			$(this.options.city).bind('change', this.baseBind($(this.options.city), $(this.options.dist), '区域'));
		},
		baseBind : function($obj, $sub, areaTitle) {
			$obj.change(function() {
				$v = $(this).val();
				$.ajax({
					type: "POST",
					url : "/ajax/region?p="+$v,
					dataType :"json",
					success: function(s){
						var html = '<option value="" >===请选择'+areaTitle+'===</option>';
						for(var o in s) {
							html += "<option value='"+s[o].region_id+"'>"+s[o].region_name+"</option>";
						}
						$sub.html(html);
					}
				});
			});
		}
	};
})();