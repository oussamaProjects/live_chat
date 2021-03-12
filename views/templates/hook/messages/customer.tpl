
{assign var="id_lang"           value={($cookie->id_lang) ? $cookie->id_lang : 1}}
{assign var="customer_name"     value=$message.customer_name}
{assign var="product"           value=$message.productdetail.lang[$id_lang]}

<div class="lp_comment_details_container">
    <div class="lp_comment customer">
        <div class="lp_comment_details">

            <div class="lp_comment_info_container">
                <div class="lp_comment_info">
                    <div class="lp_product_details">
                        <div class="lp_product_name">
                            <strong>{$customer_name}</strong>&nbsp;
                            <span>{$product.action}</span>&nbsp;
                            <span class="celebrate"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>