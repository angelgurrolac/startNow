$(document).ready(main);
 
var contador = 1;
 
function main(){
	$('#banner').css('height',( $(window).height() * .3 )+ "px");

	$('.menu_bar').click(function(){
		// $('nav').toggle(); 
 
		if(contador == 1){
			$('nav').animate({
				left: '0'
			});
			contador = 0;
		} else {
			contador = 1;
			$('nav').animate({
				left: '-100%'
			});
		}
 
	});

 	$('.submenu').click(function(){
		$(this).children('.children').slideToggle();
	});
	$(document).scroll(function() {
		if (contador ==	 0) {
			$('nav').animate({
				left: '-100%'
			});
			contador = 1;
		}
	});
}

