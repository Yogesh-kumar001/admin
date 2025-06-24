{*
 * 2007-2025 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2025 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
*}

<ul id="addedRules" class="list-group">
    {foreach from=$rules item=rule}
        <li class="list-group-item"
            data-rule-id="{$rule.id|escape:'html':'UTF-8'}"
            data-segment="{$rule.rule_data.segment|escape:'html':'UTF-8'}"
        >
            
            <div class="d-flex justify-content-between align-items-center">
                <span>
                    <strong>{l s='Rule Type' mod='awvisualmerchandising'}:</strong> {$rule.rule_type|escape:'html':'UTF-8'}
                </span>
                <span>
                    <strong>{l s='Segment' mod='awvisualmerchandising'}:</strong> {$rule.rule_data.segment|escape:'html':'UTF-8'}
                </span>
            </div>
            {if isset($rule.rule_data.attributes_values) && count($rule.rule_data.attributes_values)}
                {foreach from=$rule.rule_data.attributes_values key=attr_name item=values}
                    {foreach from=$values item=value}
                        <strong>{$attr_name|escape:'html':'UTF-8'}:</strong> {$value.name|escape:'html':'UTF-8'}{if isset($rule.rule_data.attributes_ranges[$attr_name])} ({l s='Blending' mod='awvisualmerchandising'}: {$rule.rule_data.attributes_ranges[$attr_name]|escape:'html':'UTF-8'} %){/if} <br>
                    {/foreach}
                {/foreach}
            {/if}
            {if isset($rule.rule_data.features_values) && count($rule.rule_data.features_values)}
                {foreach from=$rule.rule_data.features_values key=featureKey item=feature}
                    <strong>{$feature.featurename|escape:'html':'UTF-8'}:</strong> {$feature.value|escape:'html':'UTF-8'}{if isset($rule.rule_data.features_ranges[$featureKey])} ({l s='Blending' mod='awvisualmerchandising'}: {$rule.rule_data.features_ranges[$featureKey]|escape:'html':'UTF-8'} %){/if} <br>
                {/foreach}
            {/if}
            {if isset($rule.rule_data.brand_name)}
                <strong>{l s='Brand' mod='awvisualmerchandising'}:</strong> {$rule.rule_data.brand_name|escape:'html':'UTF-8'}{if isset($rule.rule_data.brand_range)} ({l s='Blending' mod='awvisualmerchandising'}: {$rule.rule_data.brand_range|escape:'html':'UTF-8'} %){/if}<br>
            {/if}
            {if isset($rule.rule_data.supplier_name)}
                <strong>{l s='Supplier' mod='awvisualmerchandising'}:</strong> {$rule.rule_data.supplier_name|escape:'html':'UTF-8'}{if isset($rule.rule_data.supplier_range)} ({l s='Blending' mod='awvisualmerchandising'}: {$rule.rule_data.supplier_range|escape:'html':'UTF-8'} %){/if}<br>
            {/if}
            {if isset($rule.rule_data.discounted)}
                <strong>{l s='Discount' mod='awvisualmerchandising'}:</strong> {if $rule.rule_data.discounted}{l s='Yes' mod='awvisualmerchandising'}{else}{l s='No' mod='awvisualmerchandising'}{/if}{if isset($rule.rule_data.discounted_range)} ({l s='Blending' mod='awvisualmerchandising'}: {$rule.rule_data.discounted_range|escape:'html':'UTF-8'} %){/if}<br>
            {/if}
            {if isset($rule.rule_data.newest)}
                <strong>{l s='Newest' mod='awvisualmerchandising'}:</strong> {if $rule.rule_data.newest}{l s='Yes' mod='awvisualmerchandising'}{else}{l s='No' mod='awvisualmerchandising'}{/if}{if isset($rule.rule_data.newest_range)} ({l s='Blending' mod='awvisualmerchandising'}: {$rule.rule_data.newest_range|escape:'html':'UTF-8'} %){/if}<br>
            {/if}
            {if isset($rule.rule_data.lowstock)}
                <strong>{l s='Low Stock' mod='awvisualmerchandising'}:</strong> {if $rule.rule_data.lowstock}{l s='Yes' mod='awvisualmerchandising'}{else}{l s='No' mod='awvisualmerchandising'}{/if}{if isset($rule.rule_data.lowstock_range)} ({l s='Blending' mod='awvisualmerchandising'}: {$rule.rule_data.lowstock_range|escape:'html':'UTF-8'} %){/if}<br>
            {/if}

            <div style="padding-top: 5px;">
                <button type="button" class="btn btn-sm btn-primary edit-rule" data-rule-id="{$rule.id|escape:'html':'UTF-8'}">{l s='Edit' mod='awvisualmerchandising'}</button>
                <button type="button" class="btn btn-sm btn-danger delete-rule" data-rule-id="{$rule.id|escape:'html':'UTF-8'}">{l s='Delete' mod='awvisualmerchandising'}</button>
            </div>
        </li>
    {/foreach}
</ul>
