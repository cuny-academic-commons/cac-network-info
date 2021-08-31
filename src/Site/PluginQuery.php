<?php

namespace CAC\NetworkInfo\Site;

use CAC\NetworkInfo\Schema;

class PluginQuery {
	public static function get_all_for_site( $site_id ) {
		global $wpdb;

		$table_name = Schema::get_plugins_table_name();

		$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM {$table_name} WHERE site_id = %d", $site_id ) );

		return array_map(
			function( $id ) {
				return new PluginRecord( $id );
			},
			$ids
		);
	}
}
