<?php

namespace CAC\NetworkInfo\Site;

use CAC\NetworkInfo\Schema;

class PluginRecord {
	protected $data = array(
		'id'       => 0,
		'site_id'  => 0,
		'plugin'   => '',
	);

	public function __construct( $id = null ) {
		if ( null !== $id ) {
			$this->populate( $id );
		}
	}

	public function exists() {
		return $this->get_id() > 0;
	}

	public function save() {
		global $wpdb;

		$is_new = ( $this->get_id() === 0 );

		$table_name = Schema::get_plugins_table_name();

		if ( $is_new ) {
			$wpdb->insert(
				$table_name,
				array(
					'site_id' => $this->get_site_id(),
					'plugin'  => $this->get_plugin(),
				),
				array(
					'%d', // site_id
					'%s', // plugin
				)
			);

			$id = $wpdb->insert_id;
			$this->set_id( $id );
		} else {
			$wpdb->update(
				$table_name,
				array(
					'site_id' => $this->get_site_id(),
					'plugin'  => $this->get_plugin(),
				),
				array(
					'id' => $this->get_id(),
				),
				array(
					'%d', // site_id
					'%s', // plugin
				),
				array(
					'%d',
				)
			);
		}

		return true;
	}

	public function populate( $id ) {
		global $wpdb;

		$table_name = Schema::get_plugins_table_name();

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $id ) );
		if ( ! $row ) {
			return;
		}

		$this->set_id( $row->id );
		$this->set_site_id( $row->site_id );
		$this->set_plugin( $row->plugin );
	}

	public function delete() {
		global $wpdb;

		$table_name = Schema::get_plugins_table_name();

		$deleted = $wpdb->delete(
			$table_name,
			[
				'id' => $this->get_id(),
			],
			[
				'%d',
			]
		);

		return $deleted;
	}

	/**
	 * Get record ID.
	 *
	 * @return int
	 */
	public function get_id() {
		return (int) $this->data['id'];
	}

	/**
	 * Get site ID.
	 *
	 * @return int
	 */
	public function get_site_id() {
		return (int) $this->data['site_id'];
	}

	/**
	 * Get plugin.
	 *
	 * @return string
	 */
	public function get_plugin() {
		return $this->data['plugin'];
	}

	/**
	 * Set record ID.
	 *
	 * @param int $id
	 */
	public function set_id( $id ) {
		$this->data['id'] = (int) $id;
	}

	/**
	 * Set site ID.
	 *
	 * @param int $id
	 */
	public function set_site_id( $site_id ) {
		$this->data['site_id'] = (int) $site_id;
	}

	/**
	 * Set plugin.
	 *
	 * @param int $plugin
	 */
	public function set_plugin( $plugin ) {
		$this->data['plugin'] = $plugin;
	}
}
