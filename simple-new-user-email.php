<?php
/*
Plugin Name: Simple New User Email
Plugin URI: http://andrewnorcross.com/plugins/
Description: Provides a settings panel to modify the default new user email
Author: Andrew Norcross
Version: 1.0.0
Requires at least: 3.8
Author URI: http://andrewnorcross.com
*/
/*  Copyright 2014 Andrew Norcross

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; version 2 of the License (GPL v2) only.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


if( ! defined( 'RKV_SNUE_BASE ' ) )
	define( 'RKV_SNUE_BASE', plugin_basename(__FILE__) );

if( ! defined( 'RKV_SNUE_VER' ) )
	define( 'RKV_SNUE_VER', '1.0.0' );

class RKV_Simple_New_User_Email
{

	/**
	 * [__construct description]
	 */
	public function __construct() {
		add_action					(	'plugins_loaded', 					array(	$this,	'textdomain'				) 			);

		// backend
		add_action					(	'admin_enqueue_scripts',			array(	$this,	'scripts_styles'			),	10		);
		add_action					(	'admin_init', 						array(	$this,	'reg_settings'				) 			);
		add_action					(	'admin_menu',						array(	$this,	'admin_pages'				) 			);

	}

	/**
	 * [textdomain description]
	 * @return [type] [description]
	 */
	public function textdomain() {

		load_plugin_textdomain( 'simple-new-user-email', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * [scripts_styles description]
	 * @param  [type] $hook [description]
	 * @return [type]       [description]
	 */
	public function scripts_styles( $hook ) {

		$screen	= get_current_screen();

		if ( is_object( $screen ) && $screen->base == 'tools_page_new-user-emails' ):

			wp_enqueue_style( 'snue-admin', plugins_url( 'lib/css/snue.admin.css', __FILE__), array(), RKV_SNUE_VER, 'all' );
//			wp_enqueue_script( 'wklg', plugins_url('/lib/js/wklg.init.js', __FILE__) , array('jquery'), RKV_SNUE_VER, true );

		endif;

	}

	/**
	 * [reg_settings description]
	 * @return [type] [description]
	 */
	public function reg_settings() {

		register_setting( 'snue-settings', 'snue-settings' );

	}

	/**
	 * [user_permission description]
	 * @param  [type] $capability [description]
	 * @return [type]             [description]
	 */
	public function user_permission( $capability ) {

		return apply_filters( 'rkv_snue_caps', $capability );

	}

	/**
	 * [admin_pages description]
	 * @return [type] [description]
	 */
	public function admin_pages() {

		add_management_page( __('New User Emails', 'simple-new-user-email'), __('New User Emails', 'simple-new-user-email'), apply_filters( 'rkv_snue_caps', 'manage_options' ), 'new-user-emails', array( $this, 'settings_page' ) );

	}

	/**
	 * [settings_page description]
	 * @return [type] [description]
	 */
	public function settings_page() {
		// fetch data
		$data	= get_option('snue-settings');

		$ud_name	= isset( $data['user-name'] )	&& ! empty ( $data['user-name'] )	? $data['user-name']	: '';
		$ud_from	= isset( $data['user-from'] )	&& ! empty ( $data['user-from'] )	? $data['user-from']	: '';
		$ud_intro	= isset( $data['user-intro'] )	&& ! empty ( $data['user-intro'] )	? $data['user-intro']	: '';
		$ud_text	= isset( $data['user-text'] )	&& ! empty ( $data['user-text'] )	? $data['user-text']	: '';

		$am_name	= isset( $data['admin-name'] )		&& ! empty ( $data['admin-name'] )		? $data['admin-name']		: '';
		$am_from	= isset( $data['admin-from'] )		&& ! empty ( $data['admin-from'] )		? $data['admin-from']		: '';
		$am_to		= isset( $data['admin-to'] )		&& ! empty ( $data['admin-to'] )		? $data['admin-to']			: '';
		$am_subject	= isset( $data['admin-subject'] )	&& ! empty ( $data['admin-subject'] )	? $data['admin-subject']	: '';
		$am_content	= isset( $data['admin-content'] )	&& ! empty ( $data['admin-content'] )	? $data['admin-content']	: '';

		?>

        <div class="wrap">
        	<div id="icon-new-user-emails" class="icon32"><br /></div>
        	<h2><?php _e('New User Emails', 'simple-new-user-email' ); ?></h2>

				<div class="snue-intro">
					<p><?php _e( 'Use the following tags (with curly brackets) for specific pieces of data created during signup', 'simple-new-user-email' ); ?></p>
					<ul>
						<li><span><?php _e( 'User Name', 'simple-new-user-email' ); ?></span> <code>{username}</code></li>
						<li><span><?php _e( 'User Email', 'simple-new-user-email' ); ?></span> <code>{user-email}</code></li>
						<li><span><?php _e( 'Password', 'simple-new-user-email' ); ?></span> <code>{password}</code></li>
						<li><span><?php _e( 'Login URL', 'simple-new-user-email' ); ?></span> <code>{login-url}</code></li>
					</ul>

					<p><strong><?php _e( 'Any fields left blank will use the WordPress defaults.', 'simple-new-user-email' ); ?></strong></p>

				</div>

	            <form method="post" action="options.php">
			    <?php settings_fields( 'snue-settings' ); ?>

				<h3 class="title"><?php _e( 'User Email Notification', 'simple-new-user-email' ); ?></h3>
				<table class="form-table snue-table">
				<tbody>

					<tr>
						<th><label for="user-email-name"><?php _e( 'From Name', 'simple-new-user-email' ); ?></label></th>
						<td>
						<input type="text" class="widefat" value="<?php echo esc_attr( $ud_name ); ?>" id="user-email-name" name="snue-settings[user-name]">
						<p class="description"><?php _e( 'The name the email will be sent from', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><label for="user-email-from"><?php _e( 'From Address', 'simple-new-user-email' ); ?></label></th>
						<td>
						<input type="email" class="widefat" value="<?php echo is_email( $ud_from ); ?>" id="user-email-from" name="snue-settings[user-from]">
						<p class="description"><?php _e( 'The email address sent from', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><label for="user-email-intro"><?php _e( 'Email Subject', 'simple-new-user-email' ); ?></label></th>
						<td>
						<input type="text" class="widefat" value="<?php echo esc_attr( $ud_intro ); ?>" id="user-email-intro" name="snue-settings[user-intro]">
						<p class="description"><?php _e( 'The email subject line', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><label for="user-email-text"><?php _e( 'Email Content', 'simple-new-user-email' ); ?></label></th>
						<td>
						<?php
						$args	= array(
							'textarea_name'	=> 'snue-settings[user-text]',
							'textarea_rows'	=> 6
						);
						wp_editor( $ud_text, 'useremailtext', $args );
						?>
						<p class="description"><?php _e( 'This is the email that will be sent when a new user is added / approved.', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

				</tbody>
				</table>

				<h3 class="title"><?php _e( 'Admin Email Notification', 'simple-new-user-email' ); ?></h3>
				<table class="form-table snue-table">
				<tbody>

					<tr>
						<th><label for="admin-email-name"><?php _e( 'From Name', 'simple-new-user-email' ); ?></label></th>
						<td>
						<input type="text" class="widefat" value="<?php echo esc_attr( $am_name ); ?>" id="admin-email-name" name="snue-settings[admin-name]">
						<p class="description"><?php _e( 'The name the email will be sent from', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><label for="admin-email-from"><?php _e( 'From Address', 'simple-new-user-email' ); ?></label></th>
						<td>
						<input type="email" class="widefat" value="<?php echo is_email( $am_from ); ?>" id="admin-email-from" name="snue-settings[admin-from]">
						<p class="description"><?php _e( 'The email address sent from', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><label for="admin-email-to"><?php _e( 'To Address', 'simple-new-user-email' ); ?></label></th>
						<td>
						<input type="email" class="widefat" value="<?php echo is_email( $am_to ); ?>" id="admin-email-to" name="snue-settings[admin-to]">
						<p class="description"><?php _e( 'The email address to send the notification', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><label for="admin-email-subject"><?php _e( 'Email Subject', 'simple-new-user-email' ); ?></label></th>
						<td>
						<input type="text" class="widefat" value="<?php echo esc_attr( $am_subject ); ?>" id="admin-email-subject" name="snue-settings[admin-subject]">
						<p class="description"><?php _e( 'The email subject line', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

					<tr>
						<th><label for="admin-email-content"><?php _e( 'Email Content', 'simple-new-user-email' ); ?></label></th>
						<td>
						<?php
						$args	= array(
							'textarea_name'	=> 'snue-settings[admin-content]',
							'textarea_rows'	=> 6
						);
						wp_editor( $am_content, 'adminemailcontent', $args );
						?>
						<p class="description"><?php _e( 'This is the admin content that will be sent when a new user is added / approved.', 'simple-new-user-email' ); ?></p>
						</td>
					</tr>

				</tbody>
				</table>

				<p><input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" /></p>
				</form>

		</div>

	<?php }

	/**
	 * [set_html_content_type description]
	 */
	public function set_html_content_type() {

		return 'text/html';

	}

	/**
	 * [default_welcome description]
	 * @return [type] [description]
	 */
	public function default_welcome( $user, $plaintext_pass ) {

		$user_login	= stripslashes( $user->user_login );

		$message  = __( 'Hello,' , 'simple-new-user-email' ) . '<br />';
		$message .= sprintf( __( 'Welcome to %s! Here\'s how to log in:', 'simple-new-user-email' ), get_option( 'blogname' ) ) . '<br /><br />';
		$message .= wp_login_url() . '<br /><br />';
		$message .= sprintf( __( 'Username: %s', 'simple-new-user-email' ), $user_login ) . '<br />';
		$message .= sprintf( __( 'Password: %s', 'simple-new-user-email' ), $plaintext_pass ) . '<br />';
		$message .= sprintf( __( 'If you have any problems, please contact %s.', 'simple-new-user-email' ), get_option( 'admin_email' ) );

		return $message;

	}

	/**
	 * [default_notify description]
	 * @return [type] [description]
	 */

	public function default_notify( $user ) {

		$user_login	= stripslashes( $user->user_login );
		$user_email	= stripslashes( $user->user_email );

		$message	= sprintf( __( 'New user registration on %s:', 'simple-new-user-email' ), get_option('blogname') ) . '<br />';
		$message	.= sprintf( __( 'Username: %s', 'simple-new-user-email' ), $user_login ) . '<br />';
		$message	.= sprintf( __( 'E-mail: %s', 'simple-new-user-email' ), $user_email ) . '<br />';

		return $message;

	}


	/**
	 * [convert_user_content description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function convert_user_content( $user, $text, $plaintext_pass ) {

		$user_login	= stripslashes( $user->user_login );
		$user_email	= stripslashes( $user->user_email );

		$hold	= array( '{username}', '{user-email}', '{password}', '{login-url}' );
		$full	= array( $user_login, $user_email, $plaintext_pass, wp_login_url() );

		$text	= str_replace( $hold, $full, $text );

		$text	= apply_filters( 'snue_user_email_content', $text );

		$message	= '
		<html>
		<body>
		'.wpautop( $text ).'
		</body>
		</html>
		';

		return trim( $message );

	}

	/**
	 * [convert_admin_content description]
	 * @param  [type] $content [description]
	 * @return [type]          [description]
	 */
	public function convert_admin_content( $user, $text ) {

		$user_login	= stripslashes( $user->user_login );
		$user_email	= stripslashes( $user->user_email );

		$hold	= array( '{username}', '{user-email}' );
		$full	= array( $user_login, $user_email );

		$text	= str_replace( $hold, $full, $text );

		$text	= apply_filters( 'snue_admin_email_content', $text );

		$message	= '
		<html>
		<body>
		'.wpautop( $text ).'
		</body>
		</html>
		';

		return trim( $message );

	}

	/**
	 * [get_user_email_data description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get_user_email_data( $data, $user, $plaintext_pass ) {

		$name	= isset( $data['user-name'] )	&& ! empty ( $data['user-name'] )	? $data['user-name']	: get_bloginfo( 'name' );
		$from	= isset( $data['user-from'] )	&& ! empty ( $data['user-from'] )	? $data['user-from']	: get_option( 'admin_email' );
		$intro	= isset( $data['user-intro'] )	&& ! empty ( $data['user-intro'] )	? $data['user-intro']	: __( 'Your username and password', 'simple-new-user-email' );

		// some fancy stuff for the content
		if ( isset( $data['user-text'] ) && ! empty ( $data['user-text'] ) ) :
			$text	= $this->convert_user_content( $user, $data['user-text'], $plaintext_pass );
		else:
			$text	= $this->default_welcome( $user, $plaintext_pass );
		endif;

		$items	= array(
			'name'	=> esc_attr( $name ),
			'from'	=> is_email( $from ),
			'to'	=> stripslashes( $user->user_email ),
			'intro'	=> esc_attr( $intro ),
			'text'	=> $text
		);

		return $items;

	}

	/**
	 * [get_admin_email_data description]
	 * @param  [type] $data [description]
	 * @return [type]       [description]
	 */
	public function get_admin_email_data( $data, $user ) {

		$name	= isset( $data['admin-name'] )	&& ! empty ( $data['admin-name'] )	? $data['admin-name']	: get_bloginfo( 'name' );
		$from	= isset( $data['admin-from'] )	&& ! empty ( $data['admin-from'] )	? $data['admin-from']	: get_option( 'admin_email' );
		$to		= isset( $data['admin-to'] )	&& ! empty ( $data['admin-to'] )	? $data['admin-to']		: get_option( 'admin_email' );
		$intro	= isset( $data['admin-intro'] )	&& ! empty ( $data['admin-intro'] )	? $data['admin-intro']	: __( 'New signup', 'simple-new-user-email' );

		// some fancy stuff for the content
		if ( isset( $data['admin-text'] ) && ! empty ( $data['admin-text'] ) ) :
			$text	= $this->convert_admin_content( $user, $data['admin-text'] );
		else:
			$text	= $this->default_notify( $user );
		endif;

		$items	= array(
			'name'	=> esc_attr( $name ),
			'from'	=> is_email( $from ),
			'to'	=> is_email( $to ),
			'intro'	=> esc_attr( $intro ),
			'text'	=> $text
		);

		return $items;

	}

	/**
	 * [process_user_email description]
	 * @return [type] [description]
	 */
	public function process_user_email( $data, $user, $plaintext_pass ) {

		// fetch the data related to the email
		$items	= $this->get_user_email_data( $data, $user, $plaintext_pass );

		// switch to HTML format
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		// set my headers
		$headers	= 'From: '.$items['name'].' <'.$items['from'].'>' . "\r\n" ;

		// send the actual email
		wp_mail( $items['to'], $items['intro'], $items['text'], $headers );

		 // reset content-type
		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		return;

	}


	/**
	 * [process_admin_email description]
	 * @return [type] [description]
	 */
	public function process_admin_email( $data, $user ) {

		// fetch the data related to the email
		$items	= $this->get_admin_email_data( $data, $user );

		// switch to HTML format
		add_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		// set my headers
		$headers	= 'From: '.$items['name'].' <'.$items['from'].'>' . "\r\n" ;

		// send the actual email
		wp_mail( $items['to'], $items['intro'], $items['text'], $headers );

		 // reset content-type
		remove_filter( 'wp_mail_content_type', array( $this, 'set_html_content_type' ) );

		return;

	}

/// end class
}

// Instantiate our class
new RKV_Simple_New_User_Email();


/**
 * [wp_new_user_notification description]
 * @param  [type] $user_id        [description]
 * @param  string $plaintext_pass [description]
 * @return [type]                 [description]
 */
if ( class_exists( 'RKV_Simple_New_User_Email' ) && ! function_exists('wp_new_user_notification') ) {
	function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {

		// settings from the plugin
		$data		= get_option('snue-settings');

		// load the class with all the goodness
		$handler	= new RKV_Simple_New_User_Email();

		// get new user data
		$user		= new WP_User( $user_id );

		// fire admin notification
		$admin_process	= $handler->process_admin_email( $data, $user );

		// now go to the new user, assuming the password is there
		if ( empty( $plaintext_pass ) )
			return;

		// process user mail
		$user_process	= $handler->process_user_email( $data, $user, $plaintext_pass );

		return;

	}
}