<?php
class PushLeads{
	private $host = "";
	private $client_id = "";
	private $client_secret = "";

	public $input; //an array of lead records as objects (required)
  public $program_name; // program that activity is attributed to (required)
	public $lookup_field; //field used for deduplication
  public $reason; // activity metadata
  public $source; // activity metadata

  public function __construct( $host, $client_id, $client_secret ) {
    $this->host = rtrim($host,'/');
    $this->client_id = $client_id;
    $this->client_secret = $client_secret;
  }
	public function postData(){
		$url = $this->host . "/rest/v1/leads/push.json?access_token=" . $this->getToken();
		$ch = curl_init($url);
		$requestBody = $this->bodyBuilder();
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json','Content-Type: application/json'));
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $requestBody);
		curl_getinfo($ch);
		$response = curl_exec($ch);
		return $response;
	}

	private function getToken(){
    $ch = curl_init($this->host . "/identity/oauth/token?grant_type=client_credentials&client_id=" . $this->client_id . "&client_secret=" . $this->client_secret);
		curl_setopt($ch,  CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('accept: application/json',));
		$response = json_decode(curl_exec($ch));
		curl_close($ch);
		$token = $response->access_token;
		return $token;
	}
	private function bodyBuilder(){
		$body = new stdClass();
		if (!empty($this->program_name)){
			$body->programName = $this->program_name;
		}
		if (!empty($this->reason)){
			$body->reason = $this->reason;
		}
		if (!empty($this->source)){
			$body->source = $this->source;
		}
		if (!empty($this->lookup_field)){
			$body->lookupField = $this->lookup_field;
		}
		$body->input = $this->input;
		$json = json_encode($body);
		return $json;
	}
}
