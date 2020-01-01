<?php

	$action = ( isset( $_REQUEST[ 'action' ] ) && in_array( $_REQUEST[ 'action' ], array( 'table', 'add', 'edit' ) ) ) ? $_REQUEST[ 'action' ] : 'table';
	$nonce = wp_create_nonce( 'publishing_houses' );

?>



<div class="wrap">

	<?php 
		if ( isset( $_REQUEST[ 'notice' ] ) && ! empty( $_REQUEST[ 'notice' ] ) ) {
			printf(
				'<div id="message" class="notice is-dismissible"><p>%1$s</p></div>',
				stripcslashes( $_REQUEST[ 'notice' ] )
			);
		}
	?>

	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>

	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php network_admin_url( __( 'Вторичное меню', IBC_TEXTDOMAIN ) ); ?>">
		<a href="<?php $this->the_publishing_house_link( array( 'nonce' => $nonce ) ); ?>" class="nav-tab <?php echo ( 'table' == $action ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php $this->the_publishing_house_link( array( 'action' => 'add', 'nonce' => $nonce ) ); ?>" class="nav-tab <?php echo ( 'add' == $action ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить', IBC_TEXTDOMAIN ); ?>
		</a>
		<?php if ( 'edit' == $action ) : ?><span class="nav-tab nav-tab-active"><?php _e( 'Редактирование', IBC_TEXTDOMAIN ); ?></span><?php endif; ?>
	</nav>

	<?php if ( 'table' == $action ) : ?>

		<form method="POST">
			<?php $GLOBALS[ 'Publishing_Houses_List_Table' ]->display(); ?>
		</form>

	<?php else : ?>

		<?php
			$id = __return_empty_string();
			$name = __return_empty_string();
			if ( 'edit' == $action && isset( $_REQUEST[ 'id' ] ) ) {
				$publishing_house = $this->get_publishing_house( $_REQUEST[ 'id' ] );
				if ( ! is_wp_error( $publishing_house ) ) {
					$id = $publishing_house->id;
					$name = $publishing_house->name;
				}
			}
		?>
		<form id="publishing-house-form">
			<input type="hidden" name="page" value="publishing_houses">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
			<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>" required="required">
			<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<figure>
						<img src="<?php echo IBC_ASSETS . 'images/publisher.svg' ?>" style="display: block; width: 100%;">
					</figure>
				</div>
				<div class="col-xs-12 col-sm-6 first-sm">
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-6">
							<label for="name"><?php _e( 'Название', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-6">
							<input style="width: 100%;" id="name" type="text" name="name" value="<?php echo esc_attr( $name ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12">
							<?php submit_button( ( 'edit' == $action ) ? __( 'Сохранить', IBC_TEXTDOMAIN ) : __( 'Добавить', IBC_TEXTDOMAIN ) ); ?>
						</div>
					</div>
				</div>
			</div>
		</form>

	<?php endif; ?>

</div>
