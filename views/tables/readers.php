<div class="wrap">

	<?php $tab = ( isset( $_REQUEST[ 'tab' ] ) && ! empty( $_REQUEST[ 'tab' ] ) ) ? $_REQUEST[ 'tab' ] : ''; ?>

	<h1 class="wp-heading-inline"><?php echo get_admin_page_title() ?></h1>

	<nav class="nav-tab-wrapper wp-clearfix" aria-label="<?php esc_attr_e( 'Вторичное меню', IBC_TEXTDOMAIN ); ?>">
		<a href="<?php echo add_query_arg( array( 'page' => 'readers' ), admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( empty( $tab ) ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Список', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'readers', 'tab' => 'add' ), admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'edit' || $tab == 'add' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Добавить / редактировать', IBC_TEXTDOMAIN ); ?>
		</a>
		<a href="<?php echo add_query_arg( array( 'page' => 'readers', 'tab' => 'import' ), admin_url( 'admin.php?' ) ); ?>" class="nav-tab <?php echo ( $tab == 'import' ) ? 'nav-tab-active' : ''; ?>">
			<?php _e( 'Импорт', IBC_TEXTDOMAIN ); ?>
		</a>
	</nav>

	<?php if ( $tab == 'edit' || $tab == 'add' ) : ?>
		<?php
			$reader_id = __return_empty_string();
			$card_id = __return_empty_string();
			$first_name = __return_empty_string();
			$last_name = __return_empty_string();
			$sex = __return_empty_string();
			$department_id = __return_empty_string();
			if ( isset( $_REQUEST[ 'reader_id' ] ) ) {
				$reader = $this->get_reader( $_REQUEST[ 'reader_id' ] );
				if ( is_object( $reader ) ) {
					$reader_id = $reader->id;
					$card_id = $reader->card_id;
					$first_name = $reader->first_name;
					$last_name = $reader->last_name;
					$sex = $reader->sex;
					$department_id = $reader->department;
				}
			}
		?>
		<div class="row">
			<div class="col-xs-12 col-sm-6">
				<form id="reader-form-edit">
					<input type="hidden" name="reader_id" value="<?php echo $reader_id; ?>">
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="card_id"><?php _e( '№ читательского билета', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="card_id" type="text" value="<?php echo $card_id; ?>" readonly="readonly">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="first_name"><?php _e( 'Имя', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="first_name" type="text" name="first_name" value="<?php echo $first_name; ?>" placeholder="<?php esc_attr_e( 'Имя', IBC_TEXTDOMAIN ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="last_name"><?php _e( 'Фамилия', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<input style="width: 100%;" id="last_name" type="text" name="last_name" value="<?php echo $last_name; ?>" placeholder="<?php esc_attr_e( 'Фамилия', IBC_TEXTDOMAIN ); ?>" required="required">
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<?php _e( 'Пол', IBC_TEXTDOMAIN ); ?>
						</div>
						<div class="col-xs-12 col-sm-8">
							<label for="male"><input id="male" type="radio" name="sex" value="male" <?php checked( $sex, 'male', true ) ?>> <?php _e( 'мужской', IBC_TEXTDOMAIN ); ?></label>
							<label for="female"><input id="female" type="radio" name="sex" value="female" <?php checked( $sex, 'female', true ) ?>> <?php _e( 'женский', IBC_TEXTDOMAIN ); ?></label>
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-12 col-sm-4">
							<label for="department"><?php _e( 'Подразделение', IBC_TEXTDOMAIN ); ?></label>
						</div>
						<div class="col-xs-12 col-sm-8">
							<?php
								$this->wp_dropdown_departments( array(
									'name'         => 'department',
									'selected'     => $department_id,
									'hierarchical' => 1,
									'style'        => 'display: block; width: 100%;'
								) );
							?>
						</div>
					</div>
					<div class="row middle-xs" style="margin: .5em 0;">
						<div class="col-xs-6">
							<?php submit_button( __( 'Сохранить', IBC_TEXTDOMAIN ) ); ?>
						</div>
					</div>
				</form>
				<script>
					jQuery( document ).ready( function () {
						var $form = jQuery( '#reader-form-edit' );
						$form.submit( function ( event ) {
							event.preventDefault();
							jQuery.ajax( {
								type: 'GET',
								url: ajaxurl,
								data: {
									action: 'readers',
									security: '<?php echo $nonce; ?>',
									type: '<?php echo $tab; ?>',
									request: $form.serialize(),
								},
								success: function ( answer ) {
									console.log( answer );
									if ( answer.success ) {
										<?php if ( $tab == 'add' ) : ?>
											$form.trigger( 'reset' );
											alert( '<?php _e( 'Добавено!', IBC_TEXTDOMAIN ); ?>' );
											console.log( answer );
										<?php else : ?>
											alert( '<?php _e( 'Сохранено!', IBC_TEXTDOMAIN ); ?>' );
										<?php endif; ?>
									} else {
										alert( '<?php _e( 'Ошибка! Попробуйте позже (server)', IBC_TEXTDOMAIN ); ?>' );
									}
								},
								error: function ( answer ) {
									alert( '<?php _e( 'Ошибка! Попробуйте позже (client)', IBC_TEXTDOMAIN ); ?>' );
									console.log( answer );
								},
							} );
						} );
					} );
				</script>
			</div>
			<div class="col-xs-12 col-sm-6">
				<!-- <img src="<?php echo IBC_ASSETS; ?>images/reader.png"> -->
			</div>
		</div>
	<?php elseif ( $tab == 'import' ) : ?>
		<p>Импорт</p>
	<?php else : ?>
		<form method="POST">
			<?php $GLOBALS[ 'Readers_List_Table' ]->display(); ?>
		</form>
		<script>
			jQuery( document ).ready( function () {
				jQuery( '.reader-action-delete[data-reader]' ).click( function ( event ) {
					event.preventDefault();
					var $link = jQuery( this );
					var reader_id = $link.attr( 'data-reader' );
					jQuery.ajax( {
						type: 'GET',
						url: ajaxurl,
						data: {
							action: 'readers',
							security: '<?php echo $nonce; ?>',
							type: 'delete',
							request: reader_id,
						},
						success: function ( answer ) {
							if ( answer.success ) {
								$link.fadeOut( 250 );
								setTimeout( function () {
									$link.closest( 'tr' ).remove();
								}, 250 );
							} else {
								alert( '<?php _e( 'Ошибка! Попробуйте позже.', IBC_TEXTDOMAIN ); ?>' );	
							}
							console.log( answer );
						},
						error: function ( answer ) {
							alert( '<?php _e( 'Ошибка! Попробуйте позже.', IBC_TEXTDOMAIN ); ?>' );
							console.log( answer );
						},
					} );
				} );
			} );
		</script>
	<?php endif; ?>

</div>