<?php

namespace NewfoldLabs\WP\Module\AI\Utils;

class Description {
    private $script_url;

    public function __construct() {
        $this->script_url = plugins_url('../../dist/index.js', __FILE__);
    }

    public function set_description_container() {
        wp_enqueue_script('custom-plugin-script-description', $this->script_url, array(), '1.0', true);
        global $pagenow;

        if ($pagenow == 'options-general.php') {
            echo '<div id="description-generator-container"></div>';
            echo '<script type="text/javascript">
                    jQuery(document).ready(function($) {
                        $("#description-generator-container").insertAfter($("#blogdescription").closest("tr"));
                    });
                  </script>';
        }

        if ($pagenow == 'post-new.php' || $pagenow == 'post.php') {
            echo '<div id="description-generator-container"></div>';
        }
    }

    public function enqueue_custom_gutenberg_plugin() {
        global $pagenow;
        if ($pagenow !== 'post-new.php' && $pagenow !== 'post.php') {
            return;
        }

        wp_enqueue_script('custom-gutenberg-plugin-script', $this->script_url, array('wp-edit-post', 'wp-plugins', 'wp-element', 'wp-components'), '1.0', true);
    }
    
    public function description_admin_init() {
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_custom_gutenberg_plugin'));
        add_action('admin_footer', array($this, 'set_description_container'));
    }
}
