<?php

abstract class contact_form7_connector
{

    abstract public function get_title();
    abstract public function send_form( $contact_form, $result );
    abstract public function register_form_settings( $panels );

    public function init( $configuration ) {
      $this->load_configuration( $configuration );
      $this->form_panel_description = __( "Change this configuration to push info to marketo", 'contact-form-7-external-push' );
      if ( $this->enabled() ) {
        add_filter( 'wpcf7_editor_panels', [$this, 'register_form_settings'] );
        add_action( 'save_post_wpcf7_contact_form', [$this, 'save_form_configuration'], 10, 3 );
        add_action( 'wpcf7_submit', [$this, 'submit'], 9,2 );
      }
  	}

    public function submit( $contact_form, $result ) {
      $values = $this->get_form_configuration( $wpcf7_form );
      //if the form is not enabled then it will not submit it.
      if ( 'true' === $values['enable'] ) {
        $this->submit( $contact_form, $result );
      }
    }

    public function load_configuration( $configuration ) {
  		$connector_name = esc_attr( strtolower( $this->get_title() ) );
  		$this->values = $configuration[ $connector_name ];
  	}

    public function render_configuration_options() {
  		foreach ( $this->fields as $field ) {
        $connector_name = esc_attr( strtolower( $this->get_title() ) );
        $field_name = esc_attr( $field['name'] );
        $field_id = 'wpcf7-connector-' . $connector_name . '-' . $field_name;
        $field_html_name = 'wpcf7-connector['.$connector_name.']['.$field_name.']';
        $value = (!empty($this->values[$field_name]))? $this->values[$field_name] : '';
        echo '<tr>
      		<th><label for="' . $field_id . '">' . esc_html( $field['title'] ) . '</label></th>
      		<td> <input name="' . $field_html_name . '" id="' . $field_id . '" type="text" value="'.$value.'" class="regular-text code"></td>
      	</tr>';
  		}
  	}

    public function enabled(){
      return ( 'true' === $this->values['enable'] )? true : false;
    }

    public function get_description(){
      return empty( $this->form_panel_description )? '': $this->form_panel_description;
    }

    public function get_form_configuration( $wpcf7_form ) {
      $connector_name = esc_attr( strtolower( $this->get_title() ) );
      $values = get_post_meta( $wpcf7_form->id() , 'wpcf7_connector_' . $connector_name, true);
      return $values;
    }

    public function save_form_configuration( $post_id, $post, $update ) {
      //validation mandatory fields
  		$connector_name = esc_attr( strtolower( $this->get_title() ) );
  		$connector_values = $_POST['wpcf7-connector'][ $connector_name ];
  		update_post_meta( $post_id, 'wpcf7_connector_' . $connector_name, $connector_values );
  	}

    public function editor_form_panel_settings( $wpcf7_form ) {
      $connector_name = esc_attr( strtolower( $this->get_title() ) );
      $values = $this->get_form_configuration( $wpcf7_form );
  	?>
  	<h2><?php echo esc_html( sprintf(__( '%s Settings', 'contact-form-7-external-push' ), $this->get_title() ) ); ?></h2>
  	<fieldset>
  	<legend class="contact-form-editor-box-mail">
      <?php echo $this->get_description(); ?><br/>
      In the following fields, you can use these mail-tags:</br>
      <?php $wpcf7_form->suggest_mail_tags( $args['name'] ); ?>
    </legend>
    <p>
      <?php
        $field_id = 'wpcf7-connector-' . $connector_name . '-enable' ;
        $field_html_name = 'wpcf7-connector['.$connector_name.'][enable]';
        $selected = ( 'true' === $values['enable'] ) ? 'checked="checked"': '';
        echo '<label for=" ' . $field_id . ' ">
          <input type="checkbox" id="' . $field_id . '" name="' . $field_html_name. '" type="checkbox" value="true" ' . $selected .'>
          Enable ' . esc_attr( strtolower( $this->get_title() ) ) .' for this form.
        </label>';
      ?>
    </p>
  	<p class="description">
      <?php
      foreach( $this->form_fields as $field ) {
        $field_name = esc_attr( $field['name'] );
        $field_id = 'wpcf7-connector-' . $connector_name . '-' . $field_name;
        $field_html_name = 'wpcf7-connector[' . $connector_name . '][' . $field_name . ']';
        $description  = isset( $field['description'] )? '('.$field['description'].')' : '';
        $value = !empty( $values[ $field_name ] )? $values[ $field_name ] : '';
        echo '<label for=" ' . $field_id . ' ">' . esc_html( $field['title'] ) . ' ' . $description . '</i><br/>
    			<input type="text" id="' . $field_id . '" name=" ' . $field_html_name. ' " class="large-text" size="70" value="'.$value.'">

    		</label>';
        } ?>
  	</p>
  	</fieldset>
  	<?php
  	}
}
