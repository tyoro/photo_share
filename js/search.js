$(function(){

var search_keyword = null;
var search_books = null;
var search_draw_db = null;
var search_draw_num = 0;

var search_nums = new Array();
var id_count =0;


//$('#search-submit').click(function(){
$('form#search-form').submit(function(){
	search_keyword = $('#search-keyword').val();

	if( search_nums[search_keyword]&& search_nums[search_keyword]['end'] ){
		$('#search_msg').text('search end.');
		return false;
	}

	if( search_nums[search_keyword] ){
		search_nums[search_keyword]['count']++;
	}else{
		search_nums[search_keyword] = new Array();
		search_nums[search_keyword]['id'] = ++id_count;
		search_nums[search_keyword]['count'] = 1;
		search_nums[search_keyword]['type'] = 'db';
		search_nums[search_keyword]['end'] = false;
	}

	$('#search_msg').text('');
	$('#search_result').prepend('<div id="'+search_nums[search_keyword]['id']+'_'+search_nums[search_keyword]['type']+'_'+search_nums[search_keyword]['count']+'_result" class="result_set" ><div class="result_set_head"><img src="./img/ajax-loader.gif"/>loading...</div><div class="result_set_body clearfix"></div>');
	search_books = new Array();
	search_draw_num = 0;
	search_draw_db = null;
	if( search_keyword.length ){
		$.ajax({
		 type: 'POST'
		 ,url: './api/search'
		 ,dataType: 'json'
		 ,data: 'keyword='+search_keyword+'&page='+search_nums[search_keyword]['count']+'&type='+search_nums[search_keyword]['type']
		 ,success: function(data,textStatus,XMLHttpRequest){
		 	if( data.status ){
				drawBookList(data.param);
			}else{
				drawErrorSearch();
			}

		 }
		 ,error: function(XMLHttpRequest, textStatus, errorThrown){
			console.log(XMLHttpRequest.responseText);
		 	alert('error:'+textStatus+"\nreturn:"+XMLHttpRequest.responseText);
		 }
		});
	}
	return false;
});

function drawBookList(data){
	console.log( data );
	Array.prototype.push.apply( search_books,data.books );

	if( data.pager.atLastPage ){
		if( search_nums[data.word]['type'] == 'amazon' ){
			search_nums[data.word]['end'] = true;
		}else{
			search_nums[data.word]['type'] = 'amazon';
			search_nums[data.word]['count'] = 0;
		}
	}
	
	onOverlay(
	function(){
		$('#layer_info').show();
		if( data.type == 'db' ){
			$('#search_msg').text('in site searched.');
		}else{
			$('#search_msg').text('in amazon searched.');
		}
		result_set = $('div#'+search_nums[data.word]['id']+'_'+data.type+'_'+data.page+'_result div');
		result_set_head = result_set.eq(0);
		result_set_body = result_set.eq(1);
		result_set_head.text( '"'+data.word+'" is '+data.type+' search '+data.page+' page result.' ).click(
			function( e ){
				body = $(e.target.nextSibling);
				console.log(body.css('display'));
				if( body.css('display') == 'none' ){
					body.css('display','block');
				}else{
					body.css('display','none');
				}
			}
		);
		for( i=search_draw_num; i<9+search_draw_num && search_books.length > i ; i++){
			result_set_body.append( '<div class="book">'
				+'<a href="./book/'+search_books[i].isbn+'"><img src="'+search_books[i].image_url+'"/><br/>'
				+''+search_books[i].title+'</a><br/>'
				+'íò:'+search_books[i].author+'<br/>'
				+'Åè'+search_books[i].price+'<br/>'
				+'ISBN:'+search_books[i].isbn+'<br/>'
				+'<button onclick="javascript:toPost(\''+search_books[i].isbn+'\');">post</button>'
				+'</div><br/>'  );
		}
		search_draw_num = i;
		result_set_body.show();
	
		var pager_str = '';
		//pager
		if( data.pager.atFirstPage ){
			pager_str += '&lt;&lt; ';
		}else{
			pager_str += '<a href="#" onclick="">&lt;&lt;</a> '
		}

		if( data.pager.atLastPage ){
			pager_str += ' &gt;&gt;';
		}else{
			pager_str += ' <a href="#" onclick="">&gt;&gt;</a>'
		}

	});
}

window.onOverlay = function( func ){
	//var height = $('body:first').height();
	var height = document.documentElement.clientHeight;
	var width = document.documentElement.clientWidth;
	$('#overlay').height(height).width(width).show().fadeTo(500, 0.8, func ).click(
		function(e){
			if( e.target.id == 'overlay' ){
				$('#overlay').hide().css('opacity',0);
			}
		}
	);
}

function drawErrorSearch(){
	alert('kensaku ni shippai simasita');
}

});
