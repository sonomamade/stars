(function( $ ) {
	$(function() {
		var $target	= $( '[data-bind="sonoma-stars-input"]' );
		var $select	= $( '[data-bind="sonoma-stars-input"] select' );
		var $el		= $( '<span class="stars"><i data-value="1" class="fa fa-star-o"></i><i data-value="2" class="fa fa-star-o"></i><i data-value="3" class="fa fa-star-o"></i><i data-value="4" class="fa fa-star-o"></i><i data-value="5" class="fa fa-star-o"></i></span>' );
		
		$target.append( $el );
		
		var rating = 0;
		
		$el.on( 'mouseenter', '[data-value]', function( event ) {
			var hover = $( this ).attr( 'data-value' );
			
			$el.addClass( 'engaged' );
			
			$el.children().each( function( i, star ) {
				var $star = $( star );
				var toggle = $star.attr( 'data-value' ) > hover;
				
				$star.toggleClass( 'fa-star-o',	toggle );
				$star.toggleClass( 'fa-star',	!toggle );
			});
		});

		$el.on( 'mouseleave', function( event ) {
			$el.removeClass( 'engaged' );
			
			$el.children().each( function( i, star ) {
				var $star = $( star );
				var toggle = $star.attr( 'data-value' ) > rating;

				$star.toggleClass( 'fa-star-o',	toggle );
				$star.toggleClass( 'fa-star',	!toggle );
			});
		});

		$el.on( 'click', '[data-value]', function( event ) {
			rating = $( this ).attr( 'data-value' );
			$select.val( rating );
		});
	});
})( jQuery );