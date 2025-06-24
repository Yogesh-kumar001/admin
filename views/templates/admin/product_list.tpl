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
<div class="form-wrapper">
    <div id="product_list" class="product-cards">
        <div class="row">
        {foreach from=$ProductsList item=product}
        <div class="product-container col-md-3">
   
            <div class="product-card" data-id="{$product.id_product|escape:'html':'UTF-8'}">
                <img src="{$product.image|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}">
                <div class="product-details">
                    <p><strong>{$product.name|escape:'html':'UTF-8'}</strong></p>
                    <p><label class="plabel"> {l s='Ref' mod='awvisualmerchandising'}</label>: {$product.sku|escape:'html':'UTF-8'}</p>
                    <p><label class="plabel">{l s='SKU' mod='awvisualmerchandising'}</label>: {$product.ean13|escape:'html':'UTF-8'}</p>
                    <p><label class="plabel">{l s='Brand' mod='awvisualmerchandising'}</label>: {$product.manufacturer_name|escape:'html':'UTF-8'}</p>
                </div>
                <div class="overlay">
                    <button class="pin-button" data-id="{$product.id_product|escape:'html':'UTF-8'}">
                        <img src="{$pathUri|escape:'html':'UTF-8'}/views/img/pin.svg" alt="{l s='Pin' mod='awvisualmerchandising'}" style="width: 20px; height: 20px; margin-right: 10px;">
                        {l s='Pin' mod='awvisualmerchandising'}
                    </button>
                    <button class="hide-button" data-id="{$product.id_product|escape:'html':'UTF-8'}">
                        <img src="{$pathUri|escape:'html':'UTF-8'}/views/img/hide.svg" alt="{l s='Hide' mod='awvisualmerchandising'}" style="width: 20px; height: 20px; margin-right: 10px;">
                        {l s='Hide' mod='awvisualmerchandising'}
                    </button>
                </div>
            </div>
        </div>
        {/foreach}
        </div>
    </div>
    <div id="load-more-trigger"></div>
    <div id="loading" style="display: none; text-align: center;">
        <img src="{$pathUri|escape:'html':'UTF-8'}/views/img/loading.gif" alt="{l s='Loading...' mod='awvisualmerchandising'}">
    </div>
</div>
