// Portfolio Isotope Filtering

if($('#isotope-filtering').length && jQuery()) {

$(document).ready(function(){
var $container = $('#isotope-filtering');
$container.isotope({
	filter: '*',
	animationOptions: {
	duration: 150,
	easing: 'linear',
	queue: false,
	resizable: false,
	masonry: { columnWidth: $container.width() / 4 }
   }
});

$(window).smartresize(function(){
	$container.isotope({
	masonry: { columnWidth: $container.width() / 4 }
  });
});

$('#filtering a').click(function(){
	var selector = $(this).attr('data-filter');
	$container.isotope({
	filter: selector,
  });
  return false;
});

});
  }



// Masonry Blog Posts

if($('#masonry-blog-posts-container').length && jQuery()) {
$(window).load(function() {
$('#masonry-blog-posts-container').masonry({
  itemSelector: '.span4',
  isAnimated: true,

  columnWidth: function( containerWidth ) {
    return containerWidth / 2;
  }
});
});
}

// Main Page Slider

$(function() {

	var Page = (function() {

	var $navArrows = $( '#nav-arrows' ),
	$nav = $( '#navi-dots > span' ),
	slitslider = $( '#slider' ).slitslider( {
	onBeforeChange : function( slide, pos ) {

	$nav.removeClass( 'nav-dot-current' );
	$nav.eq( pos ).addClass( 'nav-dot-current' );

	}
	} ),

	init = function() {

	initEvents();

	},
	initEvents = function() {

	// add navigation events
	$navArrows.children( ':last' ).on( 'click', function() {

	slitslider.next();
	return false;

	} );

	$navArrows.children( ':first' ).on( 'click', function() {

	slitslider.previous();
	return false;

} );

	$nav.each( function( i ) {

	$( this ).on( 'click', function( event ) {

	var $dot = $( this );

	if( !slitslider.isActive() ) {

	$nav.removeClass( 'nav-dot-current' );
	$dot.addClass( 'nav-dot-current' );

}

	slitslider.jump( i + 1 );
	return false;

} );

} );

};

return { init : init };

})();

Page.init();


});


//Subscribe Form

$(function() {

  if($('#subscribe-form').length && jQuery()) {

    $('form#subscribe-form').submit(function() {

      $('form#subscribe-form .error').remove();
        var hasError = false;

        $('.required').each(function() {
          if(jQuery.trim($(this).val()) === '') {
            var labelText = $(this).prev('label').text();
            $(this).parent().append('<div class="error">Please enter your Email'+labelText+'</div>');
            $(this).addClass('inputError');
            hasError = true;
          } else if($(this).hasClass('email')) {
            var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
            if(!emailReg.test(jQuery.trim($(this).val()))) {
            var labelText = $(this).prev('label').text();
            $(this).parent().append('<div class="error">Please enter a valid email address'+labelText+'</div>');
            $(this).addClass('inputError');
            hasError = true;
            }
          }
        });

        if(!hasError) {
          $('form#subscribe-form input.submit').fadeOut('normal', function() {
            $(this).parent().append('');
          });
          var formInput = $(this).serialize();
          $.post($(this).attr('action'),formInput, function(data){
            $('form#subscribe-form').slideUp("fast", function() {
              $(this).before('<div class="error">Thank you for your subscription!</div>');
            });
          });
        }

        return false;
    });
  }
});



//Cond Plugin For Slider
/*
 * http://benalman.com/projects/jquery-cond-plugin/
 *
 * Copyright (c) 2009 "Cowboy" Ben Alman
 * Licensed under the MIT license
 * http://benalman.com/about/license/
 *
 * Based on suggestions and sample code by Stephen Band and DBJDBJ in the
 * jquery-dev Google group: http://bit.ly/jqba1
 */
(function($){$.fn.cond=function(){var e,a=arguments,b=0,f,d,c;while(!f&&b<a.length){f=a[b++];d=a[b++];f=$.isFunction(f)?f.call(this):f;c=!d?f:f?d.call(this,f):e}return c!==e?c:this}})(jQuery);