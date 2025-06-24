
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

{include file="{$LocalPath}views/templates/admin/live_preview.tpl"}

<div class="panel panel-default col-lg-8" style="margin:5px;">

    <div class="form-wrapper col-lg-5" style="margin: 15px auto; float: none;">
        <div id="product-search">
            <input type="text" id="search-query" placeholder="{l s='Search products by name, SKU, or EAN' mod='awvisualmerchandising'}" class="form-control">
            <ul id="search-results" class="product-list"></ul>
        </div>
    </div>
    <div id="pinned-products-wrapper" class="col-lg-12 pinned-products-wrapper" style="margin:5px;">
        {include file="{$LocalPath}views/templates/admin/pinned-products.tpl"}
    </div>
    <div id="product_list-wrapper" class="col-lg-12 product_list-wrapper" style="margin:5px;">
        {include file="{$LocalPath}views/templates/admin/product_list.tpl"}
    </div>
</div>

<script>
var token = "{$token|escape:'javascript':'UTF-8'}";
var id_awvisualmerchandising = {$id_awvisualmerchandising|escape:'javascript':'UTF-8'};
var pathUri = "{$pathUri|escape:'javascript':'UTF-8'}";
var searchActionUrl = "{$searchAction|escape:'javascript':'UTF-8'}";
var mobileItems = {$mobileItems|escape:'javascript':'UTF-8'};
var desktopItems = {$desktopItems|escape:'javascript':'UTF-8'};
var select_an_option = "{l s='Select an option' mod='awvisualmerchandising'}";
var are_you_sure = "{l s='Are you sure?' mod='awvisualmerchandising'}";
var Are_you_sure_you_want_to_remove_this_pinned_product = "{l s='Are you sure you want to remove this pinned product?' mod='awvisualmerchandising'}";
var warning = "{l s='warning' mod='awvisualmerchandising'}";
var Yes_remove_it = "{l s='Yes remove it!' mod='awvisualmerchandising'}";
var Error_loading_edit_modal = "{l s='Error loading edit modal' mod='awvisualmerchandising'}";
var Are_you_sure_you_want_to_delete_this_rule = "{l s='Are you sure you want to delete this rule?' mod='awvisualmerchandising'}";
var Yes_delete_it = "{l s='Yes, delete it!' mod='awvisualmerchandising'}";
var Cancel = "{l s='Cancel' mod='awvisualmerchandising'}";
var loadEditModal = "{l s='loadEditModal' mod='awvisualmerchandising'}";
var error = "{l s='error' mod='awvisualmerchandising'}";
var Error = "{l s='Error' mod='awvisualmerchandising'}";
var success = "{l s='success' mod='awvisualmerchandising'}";
var top_end = "{l s='top_end' mod='awvisualmerchandising'}";
var An_error_occurred_while_processing_the_response = "{l s='An error occurred while processing the response' mod='awvisualmerchandising'}";
var Failed_to_delete_rule = "{l s='Failed to delete rule' mod='awvisualmerchandising'}";
var Rule_deleted_successfully = "{l s='Rule deleted successfully' mod='awvisualmerchandising'}";
var Failed_to_pin_the_product = "{l s='Failed to pin the product.' mod='awvisualmerchandising'}";
var Failed_to_save_rule_Please_try_again = "{l s='Failed to save rule. Please try again.' mod='awvisualmerchandising'}";
var An_error_occurred_Please_try_again = "{l s='An error occurred. Please try again.' mod='awvisualmerchandising'}";
var An_error_occurred_while_updating = "{l s='An error occurred while updating.' mod='awvisualmerchandising'}";
var Product_unpinned_and_removed_from_the_list = "{l s='Product unpinned and removed from the list' mod='awvisualmerchandising'}";
var Failed_to_unpin_the_product = "{l s='Failed to unpin the product.' mod='awvisualmerchandising'}";
var An_error_occurred_while_unpinning_the_product = "{l s='An error occurred while unpinning the product.' mod='awvisualmerchandising'}";
var An_error_occurred_while_pinning_the_product = "{l s='An error occurred while pinning the product.' mod='awvisualmerchandising'}";
var Positions_updated_successfully = "{l s='Positions updated successfully' mod='awvisualmerchandising'}";
var Failed_to_update_positions = "{l s='Failed to update positions.' mod='awvisualmerchandising'}";
var An_error_occurred_while_updating_positions = "{l s='An error occurred while updating positions.' mod='awvisualmerchandising'}";
var AJAX_request_failed = "{l s='AJAX request failed.' mod='awvisualmerchandising'}";
var Updated_successfully = "{l s='Updated successfully.' mod='awvisualmerchandising'}";
var Failed_to_update = "{l s='Failed to update.' mod='awvisualmerchandising'}";
var Product_pinned_successfully = "{l s='Product pinned successfully!' mod='awvisualmerchandising'}";
var Category_updated_successfully = "{l s='Category updated successfully' mod='awvisualmerchandising'}";
var Error_in_AJAX_request = "{l s='Error in AJAX request' mod='awvisualmerchandising'}";
var An_error_occurred_while_removing_the_pinned_product = "{l s='An error occurred while removing the pinned product' mod='awvisualmerchandising'}";
var Status_updated_successfully = "{l s='Status updated successfully' mod='awvisualmerchandising'}";
var Failed_to_update_status = "{l s='Failed to update status' mod='awvisualmerchandising'}";
var Product_hidden_successfully = "{l s='Product hidden successfully' mod='awvisualmerchandising'}";
var Failed_to_hide_the_product = "{l s='Failed to hide the product' mod='awvisualmerchandising'}";
var Product_unhidden_and_removed_from_the_list = "{l s='Product unhidden and removed from the list' mod='awvisualmerchandising'}";
var Rules_sorted_successfully = "{l s='Rules sorted successfully' mod='awvisualmerchandising'}";
var Failed_to_update_rule_sort_order = "{l s='Failed to update rule sort order' mod='awvisualmerchandising'}";
var No_products_found = "{l s='No products found.' mod='awvisualmerchandising'}";
</script>
