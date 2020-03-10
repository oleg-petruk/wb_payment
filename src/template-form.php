<div id="wb-modal-payment" style="display: none">
    <div class="wb-modal-inner">
        <span id="close-wb-form"></span>
        <form id="wb-payment-form" name="pay" method="post" action="https://shop.westernbid.info">
            <input type="hidden" name="charset" value="utf-8">
            <input type="hidden" name="wb_login" value="<?php echo $wb_login?>">
            <input type="hidden" name="wb_hash" value="<?php echo $wb_hash?>">
            <input type="hidden" name="invoice" value="<?php echo $invoice?>">
            <h2 class=""><?php _e('Payment by paypal', 'westernbid');?></h2>
            <input name="first_name" value="" placeholder="<?php _e('First name:', 'westernbid');?>" required>
            <input name="email" value="" placeholder="<?php _e('Email:', 'westernbid');?>" required>
            <input name="phone" value="" placeholder="<?php _e('Telephone number:', 'westernbid');?>" required>
            <input type="hidden" name="last_name" value="">
            <input type="hidden" name="address1" value="">
            <input type="hidden" name="address2" value="">
            <input type="hidden" name="country" value="">
            <input type="hidden" name="city" value="">
            <input type="hidden" name="state" value="">
            <input type="hidden" name="zip" value="">
            <input type="hidden" name="item_name" value="<?php echo $product ?>">
            <input type="hidden" name="amount" value="<?php echo $total ?>">
            <input type="hidden" name="shipping" value="">
            <input type="hidden" name="currency_code" value="USD">
            <input type="hidden" name="item_name_1" value="<?php echo $product ?>">
            <input type="hidden" name="item_number_1" value="1">
            <input type="hidden" name="url_1" value="<?php echo get_page_link(); ?>">
            <input type="hidden" name="description_1" value="<?php echo $product_desc ?>">
            <input type="hidden" name="amount_1" value="<?php echo $total ?>">
            <input type="hidden" name="quantity_1" value="1">
            <input type="hidden" name="return" value="<?php echo get_page_link(); ?>/?payment=success">
            <input type="hidden" name="cancel_return" value="<?php echo get_page_link(); ?>/?payment=fail">
            <input type="hidden" name="notify_url" value="<?php echo get_page_link(); ?>/?payment=notify">

            <button class="wb-pay-btn" type="submit"><?php _e('Process', 'westernbid');?></button>
        </form>
    </div>
</div>
<script>
    jQuery(function ($) {
        $('#close-wb-form').on('click', function(e) {
            $('#wb-modal-payment').hide();
        });
    });
</script>