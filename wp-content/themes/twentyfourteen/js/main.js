//Save post hit count in database by using ajax
	jQuery.ajax({
	    url: dt_ajax_obj.ajaxurl,
	    type: 'GET',
	    data: {
	        action: 'register_action_hook',
	        url   : window.location.href,
	    },
	    cache: false,
	    // dataType: json,
	    success: function( data,response ) {
	    	// console.log(data.post_id);
		     if(data.status == 'success'){
		     	console.log(data);
		     	console.log('Total hit of this post is: ' + data.post_hit);
		     	console.log('Successfully database record updated.');		     	
		     }else{
		     	console.log('nope...');
		     }
	    },
	});


if(jQuery('#most_read_vertical_slider').length )  {
		jQuery.ajax({
			// here add timestamp for avoiding cahce 
			// Timestamp ensure every time send a unique request to the server 
		        url: "/most-read-vertical-slider/" + '?time=' + new Date().getTime(),
		        type: "GET",
		        dataType: "html",
		        success: function (data) {
		            jQuery('#most_read_vertical_slider').html(jQuery(data).find('.wpb_wrapper').html());
		            jQuery('#most_read_vertical_slider .vertical_carousel').carouFredSel(); 
		                jQuery('#most_read_vertical_slider .vertical_carousel').carouFredSel({ 
		                    items                : 4, 
		                    direction            : "up", 
		                    scroll : { 
		                        items            : 1, 
		                        easing           : "elastic", 
		                        prev : { button : "#sl-prev", key : "left"},
		                        next : { button : "#sl-next", key : "right" },
		                        // duration         : 1000, 
		                        pauseOnHover     : true 
		                    } 
		                	
		                }); 
		        },
		        error: function (xhr, status) {
		            alert("Sorry, there was a problem!");
		        },
		        beforeSend: function(jqXHR, settings) {
		        	// return url
		             console.log(settings.url);
		        },
		        complete: function (xhr, status) {
		        }
		    });
	}