<?php

namespace CAC\NetworkInfo;

use CAC\NetworkInfo\Site\ThemeRecord;

class App {
	/**
	 * Constructor.
	 *
	 * @since 0.1.0
	 *
	 * @return CAC\NetworkInfo\App
	 */
	private function __construct() {
		return $this;
	}

	public static function get_instance() {
		static $instance;

		if ( empty( $instance ) ) {
			$instance = new self();
		}

		return $instance;
	}

	public function init() {
		if ( defined( 'WP_CLI' ) ) {
			$this->set_up_cli_commands();
		}

		$this->set_up_hooks();
	}

	protected function set_up_cli_commands() {
		\WP_CLI::add_command( 'cac-network-info database', '\CAC\NetworkInfo\CLI\Command\DatabaseCommand' );
		\WP_CLI::add_command( 'cac-network-info sync', '\CAC\NetworkInfo\CLI\Command\SyncCommand' );
	}

	protected function set_up_hooks() {
		add_action( 'switch_theme', [ __CLASS__, 'sync_site_on_theme_switched' ] );
		add_action( 'wp_initialize_site', [ __CLASS__, 'sync_site_on_wp_initialize_site' ] );
	}

	public static function sync_site_on_wp_initialize_site( $site ) {
		self::sync_site( $site->blog_id );
	}

	public static function sync_site_on_theme_switched() {
		self::sync_site( get_current_blog_id() );
	}

	public static function sync_site( $site_id ) {
		self::sync_site_theme( $site_id );
	}

	public static function sync_site_theme( $site_id ) {
		$template   = get_blog_option( $site_id, 'template' );
		$stylesheet = get_blog_option( $site_id, 'stylesheet' );

		$theme_record = new ThemeRecord( $site_id );
		$theme_record->set_site_id( $site_id );
		$theme_record->set_template( $template );
		$theme_record->set_stylesheet( $stylesheet );
		$theme_record->save();
	}
}
