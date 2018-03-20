<?php
$parseUri = explode('wp-content', $_SERVER['SCRIPT_FILENAME']);
$pluginDirPath = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR;

include_once($parseUri[0] . 'wp-load.php');
include_once($pluginDirPath . 'InstallCaptchaProvider.php');
include_once($pluginDirPath . 'PluginInfo.php');
include_once($pluginDirPath . 'RegisterUserProvider.php');

// receive data
$customerEmail = (isset($_POST['customerEmail'])) ? $_POST['customerEmail'] : null;

if (!is_null($customerEmail)) {
    BDWP_InstallCaptchaProvider::StartInstallation();

    $customerEmail = wp_filter_nohtml_kses($customerEmail);
    $responseData = BDWP_RegisterUserProvider::RegisterUser($customerEmail);

    if ('OK' === $responseData['status']) {
        BDWP_InstallCaptchaProvider::SaveCustomerEmail($customerEmail);
    } else {
        BDWP_InstallCaptchaProvider::StopInstallation();
    }

    echo json_encode($responseData);
    exit;
}
