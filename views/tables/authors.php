<?php

	$action = ( isset( $_REQUEST[ 'action' ] ) && in_array( $_REQUEST[ 'action' ], array( 'table', 'add', 'edit' ) ) ) ? $_REQUEST[ 'action' ] : 'table';
	$nonce = wp_create_nonce( 'authors' );

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
		<a href="<?php $this->the_author_link( array( 'nonce' => $nonce ) ); ?>" class="nav-tab <?php echo ( 'table' == $action ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php $this->the_author_link( array( 'action' => 'add', 'nonce' => $nonce ) ); ?>" class="nav-tab <?php echo ( 'add' == $action ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить', IBC_TEXTDOMAIN ); ?>
		</a>
		<?php if ( 'edit' == $action ) : ?><span class="nav-tab nav-tab-active"><?php _e( 'Редактирование', IBC_TEXTDOMAIN ); ?></span><?php endif; ?>
	</nav>

	<?php if ( 'table' == $action ) : ?>

		<form method="POST">
			<?php $GLOBALS[ 'Authors_List_Table' ]->display(); ?>
		</form>

	<?php else : ?>

		<?php
			$id = __return_empty_string();
			$first_name = __return_empty_string();
			$last_name = __return_empty_string();
			$middle_name = __return_empty_string();
			if ( 'edit' == $action && isset( $_REQUEST[ 'id' ] ) ) {
				$author = $this->get_author( $_REQUEST[ 'id' ] );
				if ( ! is_wp_error( $author ) ) {
					$id = $author->id;
					$first_name = $author->first_name;
					$last_name = $author->last_name;
					$middle_name = $author->middle_name;
				}
			}
		?>
		<form id="author-form">
			<input type="hidden" name="page" value="authors">
			<input type="hidden" name="action" value="<?php echo esc_attr( $action ); ?>">
			<input type="hidden" name="nonce" value="<?php echo esc_attr( $nonce ); ?>" required="required">
			<input type="hidden" name="id" value="<?php echo esc_attr( $id ); ?>">
			<div class="row">
				<div class="col-xs-12 col-sm-6">
					<figure>
						<img src="<?php echo IBC_ASSETS . 'images/human.svg' ?>" style="display: block; width: 100%;">
					</figure>
				</div>
				<div class="col-xs-12 col-sm-6 first-sm">
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="first_name"><?php _e( 'имя', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="first_name" type="text" name="first_name" value="<?php echo esc_attr( $first_name ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="last_name"><?php _e( 'Фамилия', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="last_name" type="text" name="last_name" value="<?php echo esc_attr( $last_name ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="middle_name"><?php _e( 'Отчество', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="middle_name" type="text" name="middle_name" value="<?php echo esc_attr( $middle_name ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12">
							<?php submit_button( ( 'edit' == $action ) ? __( 'Сохранить', IBC_TEXTDOMAIN ) : __( 'Добавить', IBC_TEXTDOMAIN ) ); ?>
						</div>
					</div>
				</div>
			</div>

	<?php endif; ?>

</div>
