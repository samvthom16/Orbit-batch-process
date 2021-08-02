 jQuery.fn.orbit_batch_process = function(){

	return this.each(function(){

		var el = jQuery( this );

		var data = window.orbitBrowserData;

		var batches = data.orbit_batches;

		var batch_step = 1;

		/* HIDE ELEMENTS */
		el.find('.orbit-progress-container').hide();
		el.find('.logs-container').hide();

		/* ADD LOG */
		el.addLog = function( log ){
			var li = jQuery( document.createElement('li') );
			li.html( log );
			li.appendTo( el.find('.logs') );
		};

		/* CSS PROGRESS */
		el.updateProgress = function(){
			var width = ( ( batch_step-1 ) / batches ) * 100;

			if( width > 100 ){ width = 100;}
			if( width < 0 ){ width = 0; }

			if( width == 100 ){
				el.find('.result').html('Entire process has been completed');
			}

			el.find('.orbit-progress').animate({ width: width + '%' });
		};

		/* AJAX CALL */
		el.ajaxCall = function(){

			/* FORM THE AJAX URL */
			//var url = el.data('url') + "&orbit_batch_action=" + el.data('action') + "&orbit_batches=" + batches + "&orbit_batch_step=" + batch_step;

			/* UPDATE THE PROGRESS IN THE BUTTON HTML */
			el.find('button').html( el.data('btn') + " " + ( batch_step - 1 ) + "/" + batches );

			data.orbit_batch_step = batch_step;

			jQuery.ajax( {
				method	: 'POST',
				url			: el.data('url'),
				data		: data,
				error		: function(){
					alert( 'Error has occurred' );
				},
				'success'	: function( html ){

					/* CHECK IF BATCH STEP INCREMENT IS ITERATED */
					if( batch_step <= batches ){

						batch_step++;			/* INCREMENT BATCH STEP */

						el.addLog( html );		/* ADD TO THE LOG FROM THE AJAX HTML RESPONSE */

						el.updateProgress();	/* UPDATE PROGRESS BAR */

						el.ajaxCall();			/* EXECUTE THE NEXT BATCH CALL */

					}

				}
			});
		};

		/* button click */
		el.find('button').click( function(){
			el.ajaxCall();
			el.find('button').attr('disabled', 'disabled');

			/* SHOW ELEMENTS */
			el.find('.orbit-progress-container').show();
			el.find('.logs-container').show();
		});

	});
}

// CALLBACK FUNCTION AFTER THE DOM HAS BEEN LOADED
jQuery('document').ready(function(){

	jQuery('[data-behaviour~=orbit-batch]').orbit_batch_process();

});
