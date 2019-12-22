<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; };



class PublicPart {


	
	protected static $instance;



	public static function init() {
		is_null( self::$instance ) AND self::$instance = new self;
		return self::$instance;
	}
	

	function __construct() {
		
	}
	



}