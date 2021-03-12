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
<div class="lp-liked {if $detailproduct}detailproduct{/if}">
    {if $logged}
        {if $issetProduct}
            <a class='lp-liked-btn liked' data-id_product="{$lp_id_product|intval}" data-action="delete">
            <span class='like-icon'> <div class='heart-animation-1'></div> <div class='heart-animation-2'></div> </span>
            {if $detailproduct}{l s='Like' mod='lnk_livepromo'}{/if}
            </a>
        {else}
            <a class='lp-liked-btn' data-id_product="{$lp_id_product|intval}" data-action="add">
                <span class='like-icon'> <div class='heart-animation-1'></div> <div class='heart-animation-2'></div></span> {if $detailproduct}{l s='Like' mod='lnk_livepromo'}{/if}
            </a>
        {/if}
    {else}
        <a class="lp-liked-btn-2 not-logged" title="{l s='You must be logged' mod='lnk_livepromo'}"><span class='like-icon'> <div class='heart-animation-1'></div> <div class='heart-animation-2'></div></span> {if $detailproduct}{l s='Like' mod='lnk_livepromo'}{/if}</a>
    {/if}
</div>