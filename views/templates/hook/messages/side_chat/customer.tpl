
{assign var="id_lang"           value={($cookie->id_lang) ? $cookie->id_lang : 1}}
{assign var="customer_name"     value=$message.customer_name}
{assign var="product"           value=$message.productdetail.lang[$id_lang]}

<div class="lp_action_product_name">
    {$customer_name} {$product.action} 
</div>
<a class="lp_action_product_img celebrate_link" href="#"><span class="celebrate"></span></a>
            