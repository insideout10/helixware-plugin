<?php
/**
 * This file contains logging functions.
 */

/**
 * Log messages by sending them to the handler defined via the *hewa_write_log_handler* filter, or the default
 * *hewa_write_log_handler* method.
 *
 * @uses hewa_write_log_handler as default log handler, to write to the debug log.
 *
 * @param string $log A log message.
 */
function hewa_write_log( $log )
{
    $handler = apply_filters( 'hewa_write_log_handler', null );

    if ( is_null( $handler ) ) {
        return wl_write_log_handler( $log );
    }

    call_user_func( $handler, $log );
}

/**
 * Log messages to the error log.
 *
 * @param string $log The log message.
 */
function hewa_write_log_handler( $log ) {
    if ( true === WP_DEBUG ) {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}