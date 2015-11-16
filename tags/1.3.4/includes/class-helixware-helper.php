<?php

/**
 * A helper class.
 *
 * @since 1.2.0
 */
class HelixWare_Helper {

	/**
	 * Converts milliseconds to a timecode (H:m:s.u).
	 *
	 * @since 1.2.0
	 *
	 * @param int $value The number of milliseconds.
	 *
	 * @return string A string formatted as H:m:s.u.
	 */
	public static function milliseconds_to_timecode( $value ) {

		$u     = $value % 1000; // get the milliseconds
		$value = floor( $value / 1000 );

		$s     = $value % 60; // get the seconds.
		$value = floor( $value / 60 );

		$m = $value % 60; // get the minutes.
		$h = floor( $value / 60 ); // get the hours.

		return sprintf( '%02d:%02d:%02d.%03d', $h, $m, $s, $u );
	}

}
