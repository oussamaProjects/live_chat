
{assign var="id_lang"       value={($cookie->id_lang) ? $cookie->id_lang : 1}}
{assign var="customer_name" value=$message.customer_name}
{assign var="product"       value=$message.productdetail.lang[$id_lang]}


<div class="lp_action_product_name">
    <strong>{$customer_name}</strong>&nbsp;
    {$product.action}
    <span class="{$product.extra_action}"></span>
    {$product.name}
</div>
<a class="lp_action_product_img celebrate_link" href="#">
    <span class="celebrate"></span> 
</a>