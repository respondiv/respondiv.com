<?php
class BDWP_Update {

    private $m_PluginInfo;

    public function __construct($p_PluginInfo = array()) {

        $this->m_PluginInfo = $p_PluginInfo;

        // Check latest version and modify the transient wordpress
        add_filter('pre_set_site_transient_update_plugins', array($this, 'CheckPluginUpdate'), 10, 1);

        // Show plugin information
        add_filter('plugins_api', array($this, 'GetPluginInfoFromUrl'), 10, 3);
    }

    public function GetResponseData($p_Info) {

        @ini_set('max_execution_time', 30);
        @set_time_limit(30);

        $url = 'http://captcha.com/forms/integration/wordpress/bdwp_info.php';
        $response = wp_remote_post($url, array(
                'timeout' => 30,
                'body' => array('data' => $p_Info)
            )
        );

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
            return false;
        }

        $responseData = @unserialize(wp_remote_retrieve_body($response));

        if (is_object($responseData)) {
            return $responseData;
        }
        
        return false;
    }

    public function CheckPluginUpdate($p_Transient) {
        if (!empty($p_Transient->checked)) {
            $latestVersionResponse = $this->GetResponseData('bdwp_latest_version');
            if ($latestVersionResponse) {
                if (version_compare($latestVersionResponse->new_version, $this->m_PluginInfo['plugin_version'], '>')) {
                    $p_Transient->response[$this->m_PluginInfo['plugin_basename']] = $latestVersionResponse;
                }
            }
        }

        return $p_Transient;
    }

    public function GetPluginInfoFromUrl($p_False, $p_Action, $p_Args) {

        if (!(is_object($p_Args) && isset($p_Args->slug))) {
            return $p_False;
        }

        // show botdetect plugin information
        if ($this->m_PluginInfo['plugin_folder'] == $p_Args->slug) {
            $responseData = $this->GetResponseData('bdwp_info');
            if ($responseData) {
                return $responseData;
            } else {
                return new WP_Error('bdwp_error', __('An error has occurred while connecting to the <a href="http://captcha.com" target="_blank">captcha.com</a> site. Please try again.', 'botdetect-wp-captcha'));
            }
        } else {
            // show other plugin information
            return $this->GetThirdPartyPluginInfo($p_Action, $p_Args);
        }
    }

    public function GetThirdPartyPluginInfo($p_Action, $p_Args) {

        $url = 'http://api.wordpress.org/plugins/info/1.0/';

        $args = array(
            'timeout' => 30,
            'body' => array(
                'action' => $p_Action,
                'request' => serialize($p_Args)
            )
        );

        $response = wp_remote_post($url, $args);

        $isSuccess = true;

        if (is_wp_error($response) || wp_remote_retrieve_response_code($response) != 200) {
            $isSuccess = false;
        } else {
            $responseData = @unserialize(wp_remote_retrieve_body($response));
            if (is_object($responseData)) {
                return $responseData;
            } else {
                $isSuccess = false;
            }
        }

        if (!$isSuccess) {
            return new WP_Error('bdwp_error', __('An error has occurred while connecting to the WordPress.org site. Please try again.', 'botdetect-wp-captcha'));
        }
    }
    
}
