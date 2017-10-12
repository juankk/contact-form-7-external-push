<div class="wrap">
<h1>Contact Form 7 APIs configurations</h1>
<?php echo $out;?>
<form method="post" action="/wp-admin/admin.php?page=wpcf7_apis" novalidate="novalidate">
<?php foreach ( $this->apis as $api ) { ?>
  <h2><?php echo esc_html( $api->get_title() );?> Configuration</h2>
  <table class="form-table">
	<tbody>
    <?php $api->render_configuration_options(); ?>
	</tbody>
</table>

<?php } ?>
<?php submit_button( 'Save Changes' ); ?>
<?php wp_nonce_field( 'save-wpcf7-apis', '_wpnonce-save-wpcf7-api', false ); ?>
</form>
</div>
