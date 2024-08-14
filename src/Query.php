<?php

namespace CAC\NetworkInfo;

use WP_Site_Query;

class Query {
	public static function get_sites_using_plugin( $plugin_slug ) {
		global $wpdb;

		$table_name = Schema::get_plugins_table_name();

		// Look for values of 'plugin' that start with "$plugin_slug/"
		$like = $wpdb->esc_like( $plugin_slug ) . '/%';

		$php_file_name = $plugin_slug . '.php';

		$site_ids = $wpdb->get_col( $wpdb->prepare( "SELECT site_id FROM {$table_name} WHERE plugin LIKE %s OR plugin = %s OR plugin = %s", $like, $php_file_name, $slug ) );

		return self::get_sites_by_ids( $site_ids );
	}

	/**
	 * Note this only matches sites using the theme directly, not child themes.
	 */
	public static function get_sites_using_theme( $theme ) {
		global $wpdb;

		$table_name = Schema::get_themes_table_name();

		$site_ids = $wpdb->get_col( $wpdb->prepare( "SELECT site_id FROM {$table_name} WHERE stylesheet = %s", $theme ) );

		return self::get_sites_by_ids( $site_ids );
	}

	public static function get_sites_by_ids( $site_ids ) {
		if ( empty( $site_ids ) ) {
			return [];
		}

		$site_ids = array_map( 'intval', $site_ids );

		$query = new WP_Site_Query();

		return $query->query(
			[
				'number'   => null,
				'site__in' => $site_ids,
			]
		);
	}
}
