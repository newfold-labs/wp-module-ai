<?php

namespace NewfoldLabs\WP\Module\AI\Utils;

class Description {
    public function set_description_container() {
   
        $plugin_url = plugin_dir_url(""); // URL of the current plugin's directory
        $script_path = 'bluehost-wordpress-plugin/vendor/newfold-labs/wp-module-onboarding/vendor/newfold-labs/wp-module-ai/dist/index.js';
        $script_url = $plugin_url . $script_path;
        $aiscript_url = $plugin_url .  'bluehost-wordpress-plugin/vendor/newfold-labs/wp-module-onboarding/node_modules/@newfold-labs/wp-module-ai/dist/index.js';
        wp_enqueue_script('custom-plugin-script-js', $script_url, array(), '1.0', true);
        wp_enqueue_script('custom-plugin-script', $aiscript_url, array(), '1.0', true);
        ?>
            <div id="description-generator-container"></div>
            <script type='text/javascript'>
                jQuery(document).ready(function($) {
                    $("#description-generator-container").insertAfter($("#blogdescription").closest("tr"));
                });
            </script>;
        <?php
    }

    public function description_admin_init() {
        // Hook the set_description_container method to the admin_footer action
        add_action('admin_footer', array($this, 'set_description_container'));
    }
}