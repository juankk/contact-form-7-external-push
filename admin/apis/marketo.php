<?php

class contact_form7_marketo_connector extends contact_form7_connector {

	private $title = 'Marketo';
	private $configuration_values = [];
	protected $form_panel_description;

	protected $fields = [
		[
			'title' => 'Enpoint',
			'name' => 'endpoint'
		],
		[
			'title' => 'Client Id',
			'name' => 'client_id'
		],
		[
			'title' => 'Client Secret',
			'name' => 'client_secret'
		],
	];
	protected $form_fields = [
		[
			'title' => 'Program Name',
			'name' => 'programName',
			'description' => 'Mandatory field',
		],
		[
			'title' => 'Lookup Field',
			'name' => 'lookupField'
		],
		[
			'title' => 'Reason',
			'name' => 'reason'
		],
		[
			'title' => 'Source',
			'name' => 'source'
		],
		[
			'title' => 'E-mail',
			'name' => 'email'
		],
		[
			'title' => 'First Name',
			'name' => 'firstName'
		],
		[
			'title' => 'Last Name',
			'name' => 'lastName'
		],
	];

	public function __construct( $configuration ) {
		parent::init( $configuration );
		$this->form_panel_description = __( "Change this configuration to push info to marketo", 'contact-form-7-external-push' );
	}

	public function register_form_settings( $panels ){
	  $panels['marketo-settings-panel'] = array(
				'title' => sprintf( __( '%s Settings', 'contact-form-7-external-push' ), $this->get_title() ),
				'callback' => [ $this, 'editor_form_panel_settings' ] );
	      return $panels;
	}

	public function get_title() {
		return $this->title;
	}


	public function send_form( $contact_form, $result ) {
		$submission = WPCF7_Submission::get_instance( );
		$form_configuration = $this->get_form_configuration( $contact_form );
		$posted_data = $submission->get_posted_data();
		$lead = new stdClass();
		foreach ( $form_configuration as $key => $value ) {
			if( empty($value) || in_array($key, ['programName','source', 'reason', 'enable'], true )){
				continue;
			}
			$tagged_text = new WPCF7_MailTaggedText($value);
			$field_value = $tagged_text->replace_tags();
			$lead->$key = $field_value;
		}

		//send to marketo
		require __DIR__ . '/marketo/push-lead.php';
		$upsert = new PushLeads( $this->values['endpoint'], $this->values['client_id'], $this->values['client_secret'] );
		$upsert->program_name = $form_configuration['programName'];
		$upsert->source = $form_configuration['source'];
		$upsert->reason = $form_configuration['reason'];
		$upsert->input = array($lead);
		$response = $upsert->postData();
		//TODO verify if the items was published to marketo correctly
	}
}
