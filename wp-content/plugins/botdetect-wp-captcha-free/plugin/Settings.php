<?php
class BDWP_Settings {

    public $Options;
    private $m_IsRegisteredUser = false;

    public function __construct($p_Options = array()) {
        $this->Options = $p_Options;
        $this->m_IsRegisteredUser = BDWP_InstallCaptchaProvider::IsRegisteredUser();
    }

    public function GetOptionValue($p_FieldName) {
        $options = $this->Options;
    	return (isset($options[$p_FieldName])) ? $options[$p_FieldName] : '';
    }

    public function CanBeCheckedInput($p_FieldName, $p_FieldValue) {
    	$options = $this->Options;
    	if (isset($options[$p_FieldName])) {
            return checked($options[$p_FieldName], $p_FieldValue); 
    	}
    }

    public function CanBeDisabledInput() {
    	return (!$this->m_IsRegisteredUser) ? 'disabled' : '';
    }

    public function RenderSettings() {
        ?>
        <div class="wrap">
            <div class="icon32" id="icon-options-general"><br></div>
            <h2><?php printf(__('BotDetect CAPTCHA WordPress Plugin (%s) -- %s', 'botdetect-wp-captcha'), BDWP_PluginInfo::GetVersion(), BDWP_PluginInfo::License());?></h2>
            <hr><p class="bdwp_botdetect_license"><?php _e('The BotDetect Captcha WordPress Plugin is released under the \'BotDetect Captcha WordPress Plugin -- FREE\' license.<br>The Plugin is packaged with and dependent of the BotDetect PHP CAPTCHA library which is licensed under the BotDetect PHP CAPTCHA 3.0 Beta3 End User License Agreement.<br>In order to use the Plugin you have to accept the both licenses.', 'botdetect-wp-captcha'); ?></p>
        <?php 
            // show register user form and captcha options
            $this->RenderUserRegisterForm(); 
            $this->RenderCaptchaOptions();
        ?>
        </div>
        <?php
    }

    public function RenderIntegrationOptions() {
        ?>
        <tr valign="top">
            <th scope="row"><?php _e('Use BotDetect CAPTCHA with', 'botdetect-wp-captcha'); ?></th>
            <td>
                <label><input name="botdetect_options[on_login]" type="checkbox" <?php echo $this->CanBeDisabledInput(); ?> value="true" <?php $this->CanBeCheckedInput('on_login', true); ?> /> <?php _e('Login', 'botdetect-wp-captcha'); ?> </label><br>
                <label><input name="botdetect_options[on_registration]" type="checkbox" <?php echo $this->CanBeDisabledInput(); ?> value="true" <?php $this->CanBeCheckedInput('on_registration', true); ?> /> <?php _e('User Registration', 'botdetect-wp-captcha'); ?> </label><br>
                <label><input name="botdetect_options[on_lost_password]" type="checkbox" <?php echo $this->CanBeDisabledInput(); ?> value="true" <?php $this->CanBeCheckedInput('on_lost_password', true); ?> /> <?php _e('Lost Password', 'botdetect-wp-captcha'); ?> </label><br>
                <label><input name="botdetect_options[on_comments]" type="checkbox" <?php echo $this->CanBeDisabledInput(); ?> value="true" <?php $this->CanBeCheckedInput('on_comments', true); ?> /> <?php _e('Wordpress Comments', 'botdetect-wp-captcha'); ?> </label><br>
                <label><input name="botdetect_options[on_contact_form7]" type="checkbox" <?php echo $this->CanBeDisabledInput(); ?> value="true" <?php $this->CanBeCheckedInput('on_contact_form7', true); ?> /> <?php _e('Contact Form 7', 'botdetect-wp-captcha'); ?> </label><br>
            </td>
        </tr>
        <?php
    }

    public function RenderCaptchaOptions() {
        $isFree = (class_exists('Captcha') && Captcha::IsFree()); 
        ?>
        <form method="post" action="options.php">
            <?php settings_fields('botdetect_plugin_options'); ?>
            <?php $options = $this->Options; ?>

            <input type="hidden" name="botdetect_options[library_path]" value="<?php echo $this->GetOptionValue('library_path'); ?>" />
            <input type="hidden" name="botdetect_options[library_assets_url]" value="<?php echo $this->GetOptionValue('library_assets_url'); ?>" />

            <table class="form-table">

                <tr valign="top" >            
                    <th scope="row" colspan="2"><h3><?php _e('Plugin settings', 'botdetect-wp-captcha'); ?></h3></th>
                </tr>

                <?php $this->RenderIntegrationOptions(); // show integration options ?>

                <tr>
                    <th scope="row"><?php _e('Captcha image width', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <input type="text" style="width: 59px" <?php echo $this->CanBeDisabledInput(); ?> name="botdetect_options[image_width]" value="<?php echo $this->GetOptionValue('image_width'); ?>" />
                        <span style="color:#666666">px</span>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Captcha image height', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <input type="text" size="3" style="width: 59px" <?php echo $this->CanBeDisabledInput(); ?> name="botdetect_options[image_height]" value="<?php echo $this->GetOptionValue('image_height'); ?>" />
                        <span style="color:#666666">px</span>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Number of characters', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <input type="text" size="3" <?php echo $this->CanBeDisabledInput(); ?> id="min_code_length" name="botdetect_options[min_code_length]" value="<?php echo $this->GetOptionValue('min_code_length'); ?>" /> &ndash;
                        <input type="text" size="3" <?php echo $this->CanBeDisabledInput(); ?> id="max_code_length" name="botdetect_options[max_code_length]" value="<?php echo $this->GetOptionValue('max_code_length'); ?>" />
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Sound', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <label><input name="botdetect_options[audio]" type="checkbox" <?php echo $this->CanBeDisabledInput(); ?> value="true" <?php $this->CanBeCheckedInput('audio', true); ?> /> <?php _e('Enable audio Captcha', 'botdetect-wp-captcha'); ?></label>
                    </td>
                </tr>

                <tr>
                    <th scope="row"><?php _e('Remote Include', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <label>
                            <?php $canBeRemoteDisabled = ((!class_exists('Captcha') && !$isFree) || $isFree) ? 'disabled' : ''; ?>
                            <input name="botdetect_options[remote]" type="checkbox" <?php echo $canBeRemoteDisabled; ?> value="true" <?php $this->CanBeCheckedInput('remote', true); ?> />
                                                        <?php _e('Enable Remote Include -- used for statistics collection and proof-of-work confirmation (still work in progress)','botdetect-wp-captcha'); ?> <br>
                            <i><?php _e('Switching off is disabled with the Free version of BotDetect.', 'botdetect-wp-captcha'); ?></i>
                        </label>
                    </td>
                </tr>

                <tr valign="top">
                    <th scope="row"><?php _e('Help link', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <label><input name="botdetect_options[helplink]" type="radio" <?php echo $this->CanBeDisabledInput(); ?> value="image" <?php $this->CanBeCheckedInput('helplink', 'image'); ?> /> <?php _e('Image', 'botdetect-wp-captcha'); ?> <span style="color:#666666; margin-left:42px;"><?php _e('Clicking the Captcha image opens the help page in a new browser tab.', 'botdetect-wp-captcha'); ?></span></label><br>
                        <label><input name="botdetect_options[helplink]" type="radio" <?php echo $this->CanBeDisabledInput(); ?> value="text" <?php $this->CanBeCheckedInput('helplink', 'text'); ?> /> <?php _e('Text', 'botdetect-wp-captcha'); ?> <span style="color:#666666; margin-left:56px;"><?php _e('A text link to the help page is rendered in the bottom 10 px of the Captcha image.', 'botdetect-wp-captcha'); ?></span></label><br>
                        <label>
                            <?php $canBeHelpLinkOffDisabled = ((!class_exists('Captcha') && !$isFree) || $isFree) ? 'disabled' : ''; ?>
                            <input name="botdetect_options[helplink]" type="radio" <?php echo $canBeHelpLinkOffDisabled; ?> value="off" <?php $this->CanBeCheckedInput('helplink', 'off'); ?> /> <?php _e('Off', 'botdetect-wp-captcha'); ?> 
                            <span style="color:#666666; margin-left:63px;"><?php echo ($isFree) ? __('<i>Not available with the Free version of BotDetect.</i>', 'botdetect-wp-captcha') : __('Help link is disabled.', 'botdetect-wp-captcha'); ?></span>
                        </label>
                    </td>
                </tr>

                <tr>
                    <td colspan = "2">
                        <p><?php printf(__('Additionally: Please note almost everything is customizable by editing BotDetect\'s <a href="%scaptcha.com/doc/php/howto/captcha-configuration.html?utm_source=plugin&amp;utm_medium=wp&amp;utm_campaign=%s" target="_blank">configuration file</a>.', 'botdetect-wp-captcha'), BDWP_HttpHelpers::GetProtocol(), BDWP_PluginInfo::GetVersion()); ?></p>
                    </td>
                </tr>

                <tr><td colspan="2"><hr></td></tr>
                <tr valign="top">
                    <th scope="row"><?php _e('Misc Options', 'botdetect-wp-captcha'); ?></th>
                    <td>
                        <label><input name="botdetect_options[chk_default_options_db]" type="checkbox" <?php echo $this->CanBeDisabledInput(); ?> value="true" <?php $this->CanBeCheckedInput('chk_default_options_db', true); ?> /> <?php _e(' Reset plugin settings to default values on \'Save Changes\'.', 'botdetect-wp-captcha'); ?></label>
                    </td>
                </tr>
            </table>
            <?php submit_button(__('Save Changes', 'botdetect-wp-captcha'), 'primary', 'bdwp_button_save_changes', true, $this->CanBeDisabledInput()); ?>
        </form>
    <?php
    }


    public function RenderUserRegisterForm() {
        $canBeHiddenForm = ($this->m_IsRegisteredUser) ? 'bdwp_hidden_user_register_container' : '';
        ?>
        <div id="bdwp_user_register_container" class="<?php echo $canBeHiddenForm; ?>">
            <form id="bdwp_user_register_form">
                <input type="hidden" id="bdwp_captcha_image_url" value="<?php echo network_site_url('/');?>index.php?botdetect_request=1&amp;get=image&amp;c=login_captcha&amp;t=b2b58ace629f27c13f648b94111493ee" >
                <input type="hidden" id="bdwp_plugin_dir_url" value="<?php echo plugin_dir_url(__FILE__);?>">
                <input type="hidden" id="bdwp_error_captcha_image_message" value="<?php _e('An error occurred while generating the Captcha image. Captcha validation has been disabled in login form to avoid locking all users out of the website. <br>This error may be caused by a third-party plugin or third-party theme.','botdetect-wp-captcha'); ?>">
                <input type="hidden" id="bdwp_error_invalid_email_message" value="<?php _e('Please enter a valid email address.','botdetect-wp-captcha'); ?>">
                <input type="hidden" id="bdwp_error_itheme_security_blocked" value="<?php _e('An error occurred while generating the Captcha image. Captcha validation has been disabled in login form to avoid locking all users out of the website. Please turn off the \'Filter Suspicious Query Strings in the URL\' setting in iThemes Sercurity plugin settings.','botdetect-wp-captcha'); ?>">
                <input type="hidden" id="bdwp_error_sessions_disabled" value="<?php _e('PHP Sessions are disabled on your server, and Captcha validation in any form cannot work until you (or your administrator) enable them. Captcha validation has been disabled in login form to avoid locking all users out of the website.','botdetect-wp-captcha'); ?>">
                <input type="hidden" id="bdwp_error_network" value="<?php _e('Error occured while registering. This problem may be due to network-related issues. Please try again.','botdetect-wp-captcha'); ?>">
                <input type="hidden" id="bdwp_loading_message_1" value="<?php _e('Checking render captcha image', 'botdetect-wp-captcha'); ?>">
                <input type="hidden" id="bdwp_loading_message_2" value="<?php _e('Working', 'botdetect-wp-captcha'); ?>">

                <input type="text" size="40" class="bdwp_input_text" name="bdwp_customer_email" id="bdwp_customer_email" placeholder="<?php _e('Enter your email', 'botdetect-wp-captcha'); ?>" >
                
                <p><?php _e('We need your email to reference your deployment in our database. We will use it to inform you about security updates, new features, etc. We will never give your email to third parties, and you can easily unsubscribe (from our rare mailings) at any time.', 'botdetect-wp-captcha'); ?></p>
                <p class="button-primary" id="bdwp_button_user_register"><?php _e('Register as a plugin user', 'botdetect-wp-captcha'); ?></p>
            </form>

            <p class="bdwp_loading"></p>
            <p class="bdwp_error_message"></p>
        </div>
    <?php
    }

}
