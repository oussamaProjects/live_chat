{assign var="id_lang"           value={($cookie->id_lang) ? $cookie->id_lang : 1}}
{assign var="product"           value=$message.productdetail.lang[$id_lang]}
{assign var="specific_price"    value=$message.productdetail.specific_price[$lp_group_id]}
            
<div class="lp_action_product_name">
    {$product.name}
    {if isset($product.action)}
        <span>{$product.action}</span>
    {/if}
</div>
<a class="lp_action_product_img" href="{$product.url}"><img src="{$product.image}"></a>
                