<?php
class BDWP_ContactForm7 extends BDWP_CaptchaIntegration {

    public function RegisterErrorMessages($p_Messages) {
        return array_merge($p_Messages, array(
            'bdwp_captcha_not_match' => array(
                'description' => __('Please retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha'),
                'default' => __('Please retype the letters under the CAPTCHA image.', 'botdetect-wp-captcha')
            )
        ));
    }
    
    public function RegisterTag() {
        if (version_compare(WPCF7_VERSION, '4.2', '>=')) {
            $this->RegisterTag_4_2();
        } else {
            $this->RegisterTag_4_1();
        }
    }
    
    /**
     * Register the BotDetect CAPTCHA tag on CF7 version 4.2.x or later
     */
    public function RegisterTag_4_2() {
        $tagGenerator = WPCF7_TagGenerator::get_instance();
        $tagGenerator->add('captcha-id', 'BotDetect CAPTCHA', array($this, 'RenderTagPane_4_2'), array('nameless' => 1));
    }
    
    /**
     * Register the BotDetect CAPTCHA tag on CF7 version 4.1.x or prior
     */
    public function RegisterTag_4_1() {

        if (!function_exists('wpcf7_add_tag_generator')) {
            return false;
        }

        wpcf7_add_tag_generator('captcha-id', 'BotDetect CAPTCHA', 'wpcf7_tg_pane_botdetect_wp_captcha', array($this, 'RenderTagPane_4_1'));		
    }

    public function RegisterShortcode() {

        if (!function_exists('wpcf7_add_shortcode')) {
            return false;
        }

        wpcf7_add_shortcode(array('botdetect_captcha', 'botdetect_captcha*'), array($this, 'CaptchaShortcodeHandler'), true);
    }

    public function CaptchaShortcodeHandler($p_Tag) {

        $tag = new WPCF7_Shortcode($p_Tag);

        // captcha options
        $name = $tag->name;
        $captchaId = str_replace('-', '_', $name);
        $userInputId = $name;

        // get input classes
        $classes = $this->GetInputClasses($tag->options);

        $captchaForm = $this->ShowCaptchaForm($captchaId, $userInputId, 
            array(
                'label' => __('Retype the characters', 'botdetect-wp-captcha'),
                'prepend' => '<p>',
                'append' => '</p>'
            ), 
            array(
                'prepend' => '<span class="wpcf7-form-control-wrap ' . $name . '">',
                'append' => '</span>',
                'classes' => $classes
            )
        );

        // show error message when JavaScript is disabled
        $errorMessage = (function_exists('wpcf7_get_validation_error')) ? wpcf7_get_validation_error($name) : '';
        if (!empty($errorMessage)) {
            $error = $this->MakeErrorMessage($errorMessage);
            $captchaForm .= $error;
        }

        return $captchaForm;
    }

    public function GetInputClasses($p_TagOptions = array()) {
        $classes = array();

        if (!empty($p_TagOptions)) {
            foreach ($p_TagOptions as $option) {
                $pattern = '/^(class:)/i';
                if (preg_match($pattern, $option)) {
                    $class = preg_replace($pattern, '', $option);
                    array_push($classes, $class);
                }
            }
        }

        return implode(' ', $classes);
    }

    public function MakeErrorMessage($p_ErrorMessage) {
        $error = sprintf('<span class="wpcf7-not-valid-tip" role="alert">%s</span>', $p_ErrorMessage);
        return $error;
    }

    public function ContactValidate($p_Result, $p_Tag) {

        $tag = new WPCF7_Shortcode($p_Tag);

        $name = $tag->name;
        $captchaId = str_replace('-', '_', $name);
        $userInputId = $name;

        $isHuman = $this->ValidateCaptcha($captchaId, $userInputId);

        if (!$isHuman) {
            if (is_object($p_Result)) {
                $p_Result->invalidate($tag, wpcf7_get_message('bdwp_captcha_not_match'));
            } else {
                // older version
                $p_Result['valid'] = false;
                $p_Result['reason'][$name] = wpcf7_get_message('bdwp_captcha_not_match');
            }
        }

        return $p_Result;
    }

    public function ContactHead() {
        wp_enqueue_style('botdetect-captcha-style');
    }
    
    public function RenderTagPane_4_2($p_ContactForm, $p_Args = '') {
        $args = wp_parse_args( $p_Args, array() );
        $description = __( "For more details, see %s.", 'contact-form-7' );
        $descLink = wpcf7_link( __( 'http://captcha.com/doc/php/howto/wp/contact-form-7-captcha.html', 'contact-form-7' ), __( 'Contact Form 7 CAPTCHA', 'contact-form-7' ) );
?>
	<div class="control-box">
            <fieldset>
                <legend><?php echo sprintf(esc_html($description), $descLink ); ?></legend>

                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
                            <td>
                                <fieldset>
                                    <legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
                                    <label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
                                </fieldset>
                            </td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Captcha Id', 'contact-form-7' ) ); ?></label></th>
                            <td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
                        </tr>

                        <tr>
                            <th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
                            <td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
                        </tr>

                    </tbody>
                </table>
            </fieldset>
	</div>

	<div class="insert-box">
            <input type="text" name="botdetect_captcha" class="tag code" readonly="readonly" onfocus="this.select()" />

            <div class="submitbox">
                <input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
            </div>
	</div>
<?php
    }

    public function RenderTagPane_4_1($p_ContactForm) {
?>
        <div id="wpcf7_tg_pane_botdetect_wp_captcha" class="hidden">
            <div><?php echo esc_html(__( "For more details, see ", 'contact-form-7' )); ?><a href="http://captcha.com/doc/php/howto/wp/contact-form-7-captcha.html" target="_blank"><?php echo esc_html(__( 'Contact Form 7 CAPTCHA', 'contact-form-7' )); ?>.</a></div>
            <form action="">
                <table>
                    <thead>
                        <tr>
                            <td>
                                <label><input type="checkbox" name="required">&nbsp;<?php echo esc_html(__('Required field?', 'contact-form-7')); ?></label>
                            </td>
                        </tr>

                        <tr>
                            <td>
                                <?php echo esc_html(__('Captcha Id', 'contact-form-7')); ?> <br>
                                <input type="text" name="name" class="tg-name oneline">
                            </td>
                        </tr>
                    </thead>

                    <tbody>	
                        <tr>
                            <td>
                                <?php echo esc_html(__('Input field setting', 'contact-form-7')); ?> <br>
                                <code>class</code> (optional)<br>
                                <input type="text" name="class" class="classvalue oneline option">
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="tg-tag">
                    <?php echo esc_html(__('Copy this code and paste it into the form left.', 'contact-form-7')); ?><br>
                    <input type="text" name="botdetect_captcha" class="tag wp-ui-text-highlight code" readonly="readonly" onfocus="this.select()">
                </div>
            </form>
        </div>
<?php
    }

}
