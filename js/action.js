$(window).load(function(){
	$('.preloader').fadeOut();
});

$(window).ready(function() {
	ajax_call('../php/connect.php',0,'.depat');
	$('.autocomplete').empty();	
	home_out();	
});	

$('body').on('focusout', 'input', function(){
	if($('.autocomplete-block' + ':hover').length)
		return;
	$('.autocomplete').css({'display': 'none'});
});

$('body').on('click', '.ulblock', function(){
	var path = $(this).parent().children('ul');
	$(path).slideToggle('slow');
	$(this).children().toggleClass('open');
});

$('body').on('click', '.pointer-click', function(){
	var data = $(this).data('path');
	$('.pointer').removeClass('check');;
	$(this).addClass('check');
	if ($(this).children('span').hasClass('home')) 
		home_out();
	else
		ajax_call('../php/name.php',data,'.table', 1);
});		

$('body').on('click', '.person', function(){
	$('.autocomplete').css({'display': 'none'});
	var data = $(this).data('path');
	//ajax_call('../php/data.php',data,'.info');
	$(this).toggleClass('open');	
	$(this).children('.info').slideToggle('slow');
});	

$('.search-wrapper').bind('submit', function(e){
	e.preventDefault();
	var x = document.getElementById('search').value;
	ajax_call('../php/name.php',x,'.table',2);
	$('.autocomplete').empty();
});

$('input').on('input', function(){
	$('.autocomplete').css({'display': ''});
	var x = this.value; 
	if (x != '')
		ajax_call('../php/datalist.php',x,'.autocomplete');
	else
		$('.autocomplete').empty();
});
	
$('body').on('click', '.autocomplete-block', function(){
	var x = this.innerHTML;
	ajax_call('../php/name.php',x,'.table',2);
	$('.autocomplete').empty();
});		
	
function ajax_call(php, path, out, search_id) {	
	$.ajax({
		type: 'GET',
		url: php,
		data: {
			path: path,
			id: search_id
		},
		success: function(data) {
			$(out).html(data);
		}
	});
};

function home_out(){
		$('.table,.text').html('');
		
		var json = $.getJSON('js/people.json',function(data){
			var item = [];
			
			$.each(data.people, function(key, value){
				item.push('<td></td><td>' + value.name + '</td><td>' 
							+ value.depat + '</td><td>' 
							+ value.phone + '</td>');
				$('<tr/>',{html: item[key]}).appendTo('.table');
			});
	});	
};