<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; };



class Ajax {
	

	use Departments;
	use Readers;


	protected static $instance;


	public static function init() {
		is_null( self::$instance ) AND self::$instance = new self;
		return self::$instance;
	}


	function __construct() {
			// $action = 'readers';
			// add_action( "wp_ajax_{$action}", array( $this, $action ) );
			// add_action( "wp_ajax_nopriv_{$action}", array( $this, $action ) );
		foreach ( array( 'readers', 'departments' ) as $action ) {
			add_action( "wp_ajax_{$action}", array( $this, $action ) );
			add_action( "wp_ajax_nopriv_{$action}", array( $this, $action ) );
		}
	}



	public function readers() {
		if ( isset( $_REQUEST[ 'type' ] ) && ! empty( $_REQUEST[ 'type' ] ) && isset( $_REQUEST[ 'request' ] ) && ! empty( $_REQUEST[ 'request' ] ) ) {
			if ( check_ajax_referer( 'readers', 'security', false ) ) {
				$result = __return_false();
				switch ( $_REQUEST[ 'type' ] ) {
					case 'edit':
					case 'add':
						$request = __return_empty_array();
						wp_parse_str( $_REQUEST[ 'request' ], $request );
						if ( empty( trim( $request[ 'reader_id' ] ) ) ) {
							$result = $this->add_reader( $request[ 'first_name' ], $request[ 'last_name' ], $request[ 'sex' ], $request[ 'department' ] );
						} else {
							$result = $this->update_reader( $request[ 'reader_id' ], $request[ 'first_name' ], $request[ 'last_name' ], $request[ 'sex' ], $request[ 'department' ] );
						}
						break;
					case 'delete':
						// wp_send_json_error( $_REQUEST[ 'request' ] );
						$this->delete_reader( $_REQUEST[ 'request' ] );
						$result = __return_true();
						break;
				}
				if ( $result ) {
					wp_send_json_success( $result );
				} else {
					wp_send_json_error( $result );
				}
			} else {
				wp_die( 'nonce error' );
			}
		} else {
			wp_die( 'request error' );
		}
	}



	public function departments() {
		if ( isset( $_REQUEST[ 'type' ] ) && ! empty( $_REQUEST[ 'type' ] ) && isset( $_REQUEST[ 'request' ] ) && ! empty( $_REQUEST[ 'request' ] ) ) {
			if ( check_ajax_referer( 'departments', 'security', false ) ) {
				$result = __return_false();
				switch ( $_REQUEST[ 'type' ] ) {
					case 'edit':
					case 'add':
						$request = __return_empty_array();
						wp_parse_str( $_REQUEST[ 'request' ], $request );
						if ( empty( trim( $request[ 'department_id' ] ) ) ) {
							$result = $this->add_department( $request[ 'name' ], $request[ 'parent' ] );
						} else {
							$result = $this->update_department( $request[ 'department_id' ], $request[ 'name' ], $request[ 'parent' ] );
						}
						break;
					case 'delete':
						$this->delete_department( $_REQUEST[ 'request' ] );
						$result = __return_true();
						break;
				}
				if ( $result ) {
					wp_send_json_success( $result );
				} else {
					wp_send_json_error( $result );
				}
			} else {
				wp_die( 'nonce error' );
			}
		} else {
			wp_die( 'request error' );
		}
	}




}