var $ =jQuery.noConflict();
$.extend({
    keys: function(obj){
        var a = [];
        $.each(obj, function(k){
          a.push(k);
        });
        return a;
    }
});


var linkList = [];
$(document).ready(function() {

	get_storage_value_onload();
	var index = 1;
  if(linkList.length){
	   var max_index = Math.max.apply(Math,linkList.map(function(l){return l.index;}));
     index = max_index + 1;
  }

  show_Related_list();

  var $language_Selection = $('input[type="radio"][name="language_site_related_list"]');
    $language_Selection.on('change', function() {
    $('.' + this.className).prop('checked', this.checked);
    show_Related_list();
  });

	$('#related_add_button').click(function(){
		var type = $('#related_content_type').val();
    var is_link_valid = [];
		url = {};
		label = {};
		$("input#related_content_label").each(function(){
				var lang = $(this).attr("lang");
				label[lang] = $(this).val();
		});

    $("input#related_content_url").each(function(){
				var lang = $(this).attr("lang");
				url[lang] = $(this).val();
        is_link_valid.push(is_url_valid($(this).val()));
		});

		if(($.keys(url).length > 0) && (type !== "")) {
			if( $.inArray( false, is_link_valid)  == -1 ) {
				push_item_to_json(type, url, label, index);
				add_item_to_list_container(type, url, label, index);
				clear_related_fields();
				index++;
			}else {
				$('.related_error').html("<p>URL is not valid, please check it.</p>");
			}
		}else {
			$('.related_error').html("<p>URL and TYPE are required.</p>");
		}

    return false;
	});

	$('#related_list_box').on('click', '#delete_item', function()
	{
			var item_index = $(this).attr("index");
	  	$('.item-'+item_index).remove();
	  	remove_item_from_object_with_index(item_index);
	});

	$('#related_list_box').on('click', '#edit_item', function()
	{
			var edit_item_index = $(this).attr("index");
			var item_index = get_item_from_object_with_index(edit_item_index);
			var edit_label = linkList[item_index].label;
			var edit_url = linkList[item_index].url;
				$.each(edit_label, function(key){
					$("input[id='related_content_label'][lang='"+key+"']").val(edit_label[key]);
				});
        $.each(edit_url, function(key){
					$("input[id='related_content_url'][lang='"+key+"']").val(edit_url[key]);
				});

				$('#related_content_type').val(linkList[item_index].type);

				$('.related_content_form').append("<input type='hidden' class='update_item' id='item' value='"+edit_item_index+"' name='item_index' /> ");
				$('#related_update_button').show();
				$('#related_cancel_button').show();
				$('#related_add_button').hide();
	});

	$('#related_update_button').click(function(){
		var update_index = $('.update_item').val();
		var update_type = $('#related_content_type').val();
		var is_update_link_valid = [];

		update_label = {};
		$("input#related_content_label").each(function(){
				var lang = $(this).attr("lang");
				update_label[lang] = $(this).val();
		});

    update_url = {};
    $("input#related_content_url").each(function(){
        var lang = $(this).attr("lang");
        update_url[lang] = $(this).val();
        is_update_link_valid.push(is_url_valid($(this).val()));
    });

    if(($.keys(update_url).length > 0) && (update_type !== "")) {
			if( $.inArray( false, is_update_link_valid)  == -1 ) {
				push_update_item_to_json(update_type, update_url, update_label, update_index);
				update_item_to_list_container(update_type, update_url, update_label, update_index);
			}else {
				$('.related_error').html("<p>URL is not valid, please check it.</p>");
			}
		}else {
			$('.related_error').html("<p>URL and TYPE are required.</p>");
		}

		clear_related_fields();
		$('#related_update_button').hide();
		$('#related_cancel_button').hide();
		$('#related_add_button').show();

		return false;
	});

	$('#related_cancel_button').click(function(){
		clear_related_fields();
		$('#related_update_button').hide();
		$('#related_cancel_button').hide();
		$('#related_add_button').show();
	});

  var current_related_list, pre;
  $(".related_list").sortable({
      start:function(event, ui){
          pre = ui.item.index();
      },
      stop: function(event, ui) {
          current_related_list = $(this).attr('id');
          post = ui.item.index();
          other = (current_related_list == 'related_list') ? 'related_list_localize' : 'related_list';
          //Use insertBefore if moving UP, or insertAfter if moving DOWN
          if (post > pre) {
              $('#'+other+ ' p.item:eq(' +pre+ ')').insertAfter('#'+other+ ' p.item:eq(' +post+ ')');
          }else{
              $('#'+other+ ' p.item:eq(' +pre+ ')').insertBefore('#'+other+ ' p.item:eq(' +post+ ')');
          }
          $($("#"+current_related_list+ " p.item").get()).each(function (index) {
            var item_index = $(this).children(" #edit_item").attr('index');
            chnage_related_item_order(item_index, index);
         });
      }
  }).disableSelection();

}); //$(document).ready

function chnage_related_item_order(item_index, order) {
	var id = get_item_from_object_with_index(item_index);
	linkList[id].order = order;
	update_form_value();
}

function show_Related_list(){
  var $forms = $('.language_settings');
  $forms.hide();
  var selected = $('input[type="radio"][name=language_site_related_list]').filter(':checked').val();
  $('.language-' + selected).show();
}

function push_item_to_json(type, url, label, item_index){
	item = {};
	item ["index"] = item_index;
	item ["type"] = type;
	item ["url"] = url;
	item ["label"] = label;
	item ["order"] = item_index;
	linkList.push(item);
	update_form_value();
}

function push_update_item_to_json(type, url, label, item_index){
	var id = get_item_from_object_with_index(item_index);
	linkList[id].label = label;
	linkList[id].type = type;
	linkList[id].url = url;
	update_form_value();
}

function add_item_to_list_container(type, url, label, item_index){
	var hyperlink_text = get_hyperlink_lable(label, url);
  var hyperlink_url = get_hyperlink_url(url);

	var link_label_en = "<a href='"+hyperlink_url['en']+"' target='_blank'>" + hyperlink_text['en'] + "</a> <span class='related-type'>("+type+")</span>";

	var edit_item = ' <span id="edit_item" index ="'+item_index+'" href="#"><span class="dashicons dashicons-edit"></span></span>';
	var delete_item = ' <span id="delete_item" index ="'+item_index+'" href="#"><span class="dashicons dashicons-no"></span></span>';
	$("#related_list").append("<p class='item item-"+item_index+"'>" + link_label_en + edit_item + delete_item + "</p>");
	if($("#related_list_localize").length){
		var link_label_localize = "<a href='"+hyperlink_url['localize']+"' target='_blank'>" + hyperlink_text['localize'] + "</a> <span class='related-type'>("+type+")</span>";
		$("#related_list_localize").append("<p class='item item-"+item_index+"'>" + link_label_localize + edit_item + delete_item +"</p>");
	}
}

function update_item_to_list_container(type, url, label, item_index){
	var hyperlink_text = get_hyperlink_lable(label, url);
  var hyperlink_url = get_hyperlink_url(url);
	$("#related_list .item-"+item_index + " a").attr("href", hyperlink_url['en']);
	$("#related_list .item-"+item_index + " a").text(hyperlink_text['en']);
	$("#related_list .item-"+item_index + " .related-type").text("("+type+")");
	if($("#related_list_multiple_box").has('#related_list_localize')){
		$("#related_list_localize .item-"+item_index + " a").text(hyperlink_text['localize']);
		$("#related_list_localize .item-"+item_index + " a").attr("href", hyperlink_url['localize']);
		$("#related_list_localize .item-"+item_index + " .related-type").text("("+type+")");
	}
}

function remove_item_from_object_with_index(item_index){
	var link_index = get_item_from_object_with_index(item_index);
	if (link_index){
    linkList.splice(link_index,1);
		update_form_value();
    return;
  }
}
function get_hyperlink_url (url, lang) {
  lang = (typeof lang !== 'undefined') ?  lang : null;
	var url_en, url_localize;
  if (typeof url !== "string"){
    if(!$.isEmptyObject(url)){
  		$.each(url, function(key){
  			if(key == "en"){
  				url_en = url[key];
  			}else{
  				url_localize = url[key];
  			}
  		});
  }
	}else {
    url_en = url;
    url_localize = url;
  }

	hyperlink_url = {};
	hyperlink_url['en'] = url_en;
	hyperlink_url['localize'] = url_localize;

  if(lang){
    return hyperlink_url[lang];
  }
	return hyperlink_url;
}

function get_hyperlink_lable (label, url) {
	var hyperlink_text_en;
	var hyperlink_text_localize;
  var hyperlink_url = get_hyperlink_url(url);

	if(!$.isEmptyObject(label)){
		$.each(label, function(key){
			if(key == "en"){
				hyperlink_text_en = label[key];
			}else{
				hyperlink_text_localize = label[key];
			}
		});
	}

	if(!hyperlink_text_en){
		hyperlink_text_en = hyperlink_url['en'];
	}
	if(!hyperlink_text_localize){
		hyperlink_text_localize = hyperlink_url['localize'];;
	}
	hyperlink_text = {};
	hyperlink_text['en'] = hyperlink_text_en;
	hyperlink_text['localize'] = hyperlink_text_localize;

	return hyperlink_text;
}

function get_item_from_object_with_index(item_index){
  for (var id in linkList){
    if (linkList[id]["index"] == item_index){
      return id;
    }
  }
  return null;
}

function get_storage_value_onload(){
  var linkList_json = $("#related_content").val();
  if (linkList_json) {
		linkList = JSON.parse(linkList_json);
    console.log(linkList_json);
		linkList.sort(function(a, b) {
		    return parseFloat(a.order) - parseFloat(b.order);
		});
		set_related_list_value_onload();
	}
	update_form_value();
}

function set_related_list_value_onload(){
	if(linkList){
	  for (var id in linkList){
	    var link_item = linkList[id];
			var label = link_item["label"];
      var hyperlink_text = link_item["url"];
			if( $.keys(label).length > 0 ){
				hyperlink_text = label;
			}
			//add_item_to_list_container(link_item["type"], link_item["url"], hyperlink_text, id);
			add_item_to_list_container(link_item["type"], link_item["url"], hyperlink_text, link_item["index"]);
	  }
	}
}

function update_form_value(){
	var linkList_json = JSON.stringify(linkList);
	if(linkList.length > 0) {
		$("#related_list_multiple_box").show();
		$("#related_content").val(linkList_json);
	}else {
		$("#related_list_multiple_box").hide();
		$("#related_content").val("");
	}
}

function clear_related_fields(){
	$('.related_error').text('');
	$('#related_content_type').val('');
	$('input#related_content_label').each(function(){
		$(this).val('');
	});
  $('input#related_content_url').each(function(){
		$(this).val('');
	});
	$('.update_item').remove();
}

function is_url_valid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}
