<?php
/**
 * One-time Redis bootstrap for this migrated WordPress site.
 */

add_action(
    'init',
    static function () {
        if ( get_option( 'codex_redis_bootstrap_done' ) ) {
            return;
        }

        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        if ( ! is_plugin_active( 'redis-cache/redis-cache.php' ) ) {
            activate_plugin( 'redis-cache/redis-cache.php', '', false, true );
        }

        $source = WP_CONTENT_DIR . '/plugins/redis-cache/includes/object-cache.php';
        $target = WP_CONTENT_DIR . '/object-cache.php';

        if ( file_exists( $source ) ) {
            if ( ! file_exists( $target ) || md5_file( $source ) !== md5_file( $target ) ) {
                copy( $source, $target );
            }

            update_option( 'codex_redis_bootstrap_done', time(), false );
        }
    },
    1
);
