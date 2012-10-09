<?php

/**
 * Plugin Name: Rank Tracker
 * Plugin URI: http://whooshtraffic.com
 * Description: Whoosh Traffic's Rank Tracker right in your own blog.
 * Version: 0.8
 * Author: Parnell Springmeyer
 * Author URI: mailto:support@whooshtraffic.com
 */

require_once('includes/whoosh-traffic_api_php/Ranktracker.php');
require_once('includes/plugin_callbacks.php');
require_once('includes/Savant3-3.0.1/Savant3.php');

function domain($item)
{
    $domain = parse_url($item);
    $domain = $domain['host'];
    
    return $domain;
}

class RankTrackerPlugin extends PluginCallbacks
{
    private $api;
    private $errors = false;
    
    public function __construct()
    {
        parent::__construct('Rank Tracker', 'ranktracker');
        
        add_action('init', array($this, 'admin_init'));
        
        // Use WordPress' API to get the options in settings
        $api_key   = get_option('whoosh_api_key', Null);
        $api_login = get_option('whoosh_api_login', Null);
        
        if($api_key and $api_login)
        {
            $this->api = new Ranktracker($api_key, $api_login);
        } else {
            $this->errors = array("Your API keys need to be set! Look in Settings -> General -> Whoosh Traffic for the relevant settings fields!");
        }
    }
    
    /**
     * Initialize the plugin and all of its menus. Much of the
     * initialization logic is in the parent class PluginCallbacks.
     */
    public function admin_init()
    {
        add_action('admin_menu',          array(&$this, 'admin_menu'));
        add_action('admin_head',          array('PluginCallbacks', 'admin_css'));
        add_action('admin_head',          array('PluginCallbacks', 'rickshaw_css'));
        add_action('admin_print_scripts', array('PluginCallbacks', 'admin_js'));
        
        add_filter('admin_footer_text',   array('PluginCallbacks', 'admin_footer_left'));
        add_filter('update_footer',       array('PluginCallbacks', 'admin_footer_right'), 11);

        // Ajax actions
        add_action('wp_ajax_'.$this->plugin_id.'_delete_pair', array(&$this, 'ajax_delete_pair'));
        add_action('wp_ajax_'.$this->plugin_id.'_update_pair', array(&$this, 'ajax_update_pair'));
        
        /**
         * Check to make sure settings is set to the master, if not,
         * register it.
         */
        $settings_master = get_option('whoosh_settings_master', False);
        
        if(!$settings_master or $settings_master == 'ranktracker')
        {
            add_action('admin_init', array('PluginCallbacks', 'whoosh_settings_init'));
            
        }
    }
    
    /**
     * Build the administrative menu and sub-menus.
     */
    public function admin_menu()
    {
    
        add_menu_page(
            __('Ranked Results'),
            __($this->plugin_name),
            'administrator',
            $this->plugin_id,
            array(&$this, 'admin_ranked_results'),
            plugins_url('', __FILE__).'/images/icon.png',
            100);
        
        add_submenu_page(
            $this->plugin_id,
            __('Ranked Results'),
            __('Ranked Results'),
            'administrator',
            $this->plugin_id,
            array(&$this, 'admin_ranked_results'));
        
        add_submenu_page(
            $this->plugin_id,
            'Unranked Results',
            'Unranked Results',
            'manage_options',
            $this->plugin_id.'_unranked_results',
            array(&$this, 'admin_unranked_results')
        );
        
        add_submenu_page(
            $this->plugin_id,
            'Result Graph',
            '',
            'manage_options',
            $this->plugin_id.'_details_page',
            array(&$this, 'admin_details_page')
        );
        
        /* add_submenu_page( */
        /*     $this->plugin_id, */
        /*     'Track New', */
        /*     'Track New', */
        /*     'manage_options', */
        /*     $this->plugin_id.'_add_new', */
        /*     array(&$this, 'admin_add_new_page') */
        /* ); */
    }
    
    public function admin_details_page()
    {
        if(!isset($_GET['pair_id']))
        {
            parent::show_errors(array("A pair id must be provided!"));
        } else {
            $id = intval($_GET['pair_id']);
            
            try {
                $pair = $this->api->get($id);
                $settings = $this->api->settings($id);
                
                // Try getting the timeline, if it doesn't exist it will 404
                try {
                    $timeline = $this->api->timeline($id);
                } catch (Exception $e) {
                    $timeline = False;
                }
                
                // Try getting the page cache, if it doesn't exist it will 404
                try {
                    $page_cache = False;//$this->api->cache($id);
                } catch (Exception $e) {
                    $page_cache = False;
                }
                
                $tpl = new Savant3(array('template_path' => dirname(__FILE__)));
                $tpl->name = $this->plugin_id;
                $tpl->plugin_id = $this->plugin_id;
                $tpl->static = $this->plugin_url;
                
                $tpl->pair = $pair;
                $tpl->settings = $settings;
                $tpl->timeline = $timeline;
                $tpl->page_cache = $page_cache;
                
                echo $tpl->display('templates/ranktracker_details.tpl.php');
            } catch (Exception $e) {
                parent::show_errors(array($e->getMessage()));
            }
        }
    }
    
    public function admin_ranked_results()
    {
        if($this->errors)
        {
            parent::show_errors($this->errors);
        } else {
            $this->admin_results_page('ranked', 'Ranked Results');
        }
    }

    public function admin_unranked_results()
    {
        if($this->errors)
        {
            parent::show_errors($this->errors);
        } else {
            $this->admin_results_page('unranked', 'Unranked Results');
        }
    }
    
    /**
     * Generate a rendered HTML page for either ranked or unranked
     * results.
     */
    private function admin_results_page($type='ranked', $title = 'Results')
    {
        set_time_limit(0);
        $error = array();
        
        // Be sure to catch any API errors and echo out (since WP
        // doesn't have a very good exception logger
        try {
            $results = $this->api->get_all($type);
            
            $grouped_results = array();
            
            foreach($results as $item)
            {
                $rooturi = domain($item['url']);
                $grouped_results[$rooturi] = array();
            }
            
            foreach($results as $pair)
            {
                $domr = domain($pair['url']);
                if(array_key_exists($domr, $grouped_results))
                {
                    $grouped_results[$domr][] = $pair;
                }
            }
            
            $tpl = new Savant3(array('template_path' => dirname(__FILE__)));
            $tpl->name = $this->plugin_id;
            $tpl->plugin_id = $this->plugin_id;
            $tpl->type = $type;
            $tpl->items = $grouped_results;
            
            echo $tpl->display('templates/ranktracker_results.tpl.php');
        } catch (Exception $e) {
            parent::show_errors(array($e->getMessage()));
        }

    }
    
    /**
     * Handle the AJAX deletion of a pair.
     */
    public function ajax_delete_pair()
    {
        if(isset($_POST['pair_id']) && !empty($_POST['pair_id']))
        {
            try {
                $this->api->delete(intval($_POST['pair_id']));
                $res = json_encode(array('errors' => false));
            } catch(Exception $e) {
                $res = json_encode(array('errors' => true, 'message' => $e->getMessage()));
            }
        } else {
            $res = json_encode(array('errors' => true, 'message' => 'No pair_id or it was empty'));
        }

        die($res);
    }
}

global $WT;
$WT = new RankTrackerPlugin();
//register_activation_hook( __FILE__, '$WT->whoosh_install' );
