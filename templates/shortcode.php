<?php

	$params = $atts['params'];
	$params['nonce_data'] = wp_create_nonce( 'orbit-batch-process' );
	$params['orbit_batch_action'] = $atts['batch_action'];
	$params['orbit_batches'] = $atts['batches'];

?>
<div data-url='<?php echo $url;?>' data-behaviour='orbit-batch' data-btn='<?php echo $atts['btn_text'];?>'>
	<script type="text/javascript">
		window.orbitBrowserData = <?php echo json_encode( wp_unslash( $params ) );?>;
	</script>
	<h3><?php _e( $atts['title'] );?></h3>
	<p><?php _e( $atts['desc'] );?></p>
	<div class='orbit-progress-container'>
		<div class='orbit-progress'></div>
	</div>
	<button class='btn btn-default'><?php echo $atts['btn_text'];?></button>
	<p class='result'></p>
	<div class='logs-container'>
		<h5>Logs</h5>
		<ul class='logs'></ul>
	</div>
</div>
