<?php
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2025 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

$sql = [];

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'awvisualmerchandising` (
    `id_awvisualmerchandising` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_category` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    `description` TEXT COLLATE utf8mb4_unicode_ci,
    `desktop_rows` INT UNSIGNED NOT NULL DEFAULT 1,
    `mobile_rows` INT UNSIGNED NOT NULL,
    `active` TINYINT UNSIGNED NOT NULL,
    `date_add` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `date_upd` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id_awvisualmerchandising`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'awvisualmerchandising_hidden_product` (
    `id_hidden_product` BIGINT NOT NULL AUTO_INCREMENT,
    `id_awvisualmerchandising` BIGINT NOT NULL,
    `id_product` BIGINT NOT NULL,
    PRIMARY KEY (`id_hidden_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'awvisualmerchandising_pinned_product` (
    `id_pinned_product` BIGINT NOT NULL AUTO_INCREMENT,
    `id_awvisualmerchandising` BIGINT NOT NULL,
    `id_product` BIGINT NOT NULL,
    `sort` INT NOT NULL,
    PRIMARY KEY (`id_pinned_product`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'awvisualmerchandising_rules` (
    `id` BIGINT NOT NULL AUTO_INCREMENT,
    `id_awvisualmerchandising` BIGINT NOT NULL,
    `rule_type` VARCHAR(255) COLLATE utf8mb4_general_ci NOT NULL,
    `sort` INT NOT NULL,
    `rule_data` TEXT COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;';

foreach ($sql as $query) {
    if (Db::getInstance()->execute($query) === false) {
        return false;
    }
}

return true;
