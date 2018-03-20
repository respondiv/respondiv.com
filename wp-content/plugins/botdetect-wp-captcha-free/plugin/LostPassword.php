<?php
class BDWP_LostPassword extends BDWP_CaptchaIntegration {

    private $m_CaptchaId = 'lost_password_captcha';
    private $m_UserInputId = 'lost_password_captcha_field';

    public function LostPasswordForm() {
        echo $this->ShowCaptchaForm($this->m_CaptchaId, $this->m_UserInputId);
    }

    public function LostPasswordValidate() {
        if ($_POST) {
            $isHuman = $this->ValidateCaptcha($this->m_CaptchaId, $this->m_UserInputId);
            if (!$isHuman) {
                wp_die(__('<strong>ERROR</strong>: Please browser\'s back button and retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha'), 'BotDetect');
            } else {
                $this->ResetCaptcha($this->m_CaptchaId, $this->m_UserInputId);
            }
        }
    }
}
