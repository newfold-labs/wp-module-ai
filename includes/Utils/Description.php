<?php

namespace NewfoldLabs\WP\Module\AI\Utils;

class Description {
    public function set_description_container() {
        $plugin_url = BLUEHOST_PLUGIN_URL;
        $script_url = $plugin_url . '/vendor/newfold-labs/wp-module-onboarding/vendor/newfold-labs/wp-module-ai/dist/index.js';
      /*   echo '<script>console.log("dsdsssds' . $script_url . '")</script>';
        echo '<script>console.log("dsdsssds' . NFD_MODULE_AI_DIR . '")</script>';
        $script_url = NFD_MODULE_AI_DIR.'/dist/index.js' */;

        wp_enqueue_script('custom-plugin-script-description', '../../dist/index.js', array(), '1.0', true);
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
            <script type='text/javascript'>
                jQuery(document).ready(function($) {
                    async function waitForElement(selector, maxAttempts = 5) {
                    let attempts = 0;
                    
                    while (document.querySelector(selector) === null && attempts < maxAttempts) {
                        attempts++;
                        console.log("attempts", attempts);
                        await new Promise(resolve => requestAnimationFrame(resolve));
                    }
                    
                    if (attempts >= maxAttempts) {
                        throw new Error(`Failed to find element with selector "${selector}" after ${maxAttempts} attempts.`);
                    }

                    return document.querySelector(selector);
                }

                waitForElement('#editor .editor-post-excerpt .editor-post-excerpt__textarea textarea')
                    .then(element => {
                        if(element){
                            console.log("elemnt loaded", element);
                            // Element has loaded, do something with it.
                            $("#description-generator-container").insertAfter($("#editor .editor-post-excerpt .editor-post-excerpt__textarea textarea").closest(".components-base-control"));
                        }
                    })
                    .catch(error => {
                        // Handle error (element not found)
                        console.error(error);
                    });
                });
            </script>;
        <?php
        }
    }

    public function description_admin_init() {
        // Hook the set_description_container method to the admin_footer action
        add_action('admin_footer', array($this, 'set_description_container'));
    }
}