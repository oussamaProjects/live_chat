{**
*
* L'nk Live Promo Module
*
* @author    Abdelakbir el Ghazouani - By L'nkboot.fr
* @copyright 2016-2020 Abdelakbir el Ghazouani - By L'nkboot.fr (http://www.lnkboot.fr)
* @license   Commercial license see license.txt
* @category  Module
* Support by mail  : contact@lnkboot.fr
*
*}

{capture name=path}{l s='My favorites products' mod='lnk_livepromo'}{/capture}
<div class="lnk-container">
	{if !$is_logged}
		<div class="lnk-401">
			<h1 class="lnk-h1">{l s='Authentication' mod='lnk_livepromo'}</h1>
			<p>{l s='Authentication is required to access the page.' mod='lnk_livepromo'}</p>
		</div>	
    {else}
		<div class="row">
			{foreach from=$products item=product}
				<div class="col-xs-6 col-md-3">
					<div class="lp-block-product">
						<a href="{$link->getProductlink($product.id_product, $product.link_rewrite, $product.category_rewrite, null, null, null, $product.id_product_attribute)|escape:'htmlall':'UTF-8'}" title="{l s='Product detail' mod='advansedwishlist'}">
                            <img src="{$link->getImageLink($product.link_rewrite, $product.cover, 'home_desktop')|escape:'htmlall':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}" />
                        </a>
						<div class="lp-content-block">
							<h3>{$product.name|escape:'html':'UTF-8'}</h3>
							{if $product.has_reduction}
								<span class="lp-price">{$product.specific_price} <em>{$product.price}</em></span>
							{else}
								<span class="lp-price">{$product.price}</span>
							{/if}
						</div>
					</div>	
				</div>	
			{/foreach}
		</div>	
	{/if}
</div>