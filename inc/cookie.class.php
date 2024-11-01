<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('STS_Cookie')) {
	class STS_Cookie
	{
		private static $_instance;

		public static function getInstance()
		{
			if (self::$_instance == null) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * Set cookie
		 *
		 * @param $name
		 * @param $value
		 * @param int $expire   If set to 0, or omitted, the cookie will expire at the end of the session (when the browser closes).
		 * @param bool $secure
		 * @param bool $httponly
		 */
		public function set_cookie($name, $value, $expire = 0, $secure = false, $httponly = false) {
			if ( ! headers_sent() ) {
				setcookie( $name, $value, $expire, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, $secure, $httponly );
			} elseif ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				headers_sent( $file, $line );
				trigger_error( "{$name} cookie cannot be set - headers already sent by {$file} on line {$line}", E_USER_NOTICE ); // @codingStandardsIgnoreLine
			}
		}

		public function get_cookie($name, $default = false) {
			if (isset($_COOKIE[$name])) {
				return $_COOKIE[$name];
			}
			return $default;
		}
	}
}