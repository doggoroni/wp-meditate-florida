<?php
// no direct access
defined('ABSPATH') || die();

/** @var LSD_Payments_Gateways_Paypal $this */
/** @var LSD_Cart $cart */

$nonce = wp_create_nonce('lsd_checkout');
$options = $this->options();
$currency = LSD_Options::currency();
$cart = new LSD_Cart();
[$total, $discount, $tax] = $cart->apply_coupon();
$items = $cart->get_items();
$order_title = wp_date('Y-m-d H:i:s');
if ($items)
{
    $first = reset($items);
    $plan_id = (int)($first['plan_id'] ?? 0);
    $tier_id = $first['tier_id'] ?? '';
    $plan = new LSD_Payments_Plan($plan_id, $tier_id);
    $plan_name = get_the_title($plan_id);
    $tier = $plan->get_tier();
    $tier_name = $tier ? $tier->get_name() : '';

    $order_title = $plan_name;
    if ($tier_name) $order_title .= ' - ' . $tier_name;
}
?>
<div class="lsd-gateway-wrapper">
    <div class="lsd-fe-section-heading">
        <div class="lsd-gateway-name lsd-fe-title"><?php echo esc_html($this->name()); ?></div>
        <?php if ($this->comment()): ?>
            <div class="lsd-gateway-comment lsd-fe-description"><?php echo LSD_Kses::element($this->comment()); ?></div>
        <?php endif; ?>
    </div>
    <?php echo LSD_Kses::form($this->form_checkout_user()); ?>

    <?php echo LSD_Privacy::consent_field([
        'id' => 'lsd_checkout_consent_' . uniqid(),
        'name' => 'lsd_checkout_consent',
        'class' => 'lsd-privacy-consent-checkbox lsd-checkout-consent-input',
        'wrapper_class' => 'lsd-checkout-consent',
        'context' => 'checkout',
        'required_message' => LSD_Privacy::consent_required_text(),
    ]); ?>

    <div id="lsd-paypal-button-container"></div>
    <div class="lsd-checkout-response"></div>
</div>
<script src="https://www.paypal.com/sdk/js?client-id=<?php echo esc_attr($options['client_id'] ?? ''); ?>&currency=<?php echo esc_attr($currency); ?>" async></script>
<script>
(function ($)
{
    const lsdPaypalInterval = setInterval(function ()
    {
        if (typeof paypal === 'undefined') return;

        clearInterval(lsdPaypalInterval);

        const $wrapper = $('#lsd-gateway-form-paypal');
        const validateConsent = function ()
        {
            const consentEl = $wrapper.find('input[name="lsd_checkout_consent"]').get(0);
            if (!consentEl || !consentEl.required) return true;

            if (!consentEl.checked)
            {
                if (typeof consentEl.reportValidity === 'function')
                {
                    consentEl.reportValidity();
                }
                else
                {
                    consentEl.focus();
                }

                return false;
            }

            consentEl.setCustomValidity('');
            return true;
        };

        paypal.Buttons({
            style: {
                disableMaxWidth: true
            },
            onClick: function (data, actions)
            {
                if (!validateConsent())
                {
                    return actions && typeof actions.reject === 'function' ? actions.reject() : false;
                }

                return actions && typeof actions.resolve === 'function' ? actions.resolve() : true;
            },
            createOrder: function (data, actions)
            {
                return actions.order.create({
                    purchase_units: [{
                        description: '<?php echo esc_js($order_title); ?>',
                        amount: {
                            value: '<?php echo esc_js(number_format($total, 2, '.', '')); ?>',
                            currency_code: '<?php echo esc_js($currency); ?>'
                        }
                    }]
                });
            },
            onApprove: function (data, actions)
            {
                return actions.order.capture().then(function (orderData)
                {
                    if (orderData.status === 'COMPLETED')
                    {
                        const name = $wrapper.find('.lsd-checkout-user-name').val() || '';
                        const email = $wrapper.find('.lsd-checkout-user-email').val() || '';

                        $.ajax({
                            url: lsd.ajaxurl,
                            type: 'post',
                            dataType: 'json',
                            data: {
                                action: 'lsd_checkout',
                                _wpnonce: '<?php echo esc_js($nonce); ?>',
                                gateway: '<?php echo esc_js($this->key()); ?>',
                                paypal_order_id: orderData.id,
                                name: name,
                                email: email
                            },
                            success: function (res)
                            {
                                if (res && res.success)
                                {
                                    lsdCheckoutComplete(res.key ? res.key : res.order_id);
                                }
                                else if (res && res.message)
                                {
                                    $wrapper.find('.lsd-checkout-response').html(res.message);
                                }
                            }
                        });
                    }
                });
            }
        }).render('#lsd-paypal-button-container');
    }, 100);
})(jQuery);
</script>
