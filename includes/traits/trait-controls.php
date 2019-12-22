<?php



namespace ibc;



if ( ! defined( 'ABSPATH' ) ) { exit; };



trait Controls {



	function get_select( $args ) {
		$args = wp_parse_args( $args, array(
			'id'         => '',
			'name'       => '',
			'readonly'   => false,
			'disabled'   => false,
			'value'      => '',
			'choices'    => array(),
		) );
		extract( $args );
		printf(
			'<select id="%1$s" name="%2$s" %3$s %4$s></select>',
			$id,
			$name,
			readonly( $readonly, true, false ),
			disabled( $disabled, true, false )
		);
	}



	function render_choices( $choices, $selected = '' ) {
		$result = __return_empty_array();
		if ( is_array( $choices ) ) {
			foreach ( $choices as $value => $label ) {
				$result[] = sprintf(
					'<option value="%1$s" %2$s>%3$s<option>',
					$value,
					selected( $selected, $value, false ),
					$label
				);
			}
		}
		return implode( "\r\n", $result );
	}



}