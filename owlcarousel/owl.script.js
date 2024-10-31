jQuery(document).ready(function($) {

		var owl = $('#owl-container');
				//slider
			owl.owlCarousel({
			  autoPlay:true,
			  autoplayTimeout:400,
			  items : 1, 
			  loop:true,
			  margin:0,
			  itemsDesktop : false,
			  itemsDesktopSmall : false,
			  itemsTablet: false,
			  itemsMobile : false,
			   nav:true,
			   autoplayHoverPause:true,
			});
			
});