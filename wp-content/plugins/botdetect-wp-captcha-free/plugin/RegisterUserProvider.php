<?php
class BDWP_RegisterUserProvider {

    public static function RegisterUser($p_Email) {

        @ini_set('max_execution_time', 30);
        @set_time_limit(30);

        $request = array(
            'requestAction' => 'DL_PROD',
            'technology'	=> 'PHP',
            'source'		=> 'WORDPRESS',
            'email'		=> $p_Email,
            'source_data'	=> array(
                'wp_version' 	=> BDWP_Diagnostics::GetWordPressVersion(),
                'bdwp_version' 	=> BDWP_PluginInfo::GetVersion()
            )
        );

        $relayUrl = 'http://captcha.com/forms/integration/relay.php';
        $response = wp_remote_post($relayUrl, array(
                'timeout' => 30,
                'body'    => array('data' => json_encode($request))
            )
        );

        $responseData = array();
        
        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) == 200) {

            $data = (array)json_decode(wp_remote_retrieve_body($response));

            if ('OK' == $data['status']) {
                $responseData['download_url'] = self::RegisterDownloadUrl($p_Email);
            }

            $responseData['status'] = $data['status'];
        } else {
            $responseData['status'] = 'ERR_REMOTE';
        }

        return $responseData;
    }

    private static function RegisterDownloadUrl($p_Email) {
        $url = sprintf('%scaptcha.com/forms/integration/download.php?utm_source=plugin&amp;utm_medium=wp&amp;utm_campaign=%s&amp;technology=PHP&amp;email=%s&amp;integration=wp&amp;integration_version=%s', BDWP_HttpHelpers::GetProtocol(), BDWP_PluginInfo::GetVersion(), $p_Email, BDWP_PluginInfo::GetVersion());
        return $url;
    }

}
