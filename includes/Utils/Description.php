<?php

namespace NewfoldLabs\WP\Module\AI\Utils;

class Description {
    public function set_description_container() {
        /* $script_url = plugins_url('../../dist/index.js', __FILE__); */
        $script_url = plugins_url('dist/index.js', NFD_MODULE_AI_DIR);
        echo '<script>console.log("normal script url", "' . $script_url . '")</script>';
        wp_enqueue_script('custom-plugin-script-description', $script_url, array(), '1.0', true);
        global $pagenow;

        if ($pagenow == 'options-general.php') {
            ?>
            <div id="description-generator-container"></div>
            <script type='text/javascript'>
                jQuery(document).ready(function($) {
                    $("#description-generator-container").insertAfter($("#blogdescription").closest("tr"));
                });
            </script>;
        <?php
        }
        if ($pagenow == 'post-new.php' || $pagenow == 'post.php') {
            ?>
            <div id="description-generator-container"></div>
        <?php
        }
    }

    public function enqueue_custom_gutenberg_plugin() {
        global $pagenow;
        if ($pagenow !== 'post-new.php' && $pagenow !== 'post.php') {
            return;
        }
       /* $script_url = plugins_url('../../dist/index.js', __FILE__); */
        $script_url = plugins_url('dist/index.js', NFD_MODULE_AI_DIR);
        echo '<script>console.log("normal script url2", "' . $script_url . '")</script>';
        wp_enqueue_script('custom-gutenberg-plugin-script', $script_url , array('wp-edit-post', 'wp-plugins', 'wp-element', 'wp-components'), '1.0', true);
    }
    
    public function description_admin_init() {
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_custom_gutenberg_plugin'));
        // Hook the set_description_container method to the admin_footer action
        add_action('admin_footer', array($this, 'set_description_container'));
    }
}