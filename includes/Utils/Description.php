<?php

namespace NewfoldLabs\WP\Module\AI\Utils;

class Description {
    public function set_description_container() {
        //$plugin_url = BLUEHOST_PLUGIN_URL;
        // $script_url = $plugin_url . '/vendor/newfold-labs/wp-module-onboarding/vendor/newfold-labs/wp-module-ai/dist/index.js';
        $script_url = '../../dist/index.js';
        wp_enqueue_script('custom-plugin-script-description', $script_url, array(), '1.0', true);
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