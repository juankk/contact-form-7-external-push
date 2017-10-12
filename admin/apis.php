<?php

/**
* Class that is in charge of lsiting and displaying the connectors configured
*/
class Admin_Apis{
  private $apis;
  const APIS_FOLDER = __DIR__ . '/apis';

  public function __construct() {
    add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    $this->initialize_apis();
  }

	function admin_menu() {
    $addnew = add_submenu_page(
      'wpcf7',
      __( 'APIS', 'contact-form-7-external-push' ),
      __( 'APIS', 'contact-form-7-external-push' ),
      'manage_options',
      'wpcf7_apis',
      [$this,'render_apis_configuration'] );
	}

  function render_apis_configuration() {
    if ( isset( $_POST['_wpnonce-save-wpcf7-api'] ) &&
      wp_verify_nonce( $_POST['_wpnonce-save-wpcf7-api'], 'save-wpcf7-apis' )
    ) {
      $configuration = $_POST['wpcf7-connector'];
      update_option( 'wpcf7-connector', $configuration );
      $this->load_apis_configuration( $configuration );
      $out = '<div class="updated" id="messages"><p>';
      $out .= 'Configuration saved.';
      $out .= '</p></div>';
    }
    include_once( __DIR__ . '/views/options-view.php' );
  }

  private function load_apis_configuration( $configuration ){
    foreach ( $this->apis as $api ) {
      $api->load_configuration( $configuration );
    }
  }

  /**
   * loads the configured libraries into memory to use them later.
   **/
  public function initialize_apis() {
    $configuration = get_option( 'wpcf7-connector' );
    $this->apis = array();
    $files = (array) $this->get_files( 'php', 0 );
    foreach ($files as $file) {
      include_once( $file );
      $name = basename( $file, '.php' );
      $class_name = 'contact_form7_'.$name.'_connector';
      $this->apis[ $name ] = new $class_name( $configuration );
    }
  }

  /**
	 * Return files in apis folder
	 *
	 * @param mixed $type Optional. Array of extensions to return. Defaults to all files (null).
	 * @param int $depth Optional. How deep to search for files. Defaults to a flat scan (0 depth). -1 depth is infinite.
	 * @return array Array of files, keyed by the path to the file relative to the theme's directory, with the values
	 * 	             being absolute paths.
	 */
	public function get_files( $type = null, $depth = 0 ) {
		$files = (array) self::scandir( self::APIS_FOLDER, $type, $depth );
		return $files;
	}

  /**
	 * Scans a directory for files of a certain extension.
	 *
	 * @param string            $path          Absolute path to search.
	 * @param array|string|null $extensions    Optional. Array of extensions to find, string of a single extension,
	 *                                         or null for all extensions. Default null.
	 * @param int               $depth         Optional. How many levels deep to search for files. Accepts 0, 1+, or
	 *                                         -1 (infinite depth). Default 0.
	 * @param string            $relative_path Optional. The basename of the absolute path. Used to control the
	 *                                         returned path for the found files, particularly when this function
	 *                                         recurses to lower depths. Default empty.
	 * @return array|false Array of files, keyed by the path to the file relative to the `$path` directory prepended
	 *                     with `$relative_path`, with the values being absolute paths. False otherwise.
	 */
	private static function scandir( $path, $extensions = null, $depth = 0, $relative_path = '' ) {
		if ( ! is_dir( $path ) )
			return false;

		if ( $extensions ) {
			$extensions = (array) $extensions;
			$_extensions = implode( '|', $extensions );
		}

		$relative_path = trailingslashit( $relative_path );
		if ( '/' == $relative_path )
			$relative_path = '';

		$results = scandir( $path );
		$files = array();

		foreach ( $results as $result ) {
			if ( '.' == $result[0] )
				continue;
			if ( is_dir( $path . '/' . $result ) ) {
				if ( ! $depth || 'CVS' == $result )
					continue;
				$found = self::scandir( $path . '/' . $result, $extensions, $depth - 1 , $relative_path . $result );
				$files = array_merge_recursive( $files, $found );
			} elseif ( ! $extensions || preg_match( '~\.(' . $_extensions . ')$~', $result ) ) {
				$files[ $relative_path . $result ] = $path . '/' . $result;
			}
		}

		return $files;
	}

}

/*
$addnew = add_submenu_page(
  'wpcf7',
  __( 'APIS', 'contact-form-7-external-push' ),
  __( 'APIS', 'contact-form-7-external-push' ),
  'manage_options',
  'wpcf7_apis',
  'render_apis_configuration' );
  function render_apis_configuration(){
    echo 'testing';
  }
*/
