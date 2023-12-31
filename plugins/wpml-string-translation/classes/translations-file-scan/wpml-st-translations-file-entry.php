<?php

class WPML_ST_Translations_File_Entry {

	const NOT_IMPORTED    = 'not_imported';
	const IMPORTED        = 'imported';
	const PARTLY_IMPORTED = 'partly_imported';
	const FINISHED        = 'finished';
	const SKIPPED         = 'skipped';

	const PATTERN_SEARCH_LANG_MO   = '#[-]?([a-z]+[_A-Z]*)\.mo$#i';
	const PATTERN_SEARCH_LANG_JSON = '#([a-z]+[_A-Z]*)-[-a-z0-9]+\.json$#i';

	/** @var  string */
	private $path;

	/** @var string */
	private $domain;

	/** @var int */
	private $status;

	/** @var int */
	private $imported_strings_count = 0;

	/** @var int */
	private $last_modified;

	/** @var string */
	private $component_type;

	/** @var string */
	private $component_id;

	/**
	 * @param string $path
	 * @param string $domain
	 * @param string $status
	 */
	public function __construct( $path, $domain, $status = self::NOT_IMPORTED ) {
		if ( ! is_string( $path ) ) {
			throw new InvalidArgumentException( 'MO File path must be string type' );
		}
		if ( ! is_string( $domain ) ) {
			throw new InvalidArgumentException( 'MO File domain must be string type' );
		}

		$this->path   = $this->convert_to_relative_path( $path );
		$this->domain = $domain;

		$this->validate_status( $status );
		$this->status = $status;
	}

	/**
	 * We can't rely on ABSPATH in out tests
	 *
	 * @param string $path
	 *
	 * @return string
	 */
	private function convert_to_relative_path( $path ) {
		$parts = explode( DIRECTORY_SEPARATOR, $this->fix_dir_separator( WP_CONTENT_DIR ) );

		return str_replace( WP_CONTENT_DIR, end( $parts ), $path );
	}

	/**
	 * @return string
	 */
	public function get_path() {
		return $this->path;
	}

	public function get_full_path() {
		$wp_content_dir = $this->fix_dir_separator( WP_CONTENT_DIR );
		$parts          = explode( DIRECTORY_SEPARATOR, $wp_content_dir );

		return str_replace( end( $parts ), $wp_content_dir, $this->path );
	}

	/**
	 * @return string
	 */
	public function get_path_hash() {
		return md5( $this->path );
	}

	/**
	 * @return string
	 */
	public function get_domain() {
		return $this->domain;
	}

	/**
	 * @return int
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * @param string $status
	 */
	public function set_status( $status ) {
		$this->validate_status( $status );
		$this->status = $status;
	}

	/**
	 * @return int
	 */
	public function get_imported_strings_count() {
		return $this->imported_strings_count;
	}

	/**
	 * @param int $imported_strings_count
	 */
	public function set_imported_strings_count( $imported_strings_count ) {
		$this->imported_strings_count = (int) $imported_strings_count;
	}

	/**
	 * @return int
	 */
	public function get_last_modified() {
		return $this->last_modified;
	}

	/**
	 * @param int $last_modified
	 */
	public function set_last_modified( $last_modified ) {
		$this->last_modified = (int) $last_modified;
	}

	public function __get( $name ) {
		if ( in_array( $name, array( 'path', 'domain', 'status', 'imported_strings_count', 'last_modified' ), true ) ) {
			return $this->$name;
		}
		if ( $name === 'path_md5' ) {
			return $this->get_path_hash();
		}

		return null;
	}

	/**
	 * It extracts locale from mo file path, examples
	 * '/wp-content/languages/admin-pl_PL.mo' => 'pl'
	 * '/wp-content/plugins/sitepress/sitepress-hr.mo' => 'hr'
	 *
	 * @return null|string
	 * @throws RuntimeException
	 */
	public function get_file_locale() {
		return \WPML\Container\make( WPML_ST_Translations_File_Locale::class )->get( $this->get_path(), $this->get_domain() );
	}

	/**
	 * @return string
	 */
	public function get_component_type() {
		return $this->component_type;
	}

	/**
	 * @param string $component_type
	 */
	public function set_component_type( $component_type ) {
		$this->component_type = $component_type;
	}

	/**
	 * @return string
	 */
	public function get_component_id() {
		return $this->component_id;
	}

	/**
	 * @param string $component_id
	 */
	public function set_component_id( $component_id ) {
		$this->component_id = $component_id;
	}

	/**
	 * @param string $status
	 */
	private function validate_status( $status ) {
		$allowed_statuses = array(
			self::NOT_IMPORTED,
			self::IMPORTED,
			self::PARTLY_IMPORTED,
			self::FINISHED,
			self::SKIPPED,
		);

		if ( ! in_array( $status, $allowed_statuses, true ) ) {
			throw new InvalidArgumentException( 'Status of MO file is invalid' );
		}
	}

	/**
	 * @param string $path
	 *
	 * @return string
	 */
	private function fix_dir_separator( $path ) {
		return ( '\\' === DIRECTORY_SEPARATOR ) ? str_replace( '/', '\\', $path ) : str_replace( '\\', '/', $path );
	}

	public function get_extension() {
		return pathinfo( $this->path, PATHINFO_EXTENSION );
	}
}
