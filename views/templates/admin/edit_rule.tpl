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
<input type="hidden" name="edit_rule_id" value="{$rule.id|escape:'html':'UTF-8'}" id="edit_rule_id">
<div class="form-group">
    <label for="edit_ruleType" class="awheading">{l s='Rule Type' mod='awvisualmerchandising'}</label>
    <select id="edit_ruleType" name="edit_ruleType" class="form-control select2">
        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
        <option value="boost" {if $rule.rule_type == 'boost'}selected{/if}>{l s='Boost' mod='awvisualmerchandising'}</option>
        <option value="bury" {if $rule.rule_type == 'bury'}selected{/if}>{l s='Bury' mod='awvisualmerchandising'}</option>
    </select>
</div>
<div class="form-group">
    <label for="edit_ruleSegment" class="awheading">{l s='Rule Segment' mod='awvisualmerchandising'}</label>
    <select id="edit_ruleSegment" name="edit_ruleSegment" class="form-control select2">
        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
        <option value="attribute" {if $rule.rule_data.segment == 'attribute'}selected{/if}>{l s='Attribute' mod='awvisualmerchandising'}</option>
        <option value="feature" {if $rule.rule_data.segment == 'feature'}selected{/if}>{l s='Feature' mod='awvisualmerchandising'}</option>
        <option value="brand" {if $rule.rule_data.segment == 'brand'}selected{/if}>{l s='Brand' mod='awvisualmerchandising'}</option>
        <option value="supplier" {if $rule.rule_data.segment == 'supplier'}selected{/if}>{l s='Supplier' mod='awvisualmerchandising'}</option>
        <option value="newest" {if $rule.rule_data.segment == 'newest'}selected{/if}>{l s='Newest' mod='awvisualmerchandising'}</option>
        <option value="discounted" {if $rule.rule_data.segment == 'discounted'}selected{/if}>{l s='Discounted' mod='awvisualmerchandising'}</option>
        <option value="lowstock" {if $rule.rule_data.segment == 'lowstock'}selected{/if}>{l s='Low Stock' mod='awvisualmerchandising'}</option>
    </select>
</div>

<div class="form-group segment segment-attribute">
    <label class="awheading">{l s='Attributes' mod='awvisualmerchandising'}</label>
    {foreach from=$attributes key=group item=attributeList}
        <div class="form-group row">
            <div class="col-md-6">
                <label for="edit_attribute_{$group|escape:'html':'UTF-8'}">{$group|escape:'html':'UTF-8'}&nbsp;({l s='optional' mod='awvisualmerchandising'})</label>
                <select id="edit_attribute_{$group|escape:'html':'UTF-8'}" name="edit_attribute[{$group|escape:'html':'UTF-8'}][]" class="form-control edit_attribute-select select2" data-group="{$group|escape:'html':'UTF-8'}">
                    <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
                    {foreach from=$attributeList item=attribute}
                        <option value="{$attribute.id|escape:'html':'UTF-8'}" {if isset($selected_attributes[$group]) && in_array($attribute.id, $selected_attributes[$group])}selected{/if}>
                            {$attribute.name|escape:'html':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="col-md-6 edit_awblending" {if $rule.rule_type == 'bury'}style="display:none;"{/if}>
                <label for="range_{$group|escape:'html':'UTF-8'}">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                <div class="slider_container">
                    <input type="range" class="slider" id="range_{$group|escape:'html':'UTF-8'}" name="edit_range[{$group|escape:'html':'UTF-8'}]"
                           min="0" max="100"
                           value="{if isset($rule.rule_data.attributes_ranges[$group])}{$rule.rule_data.attributes_ranges[$group]|escape:'html':'UTF-8'}{else}0{/if}">
                    <span id="edit_rangeOutput_{$group|escape:'html':'UTF-8'}">
                        {if isset($rule.rule_data.attributes_ranges[$group])}{$rule.rule_data.attributes_ranges[$group]|escape:'html':'UTF-8'}{else}0{/if}%
                    </span>
                </div>
            </div>
        </div>
    {/foreach}
</div>

<div class="form-group segment segment-feature">
    <label class="awheading">{l s='Features' mod='awvisualmerchandising'}</label>
    {foreach from=$features item=feature}
        <div class="form-group row">
            <div class="col-md-6">
                <label for="edit_feature_{$feature.id_feature|escape:'html':'UTF-8'}">{$feature.name|escape:'html':'UTF-8'}&nbsp;({l s='optional' mod='awvisualmerchandising'})</label>
                <select id="edit_feature_{$feature.id_feature|escape:'html':'UTF-8'}" name="edit_features[{$feature.id_feature|escape:'html':'UTF-8'}][]" class="form-control select2">
                    <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
                    {foreach from=$feature.values item=featureValue}
                        <option value="{$featureValue.id_feature_value|escape:'html':'UTF-8'}" {if isset($selected_features[$feature.id_feature]) && in_array($featureValue.id_feature_value, $selected_features[$feature.id_feature])}selected{/if}>
                            {$featureValue.value|escape:'html':'UTF-8'}
                        </option>
                    {/foreach}
                </select>
            </div>
            <div class="col-md-6 edit_awblending" {if $rule.rule_type == 'bury'}style="display:none;"{/if}>
                <label for="range_feature_{$feature.id_feature|escape:'html':'UTF-8'}">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                <input type="range" class="slider" id="range_feature_{$feature.id_feature|escape:'html':'UTF-8'}" name="edit_range_feature[{$feature.id_feature|escape:'html':'UTF-8'}]"
                       min="0" max="100"
                       value="{if isset($rule.rule_data.features_ranges[$feature.id_feature])}{$rule.rule_data.features_ranges[$feature.id_feature]|escape:'html':'UTF-8'}{else}0{/if}">
                <span id="edit_rangeFeatureOutput_{$feature.id_feature|escape:'html':'UTF-8'}">
                    {if isset($rule.rule_data.features_ranges[$feature.id_feature])}{$rule.rule_data.features_ranges[$feature.id_feature]|escape:'html':'UTF-8'}{else}0{/if}%
                </span>
            </div>
        </div>
    {/foreach}
</div>

<div class="form-group segment segment-brand">
    <label for="edit_brand" class="awheading">{l s='Brand' mod='awvisualmerchandising'}</label>
    <select id="edit_brand" name="edit_brand" class="form-control select2">
        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
        {foreach from=$brands item=brand}
            <option value="{$brand.id|escape:'html':'UTF-8'}" {if $selected_brand == $brand.id}selected{/if}>{$brand.name|escape:'html':'UTF-8'}</option>
        {/foreach}
    </select>
    <div class="edit_awblending" {if $rule.rule_type == 'bury'}style="display:none;"{/if}>
        <label for="range_brand">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
        <input type="range" class="slider" id="range_brand" name="edit_range_brand" min="0" max="100" value="{if isset($rule.rule_data.brand_range)}{$rule.rule_data.brand_range|escape:'html':'UTF-8'}{else}0{/if}">
        <span id="edit_rangeBrandOutput">{if isset($rule.rule_data.brand_range)}{$rule.rule_data.brand_range|escape:'html':'UTF-8'}{else}0{/if}%</span>
    </div>
</div>

<div class="form-group segment segment-supplier">
    <label for="edit_supplier" class="awheading">{l s='Supplier' mod='awvisualmerchandising'}</label>
    <select id="edit_supplier" name="edit_supplier" class="form-control select2">
        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
        {foreach from=$suppliers item=supplier}
            <option value="{$supplier.id|escape:'html':'UTF-8'}" {if $selected_supplier == $supplier.id}selected{/if}>{$supplier.name|escape:'html':'UTF-8'}</option>
        {/foreach}
    </select>
    <div class="edit_awblending" {if $rule.rule_type == 'bury'}style="display:none;"{/if}>
        <label for="range_supplier">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
        <input type="range" class="slider" id="range_supplier" name="edit_range_supplier" min="0" max="100" value="{if isset($rule.rule_data.supplier_range)}{$rule.rule_data.supplier_range|escape:'html':'UTF-8'}{else}0{/if}">
        <span id="edit_rangeSupplierOutput">{if isset($rule.rule_data.supplier_range)}{$rule.rule_data.supplier_range|escape:'html':'UTF-8'}{else}0{/if}%</span>
    </div>
</div>

<div class="form-group segment segment-newest">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="edit_newestRule" name="edit_newestRule" value="1" {if $newest}checked{/if}>
        <label class="form-check-label awheading" for="edit_newestRule">{l s='Newest' mod='awvisualmerchandising'}</label>
    </div>
    <div class="edit_awblending" {if $rule.rule_type == 'bury'}style="display:none;"{/if}>
        <label for="range_newest">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
        <input type="range" class="slider" id="range_newest" name="edit_range_newest" min="0" max="100" value="{if isset($rule.rule_data.newest_range)}{$rule.rule_data.newest_range|escape:'html':'UTF-8'}{else}0{/if}">
        <span id="edit_rangeNewestOutput">{if isset($rule.rule_data.newest_range)}{$rule.rule_data.newest_range|escape:'html':'UTF-8'}{else}0{/if}%</span>
    </div>
</div>

<div class="form-group segment segment-discounted">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="edit_discountedRule" name="edit_discountedRule" value="1" {if $discounted}checked{/if}>
        <label class="form-check-label awheading" for="edit_discountedRule">{l s='Discounted' mod='awvisualmerchandising'}</label>
    </div>
    <div class="edit_awblending" {if $rule.rule_type == 'bury'}style="display:none;"{/if}>
        <label for="range_discounted">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
        <input type="range" class="slider" id="range_discounted" name="edit_range_discounted" min="0" max="100" value="{if isset($rule.rule_data.discounted_range)}{$rule.rule_data.discounted_range|escape:'html':'UTF-8'}{else}0{/if}">
        <span id="edit_rangeDiscountedOutput">{if isset($rule.rule_data.discounted_range)}{$rule.rule_data.discounted_range|escape:'html':'UTF-8'}{else}0{/if}%</span>
    </div>
</div>

<div class="form-group segment segment-lowstock">
    <div class="form-check">
        <input class="form-check-input" type="checkbox" id="edit_lowStockRule" name="edit_lowStockRule" value="1" {if $lowstock}checked{/if}>
        <label class="form-check-label awheading" for="edit_lowStockRule">{l s='Low Stock' mod='awvisualmerchandising'}</label>
    </div>
    <div class="edit_awblending" {if $rule.rule_type == 'bury'}style="display:none;"{/if}>
        <label for="range_lowstock">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
        <input type="range" class="slider" id="range_lowstock" name="edit_range_lowstock" min="0" max="100" value="{if isset($rule.rule_data.lowstock_range)}{$rule.rule_data.lowstock_range|escape:'html':'UTF-8'}{else}0{/if}">
        <span id="edit_rangeLowstockOutput">{if isset($rule.rule_data.lowstock_range)}{$rule.rule_data.lowstock_range|escape:'html':'UTF-8'}{else}0{/if}%</span>
    </div>
</div>
