/**
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
*
* Don't forget to prefix your containers with your own identifier
* to avoid any conflicts with others containers.
*/


function updateUIProducts() {
    
    let count;
    if ($(window).width() < 768) {
        count = typeof mobileItems !== 'undefined' ? parseInt(mobileItems, 10) : 0;
    } else {
        count = typeof desktopItems !== 'undefined' ? parseInt(desktopItems, 10) : 0;
    }

    if (count === 0) {
        return;
    }

    let mobileColSpan = Math.floor(12 / (typeof mobileItems !== 'undefined' ? parseInt(mobileItems, 10) : 1));
    let desktopColSpan = Math.floor(12 / (typeof desktopItems !== 'undefined' ? parseInt(desktopItems, 10) : 1));

    let colClasses = "col-xs-" + mobileColSpan + " col-md-" + desktopColSpan;


$(".products > .js-productt").each(function () {
  $(this)
    .removeClass(function (index, className) {
      return (className.match(/(^|\s)col-(xs|sm|md|lg|xl)-\d+/g) || []).join(" ");
    })
    .addClass(colClasses);
});


}

$(document).ready(function() {
    updateUIProducts();
    const bodyObserver = new MutationObserver(mutations => {
        mutations.forEach(mutation => {
            if (mutation.type === 'childList' && mutation.addedNodes.length) {
                $(mutation.addedNodes).each(function() {
                    if ($(this).find('.products').length) {
                        updateUIProducts();
                    }
                });
            }
        });
    });

    bodyObserver.observe(document.body, { childList: true, subtree: true });

});


