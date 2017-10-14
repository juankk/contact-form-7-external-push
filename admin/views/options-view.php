<div class="wrap">
<h1>Contact Form 7 APIs configurations</h1>
<?php echo $out;?>
<form method="post" action="/wp-admin/admin.php?page=wpcf7_apis" novalidate="novalidate">
<?php foreach ( $this->apis as $api ) {
  $connector_name = esc_attr( strtolower( $api->get_title() ) );
  $field_id = 'wpcf7-connector-' . $connector_name . '-enable';
  $field_html_name = 'wpcf7-connector['.$connector_name.'][enable]';
  $selected = $api->enabled() ? 'checked="checked"': '';
  ?>
  <h2><?php echo esc_html( $api->get_title() );?> Configuration</h2>
  <table class="form-table">
	<tbody>
    <tr>
      <th><label for="<?php echo esc_attr( $field_id ); ?>">Enable</label></th>
      <td><input name="<?php echo esc_attr( $field_html_name ); ?>" id="<?php echo esc_attr( $field_id ); ?>" type="checkbox" value="true" <?php echo $selected; ?>></td>
    </tr>
    <?php $api->render_configuration_options(); ?>
	</tbody>
</table>

<?php } ?>
<?php submit_button( 'Save Changes' ); ?>
<?php wp_nonce_field( 'save-wpcf7-apis', '_wpnonce-save-wpcf7-api', false ); ?>
</form>
</div>
