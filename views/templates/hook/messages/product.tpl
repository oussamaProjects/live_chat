
{assign var="id_lang"           value={($cookie->id_lang) ? $cookie->id_lang : 1}}
{assign var="customer_name"     value=$message.customer_name}
{assign var="product"           value=$message.productdetail.lang[$id_lang]}
{assign var="specific_price"    value=$message.productdetail.specific_price[$lp_group_id]}


<div class="lp_comment_details_container">
    <div class="lp_comment">
        <div class="lp_comment_details">

            <div class="lp_profile_img">
                <img src="{$product.image}" alt="">
            </div>
            <div class="lp_comment_info_container">
                <div class="lp_comment_info">
                    <div class="lp_customer_name">
                        <a href="#">{$customer_name}</a>
                        {if !empty($message.time)}
                            <span class="lp_dot">&nbsp;·&nbsp;</span>
                            <span class="lp_time">{$message.time}</span>
                        {/if}
                    </div>

                    <div class="lp_product_details">
                        {if $product.url}
                            <a class="lp_product_img" href="{$product.url}">
                                <img src="{$product.image}">
                            </a>
                            <div class="lp_product_name">
                                {$product.name}
                                {if isset($product.action)}
                                    <span class="action">
                                        <span>{$product.action}</span>
                                        <span class="{$product.extra_action}"></span>
                                    </span>
                                {/if}
                            </div>
                        {/if}
                    </div>

                    <div class="lp_product_price">
                        <span class="lp_price product-price">{$specific_price} </span>
                        <span class="lp_old_price product-price">{$product.price}</span>
                    </div>
                    <div class="lp_like_container">
                        <a href="{$base_dir}index.php?controller=cart?add=1&id_product={$product.id}" class="like">
                            <img src="{$lp_url_svg}cart-white.svg">
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
