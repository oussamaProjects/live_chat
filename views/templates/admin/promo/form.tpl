<div id="livepromo">
    <form action="{$currentIndex|escape}&amp;token={$currentToken|escape}&amp;addcart_rule" id="cart_rule_form" class="form-horizontal" method="post">
        <div class="panel">
            <div class="form-wrapper">
                <ul class="nav nav-tabs">
                    <li class="tab-row">
                        <a href="#cart_rule_informations" data-toggle="tab"><i class="icon-info"></i> {l s='Information'}</a>
                    </li>
                    <li class="tab-row">
                        <a href="#cart_rule_conditions" data-toggle="tab"><i class="icon-random"></i> {l s='Conditions'}</a>
                    </li>
                    <li class="tab-row">
                        <a href="#cart_rule_actions" data-toggle="tab"><i class="icon-wrench"></i> {l s='Actions'}</a>
                    </li>
                    <li class="tab-row">
                        <a href="#cart_rule_scheduall" data-toggle="tab"><i class="icon-wrench"></i> {l s='Scheduall' mod='lnk_livepromo'}</a>
                    </li>
                </ul>
                
                {if $currentObject->id}<input type="hidden" name="id_cart_rule" value="{$currentObject->id|intval}" />{/if}
                <input type="hidden" id="currentFormTab" name="currentFormTab" value="{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'quotes'}{else}informations{/if}" />
                <div class="tab-content panel">
                    <div id="cart_rule_informations" class="tab-pane active">
                        {include file='controllers/cart_rules/informations.tpl'}
                    </div>
                    <div id="cart_rule_conditions" class="tab-pane">
                        {include file='controllers/cart_rules/conditions.tpl'}
                    </div>
                    <div id="cart_rule_actions" class="tab-pane">
                        {include file='controllers/cart_rules/actions.tpl'}
                    </div>
                    <div id="cart_rule_scheduall" class="tab-pane">
                        {include file='./scheduall.tpl'}
                    </div>
                </div>
                <button type="submit" class="btn btn-default pull-right" name="submitAddcart_rule" id="{$table|escape}_form_submit_btn">{l s='Save'}
                </button>
                <!--<input type="submit" value="{l s='Save and stay'}" class="button" name="submitAddcart_ruleAndStay" id="" />-->
                {include file="footer_toolbar.tpl"}
            </div>
        </div>
    </form>
    <script type="text/javascript">
        var product_rule_groups_counter = {if isset($product_rule_groups_counter)}{$product_rule_groups_counter|intval}{else}0{/if};
        var product_rule_counters = new Array();
        var currentToken = '{$currentToken|escape:'quotes'}';
        var currentFormTab = '{if isset($smarty.post.currentFormTab)}{$smarty.post.currentFormTab|escape:'quotes'}{else}informations{/if}';
        var currentText = '{l s='Now' js=1}';
        var closeText = '{l s='Done' js=1}';
        var timeOnlyTitle = '{l s='Choose Time' js=1}';
        var timeText = '{l s='Time' js=1}';
        var hourText = '{l s='Hour' js=1}';
        var minuteText = '{l s='Minute' js=1}';

        var languages = new Array();
                {foreach from=$languages item=language key=k}
                    languages[{$k}] = {
                        id_lang: {$language.id_lang},
                        iso_code: '{$language.iso_code|escape:'quotes'}',
                        name: '{$language.name|escape:'quotes'}'
        };
                {/foreach}
                displayFlags(languages, {$id_lang_default});
    </script>
    <script type="text/javascript" src="themes/default/template/controllers/cart_rules/form.js"></script>
</div>