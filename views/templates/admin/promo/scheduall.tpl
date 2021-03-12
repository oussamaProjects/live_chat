{* <div id="common_options_div" class="form-group">
	<label class="control-label col-lg-3">{l s='Amount' mod='lnk_livepromo'}</label>
	<div class="col-lg-5">
		<div class="row">
			<div class="col-lg-12">
				<select id="common_options" name="common_options" class="form-control" onchange="select_common_option()">
					{foreach from=$common_options item=common_option key=key}
						<option value="{$key}">
								{$common_option}
						</option>
					{/foreach}
				</select>
			</div>
		</div>
	</div>
</div> *}


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
			<input type="radio" name="notify" id="notify_on" value="1" {if $promo_scheduall['notify']==1} checked="checked"{/if} />
			<label class="t" for="notify_on">
				{l s='Yes'}
			</label>
			<input type="radio" name="notify" id="notify_off" value="0"  {if $promo_scheduall['notify']==0} checked="checked"{/if} />
			<label class="t" for="notify_off">
				{l s='No'}
			</label>
			<a class="slide-button btn"></a>
		</span>
	</div>
</div>

