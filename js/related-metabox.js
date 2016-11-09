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
	var index = linkList.length;
	$('#related_add_button').click(function(){
		var type = $('#related_content_type').val();
		var url = $('#related_content_url').val();
		label = {};
		$("input#related_content_label").each(function(){
				var lang = $(this).attr("lang");
				label[lang] = $(this).val();
		});

		if(url !== "" && type !== "") {
			if( is_url_valid(url) ) {
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

	$('#related_list_box').on('click', '#delete_item', function() {
			var item_index = $(this).attr("index");
	  	$('.item-'+item_index).remove();
	  	remove_item_from_object_with_index(item_index);
	});

}); //$(document).ready

function push_item_to_json(type, url, label, item_index){
	item = {};
	item ["index"] = item_index;
	item ["type"] = type;
	item ["url"] = url;
	item ["label"] = label;
	linkList.push(item);
	update_form_value();
}

function add_item_to_list_container(type, url, label, item_index){
	var hyperlink_text_en;
	var hyperlink_text_localize;

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
		hyperlink_text_en = url;
	}
	if(!hyperlink_text_localize){
		hyperlink_text_localize = url;
	}

	var link_label_en = "<a href='"+url+"' target='_blank'>" + hyperlink_text_en + "</a> ("+type+")";
	var link_label_localize = "<a href='"+url+"' target='_blank'>" + hyperlink_text_localize + "</a> ("+type+")";
	var delete_item = ' <span id="delete_item" index ="'+item_index+'" href="#">Delete</span>';
	$("#related_list").append("<p class='item item-"+item_index+"'>" + link_label_en + delete_item +"</p>");
	$("#related_list_localize").append("<p class='item item-"+item_index+"'>" + link_label_localize + delete_item +"</p>");
}

function remove_item_from_object_with_index(item_index){
	var link_index = get_item_from_object_with_index(item_index);
	if (link_index){
    linkList.splice(link_index,1);
		update_form_value();
    return;
  }
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
			if( link_item["index"] === undefined ) {
					link_item["index"] = id;
			}
			add_item_to_list_container(link_item["type"], link_item["url"], hyperlink_text, id);
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
	$('#related_content_url').val('');
	$('#related_content_type').val('');
	$('input#related_content_label').each(function(){
		$(this).val('');
	});
}

function is_url_valid(url) {
    return /^(https?|s?ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test(url);
}
