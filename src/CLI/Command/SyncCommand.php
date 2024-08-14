<?php

namespace CAC\NetworkInfo\CLI\Command;

use \CAC\NetworkInfo\Schema;
use \CAC\NetworkInfo\App;

use \WP_CLI;
use \WP_CLI_Command;
use \WP_CLI\Utils;

use \WP_Site_Query;

class SyncCommand extends WP_CLI_Command {
	/**
	 * Sync one or more sites.
	 *
	 * ## OPTIONS
	 *
	 * [<site-id>...]
	 * : One or more site IDs or URLs to sync.
	 *
	 * [--all-sites]
	 * : Sync all sites.
	 */
	public function __invoke( $args, $assoc_args ) {
		if ( ! empty( $assoc_args['all-sites'] ) ) {
			$site_ids = $this->get_all_site_ids();
		} else {
			$site_ids = $this->generate_site_ids( $args );
		}

		$site_count = count( $site_ids );

		$progress = Utils\make_progress_bar(
			sprintf( 'Syncing data for %s sites.', number_format_i18n( $site_count ) ),
			$site_count
		);

		foreach ( $site_ids as $site_id ) {
			App::sync_site( $site_id );
			$progress->tick();
		}

		$progress->finish();

		WP_CLI::success( 'Data synced!' );
	}

	protected function get_all_site_ids() {
		$query = new WP_Site_Query(
			[
				'fields' => 'ids',
				'number' => null,
				'domain' => '',
				'path'   => '',
			]
		);

		return $query->get_sites();
	}

	protected function generate_site_ids( $args ) {
		if ( empty( $args ) ) {
			return [ get_current_blog_id() ];
		}

		$site_ids = array_map(
			function( $arg ) {
				if ( is_numeric( $arg ) ) {
					return (int) $arg;
				} else {
					if ( 0 !== strpos( $arg, 'http' ) ) {
						$arg = 'http://' . $arg;
					}

					$parts = wp_parse_url( $arg );

					$domain = isset( $parts['host'] ) ? $parts['host'] : '';
					$path   = isset( $parts['path'] ) ? trailingslashit( $parts['host'] ) : '/';

					$site = get_site_by_path( $domain, $path );
					if ( $site ) {
						return (int) $site->blog_id;
					}
				}
			},
			$args
		);

		return array_unique( array_filter( $site_ids ) );
	}
}
