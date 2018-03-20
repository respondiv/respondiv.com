<?php
include_once(BDWP_INCLUDE_PATH . 'BackwardCompatibility.php');
include_once(BDWP_INCLUDE_PATH . 'CaptchaIntegration.php');
include_once(BDWP_INCLUDE_PATH . 'Comments.php');
include_once(BDWP_INCLUDE_PATH . 'ContactForm7.php');
include_once(BDWP_INCLUDE_PATH . 'Database.php');
include_once(BDWP_INCLUDE_PATH . 'Diagnostics.php');
include_once(BDWP_INCLUDE_PATH . 'HttpHelpers.php');
include_once(BDWP_INCLUDE_PATH . 'InstallCaptchaProvider.php');
include_once(BDWP_INCLUDE_PATH . 'Localization.php');
include_once(BDWP_INCLUDE_PATH . 'Login.php');
include_once(BDWP_INCLUDE_PATH . 'LostPassword.php');
include_once(BDWP_INCLUDE_PATH . 'PluginInfo.php');
include_once(BDWP_INCLUDE_PATH . 'Register.php');
include_once(BDWP_INCLUDE_PATH . 'Settings.php');
include_once(BDWP_INCLUDE_PATH . 'Tools.php');
include_once(BDWP_INCLUDE_PATH . 'WordPress.php');
include_once(BDWP_INCLUDE_PATH . 'WordPressPlugin.php');

if (is_admin()) { 
    include_once(BDWP_INCLUDE_PATH . 'Update.php'); 
}
