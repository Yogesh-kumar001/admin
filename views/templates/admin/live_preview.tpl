
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

<div class="panel panel-default col-lg-11" style="margin:5px;padding:5px">
   
    <div class="panel-body">

        <div class="row">
            <div class="col-md-6">
                <a href="{$categoryLink|escape:'html':'UTF-8'}" class="btn btn-default" target="_blank" id="open-live-preview">
                    <i class="icon-external-link"></i>
                    {l s='Open Live Preview' mod='awvisualmerchandising'}
                </a>
                <br />
                <p class="helper-text">
                    {l s='Preview changes on front without saving or publish it.' mod='awvisualmerchandising'}
                </p>
            </div>
            <div class="col-md-6 text-right">
                <select id="publish-status" class="form-control" style="display:inline-block; width:auto; vertical-align: middle;">
                    <option value="1" {if $active == 1}selected="selected"{/if}>{l s='Published' mod='awvisualmerchandising'}</option>
                    <option value="0" {if $active == 0}selected="selected"{/if}>{l s='Unpublished' mod='awvisualmerchandising'}</option>
                </select>
                <button type="button" class="btn btn-default" id="save-status" style="margin-left: 10px; vertical-align: middle;">
                    <i class="icon-save"></i>
                    {l s='Save' mod='awvisualmerchandising'}
                </button>
                
                <br />
                <p class="helper-text">
                    {l s='Publish or Unpublish your changes.' mod='awvisualmerchandising'}
                </p>
            </div>
        </div>
        
    </div>
</div>
