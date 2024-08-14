<?php

namespace CAC\NetworkInfo;

use CAC\NetworkInfo\Site\ThemeRecord;
use CAC\NetworkInfo\Site\PluginRecord;
use CAC\NetworkInfo\Site\PluginQuery;

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
		\WP_CLI::add_command( 'cac-network-info query', '\CAC\NetworkInfo\CLI\Command\QueryCommand' );
	}

	protected function set_up_hooks() {
		add_action( 'switch_theme', [ __CLASS__, 'sync_site_on_theme_switched' ] );
		add_action( 'wp_initialize_site', [ __CLASS__, 'sync_site_on_wp_initialize_site' ] );

		// We avoid the 'silent' flag in activate_plugin.
		add_action( 'update_option_active_plugins', [ __CLASS__, 'sync_site_on_plugin_change' ] );
	}

	public static function sync_site_on_wp_initialize_site( $site ) {
		self::sync_site_theme( $site->blog_id );
	}

	public static function sync_site_on_theme_switched() {
		self::sync_site_theme( get_current_blog_id() );
	}

	public static function sync_site_on_plugin_change() {
		self::sync_site_plugins( get_current_blog_id() );
	}

	public static function sync_site( $site_id ) {
		self::sync_site_theme( $site_id );
		self::sync_site_plugins( $site_id );
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

	public static function sync_site_plugins( $site_id ) {
		$active_plugins = (array) get_blog_option( $site_id, 'active_plugins', [] );
		$active_plugins = array_flip( $active_plugins );

		// Deleting all records each time runs up the id column, so we delete only selectively.
		$site_records   = PluginQuery::get_all_for_site( $site_id );
		$recorded_files = [];
		foreach ( $site_records as $site_record ) {
			$plugin_file = $site_record->get_plugin();
			if ( ! isset( $active_plugins[ $plugin_file ] ) ) {
				$site_record->delete();
			} else {
				$recorded_files[ $plugin_file ] = 1;
			}
		}

		$to_create = array_diff_key( $active_plugins, $recorded_files );

		foreach ( $to_create as $plugin_file => $_ ) {
			$plugin_record = new PluginRecord();
			$plugin_record->set_site_id( $site_id );
			$plugin_record->set_plugin( $plugin_file );
			$plugin_record->save();
		}
	}
}
