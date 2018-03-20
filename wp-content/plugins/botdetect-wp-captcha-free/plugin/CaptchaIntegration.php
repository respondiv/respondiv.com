<?php
class BDWP_CaptchaIntegration {

    public function ResetCaptcha($p_CaptchaId = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode') {
        if (class_exists('Captcha')) {
            $captcha = &$this->InitCaptcha($p_CaptchaId, $p_UserInputId);
            $captcha->Reset();
        }
    }

    public function &InitCaptcha($p_CaptchaId = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode') {
        $captcha = new Captcha($p_CaptchaId);
        $captcha->UserInputId = $p_UserInputId;
        return $captcha;
    }

    public function ValidateCaptcha($p_CaptchaId = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode') {
        $captcha = &$this->InitCaptcha($p_CaptchaId, $p_UserInputId);

        $UserInput = $_POST[$p_UserInputId];
        $isHuman = $captcha->Validate($UserInput);

        return $isHuman;
    }

    public function GetCaptchaForm($p_CaptchaId = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode', $p_InputFieldOptions = array()) {
        $captcha = &$this->InitCaptcha($p_CaptchaId, $p_UserInputId);

        $output = $captcha->Html();

        if (empty($p_InputFieldOptions)) {
            $output .= '<input name="' . $p_UserInputId . '" type="text" id="' . $p_UserInputId . '" class="bdwp_user_input" />';
        } else {

            if (array_key_exists('prepend', $p_InputFieldOptions)) {
                $output .= $p_InputFieldOptions['prepend'];
            }

            $classes = (array_key_exists('classes', $p_InputFieldOptions)) ? $p_InputFieldOptions['classes'] : '';

            $output .= '<input name="' . $p_UserInputId . '" type="text" id="' . $p_UserInputId . '" class="bdwp_user_input ' . $classes . '" />';

            if (array_key_exists('append', $p_InputFieldOptions)) {
                $output .= $p_InputFieldOptions['append'];
            }
        }

        return $output;
    }

    public function ShowCaptchaForm($p_CaptchaId = 'BotDetectCaptcha', $p_UserInputId = 'CaptchaCode', $p_Options = array(), $p_InputFieldOptions = array()) {
        $elements = array();
        $elements[] = $this->GetCaptchaForm($p_CaptchaId, $p_UserInputId, $p_InputFieldOptions);

        if (isset($p_Options) && count($p_Options) != 0 && isset($p_Options[0])) {

            if (array_key_exists('label', $p_Options)){
                array_unshift($elements, '<label for="' . $p_UserInputId. '">' . $p_Options['label']. '</label>');
            }

            if (array_key_exists('prepend', $p_Options)){
                array_unshift($elements, $p_Options['prepend']);
            }

            if (array_key_exists('append', $p_Options)){
                $elements[] = $p_Options['append'];
            }
        }
        return implode('', $elements);
    }
}
