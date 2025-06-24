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
<div class="panel panel-default col-lg-3" style="margin:5px;">
    <div class="panel-heading">
        {l s='Select Rules' mod='awvisualmerchandising'}
    </div>
    <form method="post" action="" id="categoryForm">
        <div class="form-group">
            <label for="categorySelect">{l s='Category' mod='awvisualmerchandising'}</label>
            <select id="categorySelect" name="category" class="form-control select2">
                {foreach from=$categories key=group item=categoryList}
                    <optgroup label="{$group|escape:'html':'UTF-8'}">
                        {foreach from=$categoryList item=category}
                            <option value="{$category.id|escape:'html':'UTF-8'}" {if $category.id == $selectedCategory}selected{/if}>
                                {$category.name|escape:'html':'UTF-8'}
                            </option>
                        {/foreach}
                    </optgroup>
                {/foreach}
            </select>
        </div>
        <div id="rulesContainer">
            <label>{l s='Rules' mod='awvisualmerchandising'}</label>
            <div style="height: 5px;"></div>
            <button type="button" id="addRule" class="btn btn-primary">{l s='Add New Rule' mod='awvisualmerchandising'}</button>
            <div style="height: 20px;"></div>
            <div id="addedRulesContainer">
                {include file="{$LocalPath|escape:'html':'UTF-8'}views/templates/admin/rules.tpl"}
            </div>
        </div>
        <div class="form-group">
            <label for="desktopItems">{l s='Items per line (Desktop)' mod='awvisualmerchandising'}</label>
            <select id="desktopItems" name="desktopItems" class="form-control select2">
                <option value="1" {if $desktopItems == 1}selected{/if}>1</option>
                <option value="2" {if $desktopItems == 2}selected{/if}>2</option>
                <option value="3" {if $desktopItems == 3}selected{/if}>3</option>
                <option value="4" {if $desktopItems == 4}selected{/if}>4</option>
            </select>
        </div>
        <div class="form-group">
            <label for="mobileItems">{l s='Items per line (Mobile)' mod='awvisualmerchandising'}</label>
            <select id="mobileItems" name="mobileItems" class="form-control select2">
                <option value="1" {if $mobileItems == 1}selected{/if}>1</option>
                <option value="2" {if $mobileItems == 2}selected{/if}>2</option>
                <option value="3" {if $mobileItems == 3}selected{/if}>3</option>
            </select>
        </div>
    </form>
</div>
<div id="addRuleModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{l s='Add Rule' mod='awvisualmerchandising'}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='awvisualmerchandising'}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group rule">
                    <label for="ruleType" class="awheading">{l s='Rule Type' mod='awvisualmerchandising'}</label>
                    <select id="ruleType" name="ruleType" class="form-control select2">
                        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
                        <option value="boost">{l s='Boost' mod='awvisualmerchandising'}</option>
                        <option value="bury">{l s='Bury' mod='awvisualmerchandising'}</option>
                    </select>
                </div>
                <div class="form-group rule">
                    <label for="ruleSegment" class="awheading">{l s='Rule Segment' mod='awvisualmerchandising'}</label>
                    <select id="ruleSegment" name="ruleSegment" class="form-control select2">
                        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
                        <option value="attribute">{l s='Attribute' mod='awvisualmerchandising'}</option>
                        <option value="feature">{l s='Feature' mod='awvisualmerchandising'}</option>
                        <option value="brand">{l s='Brand' mod='awvisualmerchandising'}</option>
                        <option value="supplier">{l s='Supplier' mod='awvisualmerchandising'}</option>
                        <option value="newest">{l s='Newest' mod='awvisualmerchandising'}</option>
                        <option value="discounted">{l s='Discounted' mod='awvisualmerchandising'}</option>
                        <option value="lowstock">{l s='Low Stock' mod='awvisualmerchandising'}</option>
                    </select>
                </div>
                <div class="form-group segment segment-attribute">
                    <label for="attribute" class="awheading">{l s='Attributes' mod='awvisualmerchandising'}</label>
                    {foreach from=$attributes key=group item=attributeList}
                        <div class="form-group row">
                            <div class="col-md-6">
                                <label for="attribute_{$group|escape:'html':'UTF-8'}">{$group|escape:'html':'UTF-8'}&nbsp;({l s='optional' mod='awvisualmerchandising'})</label>
                                <select id="attribute_{$group|escape:'html':'UTF-8'}" name="attribute[{$group|escape:'html':'UTF-8'}][]" class="form-control attribute-select select2" data-group="{$group|escape:'html':'UTF-8'}">
                                    <option value="">----</option>
                                    {foreach from=$attributeList item=attribute}
                                        <option value="{$attribute.id|escape:'html':'UTF-8'}">{$attribute.name|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-md-6 awblending">
                                <label for="range_{$group|escape:'html':'UTF-8'}">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                                <div class="slider_container">
                                    <input type="range" class="slider" id="range_{$group|escape:'html':'UTF-8'}" name="range[{$group|escape:'html':'UTF-8'}]" min="0" max="100" value="0" oninput="document.getElementById('rangeOutput_{$group|escape:'html':'UTF-8'}').innerHTML = this.value + '%'">
                                    <span id="rangeOutput_{$group|escape:'html':'UTF-8'}">0%</span>
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
                                <label for="feature_{$feature.id_feature|escape:'html':'UTF-8'}">{$feature.name|escape:'html':'UTF-8'} (optional)</label>
                                <select id="feature_{$feature.id_feature|escape:'html':'UTF-8'}" name="features[{$feature.id_feature|escape:'html':'UTF-8'}][]" class="form-control feature-select select2" data-feature="{$feature.id_feature|escape:'html':'UTF-8'}">
                                    <option value="">----</option>
                                    {foreach from=$feature.values item=featureValue}
                                        <option value="{$featureValue.id_feature_value|escape:'html':'UTF-8'}">{$featureValue.value|escape:'html':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="col-md-6 awblending">
                                <label for="range_feature_{$feature.id_feature|escape:'html':'UTF-8'}">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                                <input type="range" class="slider" id="range_feature_{$feature.id_feature|escape:'html':'UTF-8'}" name="range_feature[{$feature.id_feature|escape:'html':'UTF-8'}]" min="0" max="100" value="0" oninput="document.getElementById('rangeFeatureOutput_{$feature.id_feature|escape:'html':'UTF-8'}').innerHTML = this.value + '%'">
                                <span id="rangeFeatureOutput_{$feature.id_feature|escape:'html':'UTF-8'}">0%</span>
                            </div>
                        </div>
                    {/foreach}
                </div>
                <div class="form-group segment segment-brand">
                <label for="brand" class="awheading">{l s='Brand' mod='awvisualmerchandising'}</label>
                <select id="brand" name="brand" class="form-control select2">
                        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
                        {foreach from=$brands item=brand}
                            <option value="{$brand.id|escape:'html':'UTF-8'}">{$brand.name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
                <div class="awblending">
                    <label for="range_brand">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                    <input type="range" class="slider" id="range_brand" name="range_brand" min="0" max="100" value="0" oninput="document.getElementById('rangeBrandOutput').innerHTML = this.value + '%'">
                    <span id="rangeBrandOutput">0%</span>
                </div>
            </div>
            <div class="form-group segment segment-supplier">
                <label for="supplier" class="awheading">{l s='Supplier' mod='awvisualmerchandising'}</label>
                <select id="supplier" name="supplier" class="form-control select2">
                        <option value="">{l s='Select Option' mod='awvisualmerchandising'}</option>
                        {foreach from=$suppliers item=supplier}
                            <option value="{$supplier.id|escape:'html':'UTF-8'}">{$supplier.name|escape:'html':'UTF-8'}</option>
                    {/foreach}
                </select>
                <div class="awblending">
                    <label for="range_supplier">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                    <input type="range" class="slider" id="range_supplier" name="range_supplier" min="0" max="100" value="0" oninput="document.getElementById('rangeSupplierOutput').innerHTML = this.value + '%'">
                    <span id="rangeSupplierOutput">0%</span>
                </div>
            </div>
            <div class="form-group segment segment-newest">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="newestRule" name="newestRule" value="1">
                    <label class="form-check-label awheading" for="newestRule">{l s='Newest' mod='awvisualmerchandising'}</label>
                </div>
                <div class="awblending">
                    <label for="range_newest">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                    <input type="range" class="slider" id="range_newest" name="range_newest" min="0" max="100" value="0" oninput="document.getElementById('rangeNewestOutput').innerHTML = this.value + '%'">
                    <span id="rangeNewestOutput">0%</span>
                </div>
            </div>
            <div class="form-group segment segment-discounted">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="discountedRule" name="discountedRule" value="1">
                    <label class="form-check-label awheading" for="discountedRule">{l s='Discounted' mod='awvisualmerchandising'}</label>
                </div>
                <div class="awblending">
                    <label for="range_discounted">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                    <input type="range" class="slider" id="range_discounted" name="range_discounted" min="0" max="100" value="0" oninput="document.getElementById('rangeDiscountedOutput').innerHTML = this.value + '%'">
                    <span id="rangeDiscountedOutput">0%</span>
                </div>
            </div>
            <div class="form-group segment segment-lowstock">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lowStockRule" name="lowStockRule" value="1">
                    <label class="form-check-label awheading" for="lowStockRule">{l s='Low Stock' mod='awvisualmerchandising'}</label>
                </div>
                <div class="awblending">
                    <label for="range_lowstock">{l s='Effect in listing' mod='awvisualmerchandising'}</label>
                    <input type="range" class="slider" id="range_lowstock" name="range_lowstock" min="0" max="100" value="0" oninput="document.getElementById('rangeLowstockOutput').innerHTML = this.value + '%'">
                    <span id="rangeLowstockOutput">0%</span>
                </div>
            </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="saveRule" class="btn btn-primary">{l s='Save Rule' mod='awvisualmerchandising'}</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='awvisualmerchandising'}</button>
            </div>
        </div>
    </div>
</div>
<div id="editRuleModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{l s='Edit Rule' mod='awvisualmerchandising'}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' mod='awvisualmerchandising'}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body edit-rule-modal-body">
            </div>
            <div class="modal-footer">
                <button type="button" id="updateRule" class="btn btn-primary">{l s='Update Rule' mod='awvisualmerchandising'}</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='awvisualmerchandising'}</button>
            </div>
        </div>
    </div>
</div>
