<?php

namespace CAC\NetworkInfo\CLI\Command;

use \CAC\NetworkInfo\Schema;

use \WP_CLI;
use \WP_CLI_Command;

class DatabaseCommand extends WP_CLI_Command {
	/**
	 * Install the database tables.
	 */
	public function install( $args, $assoc_args ) {
		$schema = new Schema();
		$schema->install_table();
		WP_CLI::success( 'Successfully installed tables!' );
	}
}
