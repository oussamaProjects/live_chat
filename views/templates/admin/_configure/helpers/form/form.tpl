{**
* Dropship System Client
*
* @author Ruggiero Marco
* @copyright Copyright (c) 2017, PrestaBiz
* @license End User License Agreement (EULA)
* @link http://www.prestabiz.com
* @email info@prestabiz.com
*
* E' vietata la vendita, la distribuzione e la modifica anche parziale del codice sorgente
* senza il consenso scritto del produttore.
*}
{extends file="helpers/form/form.tpl"}

{block name="defaultForm"}
    {if $old_version && !(isset($ik))}{assign var="ik" value=0}{/if}
    {$smarty.block.parent}
{/block}

{block name="input"}
    {if $input.type == 'password'}
        {assign var='value_text' value=$fields_value[$input.name]}
        <div class="input-group fixed-width-lg">
            <span class="input-group-addon">
                <i class="icon-key"></i>
            </span>
            <input type="password" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" name="{$input.name}" class="{if isset($input.class)}{$input.class}{/if}" value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}" {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off" {/if} {if isset($input.required) && $input.required } required="required" {/if} />
        </div>
    {*
    {elseif $input.type == 'daterange'}
        {assign var='value_text' value=$fields_value[$input.name]}
        <div class="input-group fixed-width-lg">
            <span class="input-group-addon">
                <i class="icon-key"></i>
            </span>
            <input type="password" id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}" name="{$input.name}" class="{if isset($input.class)}{$input.class}{/if}" value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}" {if isset($input.autocomplete) && !$input.autocomplete}autocomplete="off" {/if} {if isset($input.required) && $input.required } required="required" {/if} />
        </div> 
    *}
    {elseif $input.type == 'add_ip'}
        {assign var='value_text' value=$fields_value[$input.name]}
        <div class="col-lg-9">
            <div class="row">
                <div class="col-lg-8">
                    <input 
                        type="text" {if isset($field['id'])} 
                        id="{$field['id']}" {/if} 
                        size="{if isset($value_text)}{$value_text|intval}{else}5{/if}" 
                        name="LP_IP" 
                        value="{$value_text|escape:'html':'UTF-8'}" />
                </div>
                <div class="col-lg-1">
                    <button 
                        type="button" 
                        class="btn btn-default" 
                        onclick="addLPRemoteAddr();">
                        <i class="icon-plus"></i> Ajouter mon IP
                    </button>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            function addLPRemoteAddr() {
                var length = $('input[name=LP_IP]').attr('value').length;
                if (length > 0)
                    $('input[name=LP_IP]').attr('value', $('input[name=LP_IP]').attr('value') + ',{$smarty.server.REMOTE_ADDR}');
                else
                    $('input[name=LP_IP]').attr('value', '{$smarty.server.REMOTE_ADDR}');
            }
        </script>
    {else}
        {$smarty.block.parent}
    {/if}
{/block}

{block name="input_row"}
    {if $old_version && isset($ik) && $ik == 0}<div class="form-wrapper">{/if}
    <div class="form-group-wrapper {$input.name|lower|escape:'html':'UTF-8'}" {if isset($input.tab)} data-tab-id="{$input.tab|escape:'html':'UTF-8'}" {/if} {if isset($input.children) && $input.children} data-children-id="{$input.children|escape:'html':'UTF-8'}" {/if}>
        {$smarty.block.parent}
    </div>
    {if $old_version && isset($ik)}{assign var="ik" value=$ik+1}{if $ik == $field|count}</div>{/if}{/if}
{/block}




