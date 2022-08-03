<?php

/**
 * Load Backend related actions
 *
 * @class   IM_Admin
 */


if (!defined('ABSPATH')) {
    exit;
}


class IM_Admin
{
    /**
     * Class intance for singleton  class
     *
     * @var    object
     * @access  private
     * @since    1.0.0
     */
    private static $instance = null;

    /**
     * The token.
     *
     * @var     string
     * @access  public
     */
    public $token;

    /**
     * The main plugin file.
     *
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * Constructor function.
     *
     * @access  public
     * @param string $file plugin start file path.
     * @since   1.0.0
     */
    public function __construct($file = '')
    {

    }


    /**
     * Ensures only one instance of Class is loaded or can be loaded.
     *
     * @param string $file plugin start file path.
     * @return Main Class instance
     * @since 1.0.0
     * @static
     */
    public static function instance($file = '')
    {
        if (is_null(self::$instance)) {
            self::$instance = new self($file);
        }
        return self::$instance;
    }
}