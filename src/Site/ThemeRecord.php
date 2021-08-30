<?php

namespace CAC\NetworkInfo\Site;

use CAC\NetworkInfo\Schema;

class ThemeRecord {
	protected $data = array(
		'id'         => 0,
		'site_id'    => 0,
		'template'   => '',
		'stylesheet' => '',
	);

	public function __construct( $site_id = null ) {
		if ( null !== $site_id ) {
			$this->populate( $site_id );
		}
	}

	public function exists() {
		return $this->get_id() > 0;
	}

	public function save() {
		global $wpdb;

		$is_new = ( $this->get_id() === 0 );

		$table_name = Schema::get_themes_table_name();

		if ( $is_new ) {
			$wpdb->insert(
				$table_name,
				array(
					'site_id'    => $this->get_site_id(),
					'template'   => $this->get_template(),
					'stylesheet' => $this->get_stylesheet(),
				),
				array(
					'%d', // site_id
					'%s', // template
					'%s', // stylesheet
				)
			);

			$id = $wpdb->insert_id;
			$this->set_id( $id );
		} else {
			$wpdb->update(
				$table_name,
				array(
					'site_id'    => $this->get_site_id(),
					'template'   => $this->get_template(),
					'stylesheet' => $this->get_stylesheet(),
				),
				array(
					'id' => $this->get_id(),
				),
				array(
					'%d', // site_id
					'%s', // template
					'%s', // stylesheet
				),
				array(
					'%d',
				)
			);
		}

		return true;
	}

	public function populate( $site_id ) {
		global $wpdb;

		$table_name = Schema::get_themes_table_name();

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE site_id = %d", $site_id ) );
		if ( ! $row ) {
			return;
		}

		$this->set_id( $row->id );
		$this->set_site_id( $row->site_id );
		$this->set_template( $row->template );
		$this->set_stylesheet( $row->stylesheet );
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
	 * Get template.
	 *
	 * @return string
	 */
	public function get_template() {
		return $this->data['template'];
	}

	/**
	 * Get stylesheet.
	 *
	 * @return string
	 */
	public function get_stylesheet() {
		return $this->data['stylesheet'];
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
	 * Set template.
	 *
	 * @param int $template
	 */
	public function set_template( $template ) {
		$this->data['template'] = $template;
	}

	/**
	 * Set stylesheet.
	 *
	 * @param int $stylesheet
	 */
	public function set_stylesheet( $stylesheet ) {
		$this->data['stylesheet'] = $stylesheet;
	}
}
