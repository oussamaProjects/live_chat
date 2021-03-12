{**
*
* L'nk Live Promo Module
*
* @author Abdelakbir el Ghazouani - By L'nkboot.fr
* @copyright 2016-2020 Abdelakbir el Ghazouani - By L'nkboot.fr (http://www.lpboot.fr)
* @license Commercial license see license.txt
* @category Module
* Support by mail : contact@lpboot.fr
*
*}

<div class="lnk_lp">

    {* MAIN CHAT *}
    <div class="lp_side">

        <div class="close">x</div>

        <div class="lp_head_2">
            <div class="lp_icons text-right">
                <span class="lp-label orders">{$order_number}</span>
                <span class="lp-label carts">{$cart_number}</span>
                {if Configuration::get('LP_customer_connected')}
                    <span class="lp-label users">{l s='Live' mod='lnk_livepromo'}
                        <span>{$customer_connected}</span>
                    </span>
                {/if}
            </div>
        </div>

        <div class="lp_feature_product shine">
            <div class="lp_feature_product_container">
                {if Configuration::get('LP_customer_connected')}
                    <span class="lp-label users">
                        {l s='Live' mod='lnk_livepromo'}
                        <span>{$customer_connected}</span>
                    </span>
                {/if}
                <div class="lp_feature_product_image">
                    <div class="image" style="background: url('{$lp_urlmedia}/11010.jpg') no-repeat 50% 0% / cover;"></div>
                </div>
                <div class="lp_feature_product_title">
                    <div class="lp_head">
                        <div class="lp_icons text-right">
                            <span class="lp-label orders">{$order_number}</span>
                            <span class="lp-label carts">{$cart_number}</span>
                        </div>
                    </div>
                    <div class="lp_feature_product_title_text">
                        <a href="#" class="lp-label day_product"> Produit du jour </a>
                        <h3>Pull col v tombant très doux en beige coupe chauve souris</h3>
                    </div>
                </div>
            </div>
        </div>

        <div class="lp_body_container">

            <div class="effect-stars stars"></div>
            <div class="effect-stars twinkling"></div>
            <div class="effect-stars clouds"></div>

            <div class="lp_body_scroll">
                <div class="lp_body">
                    <div class="lp_body_content">
                        <div class="lp_commentes_container">
                            <div class="lp_commentes">
                                <div class="lp_comments_details">

                                    {foreach $messages as $message}
                                        {if $message.type == "product"} 
                                            {include file="{$pl_template_dir}/views/templates/hook/messages/product.tpl"  message=$message} 
                                        {else if $message.type == "customer"}
                                            {include file="{$pl_template_dir}/views/templates/hook/messages/customer.tpl" message=$message} 
                                        {else if $message.type == "liked"}
                                            {include file="{$pl_template_dir}/views/templates/hook/messages/liked.tpl"    message=$message} 
                                        {else if $message.type == "order"}
                                            {include file="{$pl_template_dir}/views/templates/hook/messages/order.tpl"    message=$message} 
                                        {* {else}
                                            {include file="{$pl_template_dir}/views/templates/hook/messages/default.tpl"     message=$message}  *}
                                        {/if}
                                    {/foreach}

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="lp_footer">
            <div class="lp_promos_container"></div>
            <div class="lp_icons flex-end"></div>
        </div>

        <div class="bubble shake">
            <img class="live_icon" src="{$lp_url_svg}/youtube-live.svg" alt="">
        </div>

        <div class="particles_container">
            <div class="particles"></div>
        </div>

    </div>
    {* END MAIN CHAT *}

    {* SIDE CHAT *}
    <div class="lp_side_chat">
        <div class="close">
            <i class="fas fa-arrow-alt-circle-right"></i>
            <i class="fas fa-arrow-alt-circle-left"></i>
        </div>
        <div class="lp_side_chat_container">
            {foreach $messages as $message}
                <div class="lp_action {$message.type}">
                    <div class="lp_action_info_container">
                        <div class="lp_action_info">
                            <div class="lp_action_product_details">
                                {if $message.type == "product"}
                                    {include file="{$pl_template_dir}/views/templates/hook/messages/side_chat/product.tpl"  message=$message} 
                                {else if $message.type == "customer"}
                                    {include file="{$pl_template_dir}/views/templates/hook/messages/side_chat/customer.tpl" message=$message} 
                                {else if $message.type == "order"}
                                    {include file="{$pl_template_dir}/views/templates/hook/messages/side_chat/order.tpl"    message=$message} 
                                {else if $message.type == "liked"}
                                    {include file="{$pl_template_dir}/views/templates/hook/messages/side_chat/liked.tpl"    message=$message} 
                                {* {else}
                                    {include file="{$pl_template_dir}/views/templates/hook/messages/side_chat/default.tpl" message=$message}  *}
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        </div>
    </div>
    {* END SIDE CHAT *}

</div>



<script type="text/javascript">
    var addtocartprod = '{l s='Add to cart'     mod='lnk_livepromo'}';
    var newusername   = '{l s='New user'        mod='lnk_livepromo'}';
    var newuser       = '{l s='joined us'       mod='lnk_livepromo'}';
    var userdoorder   = '{l s='places an order' mod='lnk_livepromo'}';
</script>