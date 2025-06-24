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
<div id="pinned-products" class="product-cards">
  <div class="row">
    {foreach from=$pinnedProducts item=product}
      <div class="product-container two-per-row col-md-3">
        <div class="product-card{if isset($product.hidden) && $product.hidden} vshidden not-sortable{/if}" data-id="{$product.id|escape:'html':'UTF-8'}">
          <img src="{$link->getImageLink($product.link_rewrite, $product.id_image, 'home_default')|escape:'html':'UTF-8'}" alt="{$product.name|escape:'html':'UTF-8'}">
          <div class="product-details">
            <p><strong>{$product.name|escape:'html':'UTF-8'}</strong></p>
            <p><label class="plabel">{l s='Ref' mod='awvisualmerchandising'}</label>: {$product.sku|escape:'html':'UTF-8'}</p>
            <p><label class="plabel">{l s='SKU' mod='awvisualmerchandising'}</label>: {$product.ean13|escape:'html':'UTF-8'}</p>
            <p><label class="plabel">{l s='Brand' mod='awvisualmerchandising'}</label>: {$product.manufacturer_name|escape:'html':'UTF-8'}</p>
          </div>
          {if isset($product.hidden) && $product.hidden}
            <img
              src="{$pathUri|escape:'html':'UTF-8'}/views/img/unhide.svg"
              class="hide-icon hide-pin"
              data-id="{$product.id|escape:'html':'UTF-8'}"
              alt="{l s='Unhide' mod='awvisualmerchandising'}"
            />
          {else}
            <img
              src="{$pathUri|escape:'html':'UTF-8'}/views/img/pin.svg"
              class="pin-icon toggle-pin"
              data-id="{$product.id|escape:'html':'UTF-8'}"
              alt="{l s='Unpin' mod='awvisualmerchandising'}"
            />
          {/if}
        </div>
      </div>
    {/foreach}
  </div>
</div>
