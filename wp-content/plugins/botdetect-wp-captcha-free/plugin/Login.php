<?php
class BDWP_Login extends BDWP_CaptchaIntegration {

    private $m_CaptchaId = 'login_captcha';
    private $m_UserInputId = 'login_captcha_field';

    public function LoginForm() {
        echo $this->ShowCaptchaForm($this->m_CaptchaId, $this->m_UserInputId);
    }

    public function LoginValidate($p_User) {
        if ($_POST) {
            $isHuman = $this->ValidateCaptcha($this->m_CaptchaId, $this->m_UserInputId);
            if (!$isHuman) {
                if (!is_wp_error($p_User)) {
                    $p_User = new WP_Error();
                }

                $p_User->add('captcha_fail', __('<strong>ERROR</strong>: Please retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha'), 'BotDetect');
                remove_action('authenticate', 'wp_authenticate_username_password', 20);
                return $p_User;
            }
        }
    }

    public function LoginReset() {
        $this->ResetCaptcha($this->m_CaptchaId, $this->m_UserInputId);
    }

    public function LoginHead() {
        wp_enqueue_style('botdetect-captcha-style');
    }
}
