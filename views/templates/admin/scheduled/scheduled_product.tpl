{if isset($scheduled_product) && !empty($scheduled_product)}
    {assign var="scheduled_product_exist" value=true}
{elseif !isset($scheduled_product) || empty($scheduled_product)} 
    {assign var="scheduled_product_exist" value=false}
{else}
    {assign var="scheduled_product_exist" value=false}
{/if}

<div id="livepromo">
    <form action="{$currentIndex|escape}&amp;token={$currentToken|escape}&amp;addlnk_promo_scheduled_products" id="scheduled_product_form" class="form-horizontal" method="post">

        <div class="panel">
            <div class="form-wrapper">
                <ul class="nav nav-tabs">
                    <li class="tab-row">
                        <a href="#cart_rule_informations" data-toggle="tab"><i class="icon-info"></i> {l s='Produit'}</a>
                    </li>
                </ul>

                <div class="tab-content panel">

                    <div id="Product_div" class="form-group">
                        <label class="control-label col-lg-3">{l s='Product' mod='lnk_livepromo'}</label>
                        <div class="col-lg-5">

                            <input type="hidden" id="nameScheduledProduct"  name="nameScheduledProduct" />
                            <input type="hidden" id="id_product"            name="id_product"            value="{if $scheduled_product_exist == true}{$scheduled_product.id_product}{/if}" />
                            <input type="hidden" id="id_scheduled_products" name="id_scheduled_products" value="{if isset($id_scheduled_products) && !empty($id_scheduled_products)}{$id_scheduled_products}{/if}" />

                            {if $scheduled_product_exist == false}
                                <div id="ajax_choose_product_association">
                                    <div class="input-group">
                                        <input type="text" id="product_autocomplete_input_association" name="product_autocomplete_input_association" />
                                        <span class="input-group-addon"><i class="icon-search"></i></span>
                                    </div>
                                </div>
                                <div id="divScheduledProduct"></div>
                            {else}
                                <div id="divScheduledProduct">
                                    <div class="form-control-static">
                                        <button type="button" class="btn btn-default delAssociation" name="{$scheduled_product.id_product}">
                                            <i class="icon-remove text-danger"></i>
                                        </button>
                                        {if $scheduled_product_exist == true}
                                            {$scheduled_product.name|escape:'html':'UTF-8'}
                                            {if !empty($scheduled_product.reference)}(ref: {$scheduled_product.reference}) {/if}
                                        {/if}
                                    </div>
                                </div>
                            {/if}

                        </div>
                    </div>

                    <div id="minute_options_div" class="form-group">
                        <label class="control-label col-lg-3">{l s='Minute' mod='lnk_livepromo'}</label>
                        <div class="col-lg-5">
                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="text" id="minute" name="minute" value="{$promo_scheduall['minute']|escape:'html':'UTF-8'}" onchange="this.value = this.value.replace(/,/g, '.');" />
                                </div>
                                <div class="col-lg-8">
                                    <select id="minute_options" name="minute_options" class="form-control" onchange="select_single_option('minute')">
                                        {foreach from=$minute_options item=minute_option key=key}
                                            <option {if $promo_scheduall['minute']==$key} selected="selected" {/if} value="{$key}">
                                                {$minute_option}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="hour_options_div" class="form-group">
                        <label class="control-label col-lg-3">{l s='Hour' mod='lnk_livepromo'}</label>
                        <div class="col-lg-5">
                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="text" id="hour" name="hour" value="{$promo_scheduall['hour']|escape:'html':'UTF-8'}" onchange="this.value = this.value.replace(/,/g, '.');" />
                                </div>
                                <div class="col-lg-8">
                                    <select id="hour_options" name="hour_options" class="form-control" onchange="select_single_option('hour')">
                                        {foreach from=$hour_options item=hour_option key=key}
                                            <option {if $promo_scheduall['hour']==$key} selected="selected" {/if} value="{$key}">
                                                {$hour_option}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="day_options_div" class="form-group">
                        <label class="control-label col-lg-3">{l s='Day' mod='lnk_livepromo'}</label>
                        <div class="col-lg-5">
                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="text" id="day" name="day" value="{$promo_scheduall['day']|escape:'html':'UTF-8'}" onchange="this.value = this.value.replace(/,/g, '.');" />
                                </div>
                                <div class="col-lg-8">
                                    <select id="day_options" name="day_options" class="form-control" onchange="select_single_option('day')" class="form-control">
                                        {foreach from=$day_options item=day_option key=key}
                                            <option {if $promo_scheduall['day']==$key} selected="selected" {/if} value="{$key}">
                                                {$day_option}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="weekday_options_div" class="form-group">
                        <label class="control-label col-lg-3">{l s='Weekday' mod='lnk_livepromo'}</label>
                        <div class="col-lg-5">
                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="text" id="weekday" name="weekday" value="{$promo_scheduall['weekday']|escape:'html':'UTF-8'}" onchange="this.value = this.value.replace(/,/g, '.');" />
                                </div>
                                <div class="col-lg-8">
                                    <select id="weekday_options" name="weekday_options" class="form-control" onchange="select_single_option('weekday')">
                                        {foreach from=$weekday_options item=weekday_option key=key}
                                            <option {if $promo_scheduall['weekday']==$key} selected="selected" {/if} value="{$key}">
                                                {$weekday_option}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="month_options_div" class="form-group">
                        <label class="control-label col-lg-3">{l s='Month' mod='lnk_livepromo'}</label>
                        <div class="col-lg-5">
                            <div class="row">
                                <div class="col-lg-4">
                                    <input type="text" id="month" name="month" value="{$promo_scheduall['month']|escape:'html':'UTF-8'}" onchange="this.value = this.value.replace(/,/g, '.');" />
                                </div>
                                <div class="col-lg-8">
                                    <select id="month_options" name="month_options" class="form-control" onchange="select_single_option('month')" class="form-control">
                                        {foreach from=$month_options item=month_option key=key}
                                            <option {if $promo_scheduall['month']==$key} selected="selected" {/if} value="{$key}">
                                                {$month_option}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-lg-3">{l s='Notification'}</label>
                        <div class="col-lg-9">
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" name="notify" id="notify_on" value="1" {if $promo_scheduall['notify']==1} checked="checked" {/if} />
                                <label class="t" for="notify_on">
                                    {l s='Yes'}
                                </label>
                                <input type="radio" name="notify" id="notify_off" value="0" {if $promo_scheduall['notify']==0} checked="checked" {/if} />
                                <label class="t" for="notify_off">
                                    {l s='No'}
                                </label>
                                <a class="slide-button btn"></a>
                            </span>
                        </div>
                    </div>

                    <div class="clearfix"></div>

                </div>

                <div class="panel-footer" id="toolbar-footer">
                    <button type="submit" name="submitAddlnk_promo_scheduled_products" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save'}</button>
                    <a id="desc-cart_rule-cancel" class="btn btn-default" href="index.php?controller=AdminPromo&amp;token=fed053be4b7eac0fd2b04a3a8d0e1646">
                        <i class="process-icon-cancel"></i> <span>Annuler</span>
                    </a>
                    <button type="submit" name="submitAddlnk_promo_scheduled_productsAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i> {l s='Save and stay'}</button>
                </div>

            </div>
        </div>
    </form>
</div>