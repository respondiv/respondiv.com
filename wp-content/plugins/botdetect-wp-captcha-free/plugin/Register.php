<?php
class BDWP_Register extends BDWP_CaptchaIntegration {

    private $m_CaptchaId = 'register_captcha';
    private $m_UserInputId = 'register_captcha_field';

    public function RegisterForm() {
        echo $this->ShowCaptchaForm($this->m_CaptchaId, $this->m_UserInputId);
    }

    public function RegisterValidation($p_Error) {
        if ($_POST) {
            $isHuman = $this->ValidateCaptcha($this->m_CaptchaId, $this->m_UserInputId);
            if (!$isHuman) {
                if (!is_wp_error($p_Error)) {
                    $p_Error = new WP_Error();
                }

                $p_Error->add('captcha_fail', __('<strong>ERROR</strong>: Please retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha'), 'BotDetect');
                return $p_Error;
            } else {
                $this->ResetCaptcha($this->m_CaptchaId, $this->m_UserInputId);
                return $p_Error;
            }
        }
    }
}
