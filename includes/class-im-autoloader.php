<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 *
 * @class IM_Autoloader
 */
class IM_Autoloader {

	/**
	 * The base path for autoloading.
	 *
	 * @var string
	 */
	protected $base_path = '';

	/**
	 * Whether to use the legacy API classes.
	 *
	 * @var bool
	 */
	protected $legacy_api = false;

	/**
	 * AWC_Autoloader constructor.
	 *
	 * @param string $base_path
	 */
	public function __construct( $base_path ) {
		$this->base_path = untrailingslashit( $base_path );
	}

	/**
	 * Destructor.
	 */
	public function __destruct() {
		$this->unregister();
	}

	/**
	 * Register the autoloader.
	 *
	 * @author Jeremy Pry
	 */
	public function register() {
		spl_autoload_register( array( $this, 'autoload' ) );
	}

	/**
	 * Unregister the autoloader.
	 */
	public function unregister() {
		spl_autoload_unregister( array( $this, 'autoload' ) );
	}

	/**
	 * Autoload a class.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $class The class name to autoload.
	 */
	public function autoload( $class ) {
		$class = strtolower( $class );

		if ( ! $this->should_autoload( $class ) ) {
			return;
		}

		$full_path = $this->base_path . $this->get_relative_class_path( $class ) . $this->get_file_name( $class );
		if ( is_readable( $full_path ) ) {
			require_once( $full_path );
		}
	}

	/**
	 * Determine whether we should autoload a given class.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $class The class name.
	 *
	 * @return bool
	 */
	protected function should_autoload( $class ) {
		// We're not using namespaces, so if the class has namespace separators, skip.
		if ( false !== strpos( $class, '\\' ) ) {
			return false;
		}

		// There are some legacy classes without im in the name.
		// static $legacy = array(
		// 	'wc_order_item_pending_switch'         => 1,
		// 	'wc_report_retention_rate'             => 1,
		// 	'wc_report_upcoming_recurring_revenue' => 1,
		// );
		// if ( isset( $legacy[ $class ] ) ) {
		// 	return true;
		// }

		return  false !== strpos( $class, 'im_' );
	}



	/**
	 * Convert the class name into an appropriate file name.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $class The class name.
	 *
	 * @return string The file name.
	 */
	protected function get_file_name( $class ) {
		$file_prefix = 'class-';
		if ( $this->is_class_abstract( $class ) ) {
			$file_prefix = 'abstract-';
		} elseif ( $this->is_class_interface( $class ) ) {
			$file_prefix = 'interface-';
		}
		return $file_prefix . str_replace( '_', '-', $class ) . '.php';
	}




	/**
	 * Determine if the class is one of our abstract classes.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $class The class name.
	 *
	 * @return bool
	 */
	protected function is_class_abstract( $class ) {
		static $abstracts = array(
			'im_background_repairer'      => true,
		);

		return isset( $abstracts[ $class ] );
	}



	/**
	 * Determine if the class is one of our class interfaces.
	 *
	 * @param string $class The class name.

	 * @return bool
	 */
	protected function is_class_interface( $class ) {
		static $interfaces = array(
			'awc_cache_updater' => true,
		);

		return isset( $interfaces[ $class ] );
	}



	/**
	 * Determine if the class is one of our data stores.
	 *
	 * @param string $class The class name.

	 * @return bool
	 */
	protected function is_class_data_store( $class ) {
		static $data_stores = array(
			'im_related_order_store_cached_cpt'  => true,
		);

		return isset( $data_stores[ $class ] );
	}




	/**
	 * Get the relative path for the class location.
	 *
	 * This handles all of the special class locations and exceptions.
	 *
	 * @author Jeremy Pry
	 *
	 * @param string $class The class name.
	 *
	 * @return string The relative path (from the plugin root) to the class file.
	 */
	protected function get_relative_class_path( $class ) {

		$path     = '/includes';
		$is_admin = ( false !== strpos( $class, 'admin' ) );

        if($is_admin)
            $path .= '/admin';

		if ( $this->is_class_abstract( $class ) ) {		
            $path .= '/abstract_class';	
		} elseif ( $this->is_class_interface( $class ) ) {
			$path .= '/interfaces';
		} 
        
		return trailingslashit( $path );
	}

	/**
	 * Set whether the legacy API should be used.
	 *
	 * @author Jeremy Pry
	 *
	 * @param bool $use_legacy_api Whether to use the legacy API classes.
	 *
	 * @return $this
	 */
	public function use_legacy_api( $use_legacy_api ) {
		$this->legacy_api = (bool) $use_legacy_api;

		return $this;
	}
}