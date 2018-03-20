<?php
$parseUri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$pluginDirPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

include_once($parseUri[0] . 'wp-load.php');
include_once($pluginDirPath . 'Diagnostics.php');
include_once($pluginDirPath . 'InstallCaptchaProvider.php');

// receive data
$captchaImageUrl = (isset($_POST['captchaImageUrl'])) ? $_POST['captchaImageUrl'] : null;

if (!is_null($captchaImageUrl)) {

    if (BDWP_IsImage($captchaImageUrl)) {
        $status = 'OK';
    } else {
        // disable login form
        BDWP_DisableLoginForm();

        // check php session
        if (!BDWP_Diagnostics::IsSessionEnabled()) {
            $status = 'ERR_SESSION_IS_DISABLED';
        } else if (BDWP_IsEnabledSuspiciousQueryStringsField()) { 
            // the Suspicious Query Strings is enabled in the iThemes Security plugin
            $status = 'ERR_PROBLEMS_WITH_ITHEMES_SECURITY';
        } else {
            $status = 'ERR_IMAGE_BROKEN';
        }
    }

    BDWP_InstallCaptchaProvider::StopInstallation();

    $response = array('status' => $status);
    echo json_encode($response);
    exit;
}


// check captcha image url 
function BDWP_IsImage($p_ImageUrl) {
    $isImage = false;

    $response = wp_remote_get($p_ImageUrl, array('timeout' => 30));
    $statusCode = wp_remote_retrieve_response_code($response);

    $errorConnect = "couldn't connect to host";
    if (is_wp_error($response) && $errorConnect == $response->get_error_message()) {
        // cannot connect to host, this error may be due to security-related issues
        $isImage = true;
    } else if (200 == $statusCode) {
        $size = @getimagesize($p_ImageUrl);
        if (is_array($size)) {
            $isImage = true;
        }
    }

    return $isImage;
}

// disable login form
function BDWP_DisableLoginForm() {
    if (BDWP_IsInstalling()) {
        $options = get_option('botdetect_options');
        $options['on_login'] = false;
        update_option('botdetect_options', $options);
    }
}

// in progress installation
function BDWP_IsInstalling() {
    $workflow = get_option('bdwp_workflow');
    return (is_array($workflow) && array_key_exists('bdphplib_is_installing', $workflow));
}

// check a plugin is exists and activated
function BDWP_IsPluginAvaliable($p_ThemeName) {
    $isAvaliable = false;

    $plugins = BDWP_Diagnostics::GetPlugins();
    foreach ($plugins as $theme) {
        if ($theme['Name'] === $p_ThemeName) {
            if ($theme['Activated']) {
                $isAvaliable = true;
            }
            break;
        }
    }

    return $isAvaliable;
}

// check the Suspicious query strings field is enabled or not in the iThemes Security plugin,
// this field can be stoped render captcha image
function BDWP_IsEnabledSuspiciousQueryStringsField() {
    if (BDWP_IsPluginAvaliable('iThemes Security')) {
        $itsecTweaks = get_option('itsec_tweaks');
        if (is_array($itsecTweaks) &&
            array_key_exists('suspicious_query_strings', $itsecTweaks) && 
            $itsecTweaks['suspicious_query_strings']
        ) {
            return true;
        }
    }
    return false;
}
