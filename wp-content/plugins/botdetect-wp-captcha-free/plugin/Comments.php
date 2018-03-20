<?php
class BDWP_Comments extends BDWP_CaptchaIntegration {

    private $m_CaptchaId = 'comments_captcha';
    private $m_UserInputId = 'comments_captcha_field';

    public function CommentForm() {
        echo $this->ShowCaptchaForm($this->m_CaptchaId, $this->m_UserInputId, array(
            'label' => __('Retype the characters', 'BotDetect'),
            'prepend' => '<p>',
            'append' => '</p>'
        ));
    }
	
    public function CommentValidate() {
        if ($_POST) {
            $isHuman = $this->ValidateCaptcha($this->m_CaptchaId, $this->m_UserInputId);
            if (!$isHuman) {
                wp_die( __('<strong>ERROR</strong>: Please browser\'s back button and retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha'), 'BotDetect');
            }
        }

            // Possible alternative to wp_die();
            // $location = empty($_POST['redirect_to']) ? get_comment_link($comment_id) : $_POST['redirect_to'] . '#comment-' . $comment_id;
            // $location = apply_filters('comment_post_redirect', $location, $comment);

            // wp_safe_redirect( $location );
            // exit;
    }

    public function CommentHead() {
        wp_enqueue_style('botdetect-captcha-style');
    }

    public function CommentReset() {
        $this->ResetCaptcha($this->m_CaptchaId, $this->m_UserInputId);
    }
}
