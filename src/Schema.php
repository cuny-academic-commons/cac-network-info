<?php

namespace CAC\NetworkInfo;

class Schema {
	public function __construct() {}

	public static function get_themes_table_name() {
		global $wpdb;
		return "{$wpdb->base_prefix}network_themes";
	}

	public function install_table() {
		$sql = $this->get_schema();

		if ( ! function_exists( 'dbDelta' ) ) {
			require ABSPATH . '/wp-admin/includes/upgrade.php';
		}

		$installed = dbDelta( $sql );
	}

	public function get_schema() {
		global $wpdb;

		$sql = array();

		$charset_collate = $wpdb->get_charset_collate();

		$table_name = self::get_themes_table_name();

		$sql[] = "CREATE TABLE {$table_name} (
					id bigint(20) NOT NULL AUTO_INCREMENT PRIMARY KEY,
					site_id bigint(20) NOT NULL,
					template varchar(200) NOT NULL,
					stylesheet varchar(200) NOT NULL,
					UNIQUE site_id (site_id),
					KEY template (template),
					KEY stylesheet (stylesheet)
				) {$charset_collate};";

		return $sql;
	}
}
