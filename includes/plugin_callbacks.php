<?PHP

class PluginCallbacks
{
    public $plugin_name;
    public $plugin_id;
    public $plugin_dir;
    public $plugin_url;
    public $plugin_cache_dir;
    
    public function __construct($name, $id)
    {
        $this->plugin_name = $name;
        $this->plugin_id   = $id;
        
        $this->plugin_dir = dirname(dirname(__FILE__));
        $this->plugin_url = plugins_url('', dirname(__FILE__));
        
        $upload_dir = wp_upload_dir();
        $this->plugin_cache_dir = $upload_dir['basedir'].'/'.$this->plugin_id;
        
        if(!file_exists($this->plugin_cache_dir))
        {
            mkdir($this->plugin_cache_dir, 0777);
        }
    }
    
    public function show_errors($msg)
    {
        foreach($msg as $value)
        {
            echo "<div class=\"error settings-error\"><p><strong>$value</strong></p></div>";
        }
    }
    
    /**
     * Add and register the section, settings, and setting fields.
     */
    public function whoosh_settings_init()
    {
        // Set the master
        update_option("whoosh_settings_master", "ranktracker");
        
        add_settings_section(
            'whoosh_settings_section',
            'Whoosh Traffic Settings',
            array('PluginCallbacks', 'whoosh_settings_description_callback'),
            'general'
        );
        
        // Settings
        add_settings_field(
            'whoosh_api_login',
            'API Login',
            array('PluginCallbacks', 'whoosh_settings_api_login'),
            'general',
            'whoosh_settings_section'
        );
        
        register_setting('general', 'whoosh_api_login');
        
        add_settings_field(
            'whoosh_api_key',
            'API Key',
            array('PluginCallbacks', 'whoosh_settings_api_key'),
            'general',
            'whoosh_settings_section'
        );
        
        register_setting('general', 'whoosh_api_key');
    }
    
    /**
     * Html rendering callback.
     */
    public function whoosh_settings_api_key()
    {
        $html = '<input type="text" size="50" id="whoosh_api_key" name="whoosh_api_key" value="'.get_option('whoosh_api_key').'" />';
        $html .= '<label for="whoosh_api_key"> '  . $args[0] . '</label>';
        
        echo $html;
    }
    
    /**
     * Html rendering callback.
     */
    public function whoosh_settings_api_login()
    {
        $html = '<input type="text" size="50" id="whoosh_api_login" name="whoosh_api_login" value="'.get_option('whoosh_api_login').'" />';
        
        $html .= '<label for="whoosh_api_login"> '  . $args[0] . '</label>';
        
        echo $html;
    }
    
    /**
     * Description callback
     */
    public function whoosh_settings_description_callback()
    {
        echo '<p>Fill-out your Whoosh Traffic API key and login key here.</p>';
        echo '<hr style="width: 500px; float: left;" />';
    }
    
    public function rickshaw_css()
    {
        echo '<link rel="stylesheet" type="text/css" media="screen, projection" href="' .plugins_url('', dirname(__FILE__)). '/css/rickshaw.css" /><link rel="stylesheet" type="text/css" media="screen, projection" href="' .plugins_url('', dirname(__FILE__)). '/css/extensions.css" />';
    }
    
    public function admin_css()
    {
    ?>
    <style type="text/css">
        #icon-options-<?php echo $plugin_id ?> {
            background: transparent url(<?php echo plugins_url('', dirname(__FILE__)).'/images/icon32.png' ?>) no-repeat 0px 0px
        }
    </style>
    <?php
    }
    
    public function admin_js()
    {
        if(in_array($_GET['page'],  array('whoosh_results', 'whoosh_unranked_results', 'whoosh_ranked_results')))
        {
            // Add global vars for js
            echo '<script type="text/javascript">
                    var plugin_id = "'.$plugin_id.'";
                 </script>';
             // Add flot
            //wp_register_script($plugin_id.'_flot_js', plugins_url('/js/flot/jquery.flot.js', __FILE__), array('jquery'));
            wp_enqueue_script($plugin_id.'_flot_js', plugins_url('/js/flot/jquery.flot.min.js', __FILE__), array('jquery'));
             // Add admin js
            //wp_register_script($plugin_id.'_admin_js', plugins_url('/js/whoosh.js', __FILE__), array('jquery'));
            wp_enqueue_script($plugin_id.'_admin_js', plugins_url('/js/whoosh.js', __FILE__), array('jquery'));
        }
    }
    
    public function admin_footer_left($text)
    {
        if(in_array($_GET['page'],  array('whoosh_settings', 'whoosh_results', 'whoosh_unranked_results')))
        {
        
            return '<a target="_blank" href="http://whooshtraffic.com/">'.$plugin_name.'</a>';
        }
        
        return $text;
    }
    
    public function admin_footer_right($text)
    {
        if(in_array($_GET['page'],  array('whoosh_settings', 'whoosh_results', 'whoosh_unranked_results')))
        {
            $links = '
                <a target="_blank" href="https://secure.whooshtraffic.com/panel/services/upgrade?service=ranktrack">Upgrade</a>&nbsp;
                <a target="_blank" href="https://whooshtraffic.com/contact/">Contact Us</a>&nbsp;
            ';
            return $links;
        }
        return $text;
    }
    
    public function whoosh_settings_callback()
    {
        // Errors messages
        $error = array();
        
        // Notification messages
        $updated = array();
        
        // Check CURL
        if(false == in_array('curl', get_loaded_extensions()))
        {
            $error[] = 'For correct plugin work you need install <a target="_blank" href="http://php.net/manual/en/book.curl.php">PHP CURL Extension</a>.';
        }
        
        // Check api_login and api_key
        if(isset($_POST['submit']))
        {
            if(isset($_POST['api_login']) && !empty($_POST['api_login']) && isset($_POST['api_key']) && !empty($_POST['api_key']))
            {
                // Save api_login
                update_option($this->plugin_id.'_api_login', $_POST['api_login']);
                // Save api_key
                update_option($this->plugin_id.'_api_key', $_POST['api_key']);
                // Success message
                $updated[] = 'Success';
            } else {
                $error[] = '<u>API Login Token</u> and <u>API Key</u> required to correct work';
            }
        }

        // API LOGIN
        $api_login = get_option($this->plugin_id.'_api_login');
        // API KEY
        $api_key = get_option($this->plugin_id.'_api_key');

    ?>
    <div class="wrap">
        <div id="icon-options-<?php echo $this->plugin_id; ?>" class="icon32"><br></div>
        <h2><?php _e('Settings'); ?></h2>

        <?php foreach ($error as $item): ?>
        <div class="error settings-error">
            <p><strong><?php echo $item; ?></strong></p>
        </div>
        <?php endforeach; ?>

        <?php foreach ($updated as $item): ?>
        <div class="updated settings-error">
            <p><strong><?php echo $item; ?></strong></p>
        </div>
        <?php endforeach; ?>

        <form action="" method="post">
            <h3>API</h3>
            <p>
                You can find api keys on that page <a href="https://secure.whooshtraffic.com/panel/services/api">https://secure.whooshtraffic.com/panel/services/api</a>
            </p>
            <table class="form-table">
                <tbody>
                    <tr valign="top">
                        <th scope="row">
                            <label for="api_login">API Login Token</label>
                        </th>
                        <td>
                            <input id="api_login" class="regular-text" type="text" name="api_login" value="<?php echo $api_login ?>">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">
                            <label for="api_key">API Key</label>
                        </th>
                        <td>
                            <input id="api_key" class="regular-text" type="text" name="api_key" value="<?php echo $api_key ?>">
                        </td>
                    </tr>
                </tbody>
            </table>
            <p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"></p>
        </form>
    </div>
    <?php
    }
}