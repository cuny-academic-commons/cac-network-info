<?php

namespace CAC\NetworkInfo\CLI\Command;

use CAC\NetworkInfo\Query;
use \WP_CLI_Command;

class QueryCommand extends WP_CLI_Command {
	/**
	 * Query for network use of plugin or theme.
	 *
	 * ## options
	 *
	 * <type>
	 * : Type of query. Either 'plugin' or 'theme'.
	 * ---
	 * options:
	 *  - plugin
	 *  - theme
	 * ---
	 *
	 * <slug>
	 * : Plugin or theme slug to query.
	 */
	public function __invoke( $args, $assoc_args ) {
		list( $type, $slug ) = $args;

		if ( 'plugin' === $type ) {
			$sites = Query::get_sites_using_plugin( $slug );
		} else {
			$sites = Query::get_sites_using_theme( $slug );
		}

		$site_data = array_map(
			function( $site ) {
				return [
					'blog_id' => $site->blog_id,
					'url'     => $site->domain . $site->path,
					'name'    => $site->blogname,
				];
			},
			$sites
		);

		\WP_CLI\Utils\format_items( 'table', $site_data, [ 'blog_id', 'url', 'name' ] );

		\WP_CLI::success( sprintf( 'Found %s sites using %s "%s".', number_format_i18n( count( $sites ) ), $type, $slug ) );
	}
}
