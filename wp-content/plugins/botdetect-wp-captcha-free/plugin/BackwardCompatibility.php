<?php
class BDWP_BackwardCompatibility {

    public static function PluginVersions() {
        return array(
            '3.0.Beta1.7'   => null,
            '3.0.Beta3.0'   => 'MigrateTo_3_0_Beta3_0',
            '3.0.Beta3.1'   => 'MigrateTo_3_0_Beta3_1',
            '3.0.Beta3.2'   => 'MigrateTo_3_0_Beta3_2',
            '3.0.Beta3.3'   => 'MigrateTo_3_0_Beta3_3',
            '3.0.Beta3.4'   => 'MigrateTo_3_0_Beta3_4',
            '3.0.Beta3.5'   => 'MigrateTo_3_0_Beta3_5',
            '3.0.0.0'       => 'MigrateTo_3_0_0_0',
            '3.0.1.0'       => 'MigrateTo_3_0_1_0',
            '3.0.1.1'       => 'MigrateTo_3_0_1_1',

        );
    }

    public static function MigrateTo_3_0_1_1() {}
    public static function MigrateTo_3_0_1_0() {}
    public static function MigrateTo_3_0_0_0() {}
    public static function MigrateTo_3_0_Beta3_5() {}

    public static function MigrateTo_3_0_Beta3_4() {
        $settings = get_option('bdwp_settings');
        if (is_array($settings)) {
            $settings['bdwp_instance_id'] = BDWP_Tools::GenerateGuid();
            update_option('bdwp_settings', $settings);
        }
    }

    public static function MigrateTo_3_0_Beta3_3() {}

    public static function MigrateTo_3_0_Beta3_2() {
        delete_option('bdwp_press_btn_save_auto_install');
    }
	
    public static function MigrateTo_3_0_Beta3_1() {
        $options = BDWP_Database::GetBotDetectOption('botdetect_options');
        if (is_array($options)) {
            update_option('botdetect_options', $options);
        }

        $diagnostics = BDWP_Database::GetBotDetectOption('bdwp_diagnostics');
        if (is_array($diagnostics)) {
            $diagnostics['database_version'] = BDWP_PluginInfo::GetVersion();
            update_option('bdwp_diagnostics', $diagnostics);
        }

        $settings = BDWP_Database::GetBotDetectOption('bdwp_settings');
        if (is_array($settings)) {
            update_option('bdwp_settings', $settings);
        }

        delete_option('botdetect_db_version');
        delete_option('press_btn_save_auto_install');
        BDWP_Database::DeleteBotDetectTable();
    }
    
    // 3.0.Beta1.7 (or prior) => 3.0.Beta3.0
    public static function MigrateTo_3_0_Beta3_0() {
        $options = get_option('botdetect_options');
        if (!is_array($options)) {
            return;
        }

        unset($options['code_length']);
        $options['min_code_length'] = 3;
        $options['max_code_length'] = 5;

        update_option('botdetect_options', $options);
    }

    public static function ResolveBackwardCompatibility() {
        $currentVersion = BDWP_PluginInfo::GetVersion();
        $lastInstalledVersion = self::GetLastInstalledBDWPVersion();
        self::MigrateTo($lastInstalledVersion, $currentVersion);
    }

    public static function UpdateDatabaseAndLastPluginInstallVersions() {
        $diagnostics = get_option('bdwp_diagnostics');
        if (is_array($diagnostics)) {
            $last_plugin_install = array(
                'datetime' => current_time('mysql'),
                'plugin_version' => BDWP_PluginInfo::GetVersion(),
                'wp_version' => BDWP_WordPress::GetWordPressVersion()
            );
            $diagnostics['last_plugin_install'] = $last_plugin_install;
            $diagnostics['database_version'] = BDWP_PluginInfo::GetVersion();
            update_option('bdwp_diagnostics', $diagnostics);
        }
    }

    public static function MigrateTo($p_LastInstalledVersion, $p_CurrentVersion) {
        if ($p_LastInstalledVersion == $p_CurrentVersion) {
            return;
        }

        $migrationApplicable = false;
        foreach (self::PluginVersions() as $v => $f) {
            if ($p_LastInstalledVersion == $v) { 
                $migrationApplicable = true;
                continue;
            }

            if ($migrationApplicable && null != $f) {
                call_user_func(array('BDWP_BackwardCompatibility', $f));
            }
        }

        self::UpdateDatabaseAndLastPluginInstallVersions();
    }

    public static function GetLastInstalledBDWPVersion() {
        // 3.0.Beta3.0 or later
        $diagnostics = get_option('bdwp_diagnostics');
        if (get_option('botdetect_db_version') == '3.0.Beta3.0') {
            $diagnostics = BDWP_Database::GetBotDetectOption('bdwp_diagnostics');
        }

        if (is_array($diagnostics)) {
            return $diagnostics['last_plugin_install']['plugin_version'];
        }

        // 3.0.Beta1.7 or prior
        if (false !== get_option('botdetect_options') && !BDWP_Database::TableExists()) {
            return '3.0.Beta1.7';
        }

        return null;
    }
}
