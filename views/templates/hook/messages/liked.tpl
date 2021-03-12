
{assign var="id_lang" value={($cookie->id_lang) ? $cookie->id_lang : 1}}
{assign var="customer_name" value=$message.customer_name}
{assign var="product"       value=$message.productdetail.lang[$id_lang]}


<div class="lp_comment_details_container">
    <div class="lp_comment liked">
        <div class="lp_comment_details">

            <div class="lp_comment_info_container">
                <div class="lp_comment_info">

                    <div class="lp_product_details">
                        {if $product.url}
                            <a class="lp_product_img" href="{$product.url}">
                                <img src="{$product.image}">
                            </a>
                            <div class="lp_product_name">
                                <span>
                                    <strong>{$customer_name}</strong>&nbsp;
                                    {$product.action}
                                    <span class="{$product.extra_action}"></span>
                                    {$product.name}
                                </span>
                            </div>
                        {/if}
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
