{extends file="parent:frontend/account/order_item_details.tpl"}

    {block name='frontend_account_order_item_repeat_order' append}

            {* PayPal Button *}
            <div class="order--repeat panel--tr">
                <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="">
                    <input type="hidden" name="lc" value="DE">
                    <input type="hidden" name="item_name" value="Bestellnummer:{$offerPosition.ordernumber}">
                    <input type="hidden" name="amount" value="{$offerPosition.invoice_amount|replace:',':'%2e'}">
                    <input type="hidden" name="currency_code" value="{$offerPosition.currency}">
                    <input type="hidden" name="button_subtype" value="services">
                    <input type="hidden" name="no_note" value="0">
                    <input type="hidden" name="no_shipping" value="1">
                    <input type="hidden" name="bn" value="PP-BuyNowBF:btn_paynowCC_LG.gif:NonHosted">
                    <input type="image" src="https://www.paypalobjects.com/de_DE/DE/i/btn/btn_paynowCC_LG.gif" border="0" name="submit" alt="Jetzt einfach, schnell und sicher online bezahlen – mit PayPal.">
                    <img alt="" border="0" src="https://www.paypalobjects.com/de_DE/i/scr/pixel.gif" width="1" height="1">
                </form>
            </div>
            
    {/block}
