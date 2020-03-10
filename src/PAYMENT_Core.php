<?php

class PAYMENT_Core
{
    public $settings_page = 'pp_wb_settings';

    public function __construct()
    {
        $this->_setHooks();
        $this->_addStyles();
    }

    private function _setHooks()
    {
        add_action('admin_menu', [$this, 'addSettingsPage']);
        add_action( 'admin_init', [$this, 'registerOptions'] );
        add_shortcode('westernbid', [$this, 'westernBidBtn']);
        wp_enqueue_script('jquery');
        add_action( 'wp_footer', [$this, 'westernBidForm'], 90 );
    }

    private function _addStyles()
    {
        add_action('wp_enqueue_scripts', [$this, 'addPublicStyles'], 902);
    }

    public function addPublicStyles()
    {
        wp_enqueue_style('wb_payments', plugins_url('westernbid-payments/assets/css/style.css'), [], PLUGIN_VERSION);
    }

    public function addSettingsPage()
    {
        add_options_page(
            'WesternBid',
            'WesternBid',
            'manage_options',
            $this->settings_page,
            [$this, 'option_page']
        );

    }

    public function option_page()
    {
        ?><div class="wrap">
        <h2><?php _e('WesternBid setting for Paypal payments', 'westernbid');?></h2>
        <form method="POST" enctype="multipart/form-data" action="options.php">
            <?php
            settings_fields('westernbid_options');
            do_settings_sections($this->settings_page);
            submit_button();
            ?>
        </form>
        </div><?php
    }

    public function registerOptions()
    {
        register_setting( 'westernbid_options', 'wb_settings', '' );
        add_settings_section( 'westernbid_settings', __('WesternBid settings', 'westernbid'), '', $this->settings_page );

        add_settings_field(
            'wb_login',
            'WesternBid login',
            [$this, 'wb_login_field'],
            $this->settings_page,
            'westernbid_settings'
        );

        add_settings_field(
            'secret_key',
            'WesternBid secret key',
            [$this, 'wb_secret_key_field'],
            $this->settings_page,
            'westernbid_settings'
        );

        add_settings_field(
            'wb_total',
            'Amount',
            [$this, 'wb_total_field'],
            $this->settings_page,
            'westernbid_settings'
        );

        add_settings_field(
            'wb_product',
            'Product name',
            [$this, 'wb_product_field'],
            $this->settings_page,
            'westernbid_settings'
        );

        add_settings_field(
            'wb_product_desc',
            'Product description',
            [$this, 'wb_product_desc_field'],
            $this->settings_page,
            'westernbid_settings'
        );

        add_settings_section( 'messages_settings', __('Messages', 'westernbid'), '', $this->settings_page );

        add_settings_field(
            'success',
            'Success message',
            [$this, 'wb_success_field'],
            $this->settings_page,
            'messages_settings'
        );

        add_settings_field(
            'error',
            'Error message',
            [$this, 'wb_error_field'],
            $this->settings_page,
            'messages_settings'
        );
    }

    public function wb_login_field()
    {
        $val = get_option('wb_settings');
        $val = $val ? $val['wb_login'] : null;
        ?>
        <input type="text" name="wb_settings[wb_login]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public function wb_secret_key_field()
    {
        $val = get_option('wb_settings');
        $val = $val ? $val['secret_key'] : null;
        ?>
        <input type="text" name="wb_settings[secret_key]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public function wb_total_field()
    {
        $val = get_option('wb_settings');
        $val = $val ? $val['wb_total'] : null;
        ?>
        <input type="text" name="wb_settings[wb_total]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public function wb_product_field()
    {
        $val = get_option('wb_settings');
        $val = $val ? $val['wb_product'] : null;
        ?>
        <input type="text" name="wb_settings[wb_product]" value="<?php echo esc_attr( $val ) ?>" />
        <?php
    }

    public function wb_product_desc_field()
    {
        $val = get_option('wb_settings');
        $val = $val ? $val['wb_product_desc'] : null;
        ?>
        <textarea class='code large-text' cols='50' rows='6' type='text' id='$id' name="wb_settings[wb_product_desc]"><?php echo esc_attr( $val ) ?></textarea>
        <?php
    }

    public function wb_success_field()
    {
        $val = get_option('wb_settings');
        $val = $val ? $val['success'] : null;
        ?>
        <textarea class='code large-text' cols='50' rows='6' type='text' id='$id' name="wb_settings[success]"><?php echo esc_attr( $val ) ?></textarea>
        <?php
    }

    public function wb_error_field()
    {
        $val = get_option('wb_settings');
        $val = $val ? $val['error'] : null;
        ?>
        <textarea class='code large-text' cols='50' rows='6' type='text' id='$id' name="wb_settings[error]"><?php echo esc_attr( $val ) ?></textarea>
        <?php
    }

    public function westernBidBtn($atts, $content)
    {
        $amount = $atts && $atts['amount']? $atts['amount'] : '123';
        $class = $atts && $atts['class']? $atts['class'] : '';
        $html = <<<HTML
            <a href='#' class='westernbid-link {$class}' data-amount='{$amount}'>{$content}</a>
            <script type="text/javascript">
                jQuery(function ($) {
                    $('a[data-amount={$amount}]').on('click', function(e) {
                        e.preventDefault();
                        $('#wb-modal-payment').show();
                    });
                });
            </script>
HTML;
        return $html;
    }

    public function westernBidForm()
    {
        $wb_login = get_option('wb_settings')['wb_login'];
        $secret_key = get_option('wb_settings')['secret_key'];
        $invoice = $wb_login.'-'.$today = date("YmdHis");  //uniq order id
        $total = get_option('wb_settings')['wb_total'];
        $product = get_option('wb_settings')['wb_product'];
        $product_desc = get_option('wb_settings')['wb_product_desc'];
        $wb_hash = md5($wb_login.$secret_key.$total.$invoice);

        if(isset($_GET['payment']) && $_GET['payment'] == 'success')
        {
            ?>
            <div id="wb-modal-payment">
                <div class="wb-modal-inner">
                    <h2 class="payment-success"><?php echo get_option('wb_settings')['success']; ?></h2>
                    <a class="close-payment-info" href="<?php echo get_page_uri(); ?>"><?php _e('Close', 'westernbid')?></a>
                </div>
            </div>
            <?php
        }

        elseif(isset($_GET['payment']) && $_GET['payment'] == 'fail')
        {
            ?>
            <div id="wb-modal-payment">
                <div class="wb-modal-inner">
                    <h2 class="payment-fail"><?php echo get_option('wb_settings')['error']; ?></h2>
                    <a class="close-payment-info" href="<?php echo get_page_uri(); ?>"><?php _e('Close', 'westernbid')?></a>
                </div>
            </div>
            <?php
        }

        elseif(isset($_GET['payment']) && $_GET['payment'] == 'notify')
        {

            if(isset($_POST['wb_hash'])) {

                $sHash = $_POST['wb_hash'];
                $wb_result = $_POST['wb_result'];
                $mc_gross = $_POST['mc_gross'];
                $invoice = $_POST['invoice'];
                $status = $_POST['payment_status'];
                $user_email = $_POST['payer_email'];
                $transaction_id = $_POST['transaction_id'];

                $str_md5 = md5($wb_login . $wb_result . $secret_key . $mc_gross . $invoice);

                if (mb_strtoupper($sHash) == mb_strtoupper($str_md5))
                {
                    $to = get_option('admin_email');
                    $subject = 'Payment notification from mpx90pin.com';
                    $message = sprintf("Товар оплачен через WesternBid.\nНомер заказа: %s\nСтатус платежа в системе PayPal: %s\nE-mail плательщика: %s\nСумма: %s\nID транзакции: %s\n", $invoice, $status, $user_email, $mc_gross, $transaction_id);

                    wp_mail($to, $subject, $message);
                }
            }
        }

        else {
            include_once( plugin_dir_path(__FILE__) .'template-form.php');
        }

    }
    public static function install()
    {
    }

    public static function uninstall()
    {
    }
}
