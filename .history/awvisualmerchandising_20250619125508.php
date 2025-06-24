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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2025 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Awvisualmerchandising extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'awvisualmerchandising';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'PrestaEasy';
        $this->need_instance = 1;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Visual Merchandising');
        $this->description = $this->l('Visual Merchandising module for Prestashop 8');

        $this->ps_versions_compliancy = array('min' => '8.0', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        include(dirname(__FILE__) . '/sql/install.php');

        Configuration::updateValue('AWVISUALMERCHANDISING_LIST_VIEW', false);
        Configuration::updateValue('AWVISUALMERCHANDISING_LIST_VIEW_MOBILE', false);

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerTab() &&
            $this->registerHook('displayBackOfficeHeader') &&
            $this->registerHook('actionProductSearchProviderRunQueryAfter');
    }

    public function uninstall()
    {
        Configuration::deleteByName('AWVISUALMERCHANDISING_LIST_VIEW');
        Configuration::deleteByName('AWVISUALMERCHANDISING_LIST_VIEW_MOBILE');

        $id_tab = (int) Tab::getIdFromClassName('AdminMerchandising');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            $tab->delete();
        }
        return parent::uninstall();
    }

    private function registerTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminMerchandising';
        $tab->name = [];

        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = 'Visual Merchandising';
        }

        $tab->id_parent = (int) Tab::getIdFromClassName('AdminCatalog');
        $tab->module = $this->name;

        return $tab->add();
    }

    public function getContent()
    {
        $redirectUrl = $this->context->link->getAdminLink('AdminMerchandising');
        Tools::redirectAdmin($redirectUrl);
        exit;
    }

    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitAwvisualmerchandisingModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'desc' => $this->l('Numbers of products per row on desktop'),
                        'name' => 'AWVISUALMERCHANDISING_LIST_VIEW',
                        'label' => $this->l('No. of products desktop'),
                    ),
                    array(
                        'col' => 4,
                        'type' => 'text',
                        'desc' => $this->l('Numbers of products per row on mobile'),
                        'name' => 'AWVISUALMERCHANDISING_LIST_VIEW_MOBILE',
                        'label' => $this->l('No. of products mobile'),
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    public function translate($string, $location = false)
    {
        return $this->l($string);
    }

    protected function getConfigFormValues()
    {
        return array(
            'AWVISUALMERCHANDISING_LIST_VIEW' => Configuration::get('AWVISUALMERCHANDISING_LIST_VIEW', null),
            'AWVISUALMERCHANDISING_LIST_VIEW_MOBILE' => Configuration::get('AWVISUALMERCHANDISING_LIST_VIEW_MOBILE', null),
        );
    }

    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = Tools::getValue('controller');
        if ($controller === 'AdminMerchandising') {
            Media::addJsDef([
                'token' => Tools::getAdminTokenLite('AdminMerchandising'),
            ]);
            $this->context->controller->addJS($this->_path . 'views/js/back.js');
            $this->context->controller->addJS($this->_path . 'views/js/product_search.js');
            $this->context->controller->addCSS($this->_path . 'views/css/back.css');
        }
    }

public function hookHeader()
{
    $categoryId = (int)Tools::getValue('id_category');
    if (!$categoryId && isset($this->context->controller->category)) {
        $categoryId = (int)$this->context->controller->category->id;
    }
    if (!$categoryId) {
        return;
    }

    $awvisualmerchandising = Db::getInstance()->getRow(
        'SELECT active, id_awvisualmerchandising FROM ' . _DB_PREFIX_ . 'awvisualmerchandising 
         WHERE id_category = ' . (int)$categoryId
    );

    if (!$awvisualmerchandising) {
        return;
    }

    $id_awvisualmerchandising = (int)$awvisualmerchandising['id_awvisualmerchandising'];
    $live_preview = Tools::getValue('live_preview');
    if ($live_preview && $this->validateSToken($id_awvisualmerchandising)) {
        $this->context->smarty->assign('live_preview', true);
        return $this->context->smarty->fetch($this->local_path . 'views/templates/front/live_preview_banner.tpl');
    } elseif (!$awvisualmerchandising['active']) {
        return;
    }

    $row = Db::getInstance()->getRow(
        'SELECT desktop_rows, mobile_rows FROM ' . _DB_PREFIX_ . 'awvisualmerchandising
         WHERE id_awvisualmerchandising = ' . (int)$id_awvisualmerchandising
    );

    $mobileItems = isset($row['mobile_rows']) ? (int)$row['mobile_rows'] : 0;
    $desktopItems = isset($row['desktop_rows']) ? (int)$row['desktop_rows'] : 0;

    Media::addJsDef([
        'mobileItems' => $mobileItems,
        'desktopItems' => $desktopItems,
    ]);

    $this->context->controller->registerJavascript(
        'module-awvisualmerchandising-front',
        'modules/' . $this->name . '/views/js/fronnt.js',
        ['position' => 'bottom']
    );

    $this->context->controller->registerStylesheet(
        'module-awvisualmerchandising-front-style',
        'modules/' . $this->name . '/views/css/front.css',
        ['media' => 'all']
    );
}



    public function hookActionProductSearchProviderRunQueryAfter($params)
    {
        $result = $params['result'];
        $products = $result->getProducts();
        if (Tools::getValue('order') && Tools::getValue('order') != 'product.position.asc') {
            return;
        }
        $query = $params['query'];
        $categoryId = $query->getIdCategory();

        if (!$categoryId) {
            return;
        }

        $awvisualmerchandising = Db::getInstance()->getRow(
            'SELECT active, id_awvisualmerchandising FROM ' . _DB_PREFIX_ . 'awvisualmerchandising 
             WHERE id_category = ' . (int)$categoryId
        );

        if (!$awvisualmerchandising) {
            return;
        }
        $id_awvisualmerchandising = (int)$awvisualmerchandising['id_awvisualmerchandising'];

        $live_preview = Tools::getValue('live_preview');

        if (!$live_preview && !$awvisualmerchandising['active']) {
            return;
        }

        if ($live_preview && !$this->validateSToken($id_awvisualmerchandising)) {
            return;
        }

        $rules = $this->getMerchandisingRules($categoryId, $id_awvisualmerchandising);

        if (empty($rules)) {
            return;
        }

        $rawSortedProducts = $this->applyMerchandisingRules($categoryId, $id_awvisualmerchandising, $rules);
        $returnSorted = [];
        foreach ($rawSortedProducts as $product) {
            $returnSorted[]['id_product'] = $product['id_product'];
        }

        $pinnedProducts = Db::getInstance()->executeS(
            'SELECT id_product 
             FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_pinned_product 
             WHERE id_awvisualmerchandising = ' . (int)$id_awvisualmerchandising . '
             ORDER BY sort'
        );
        $returnSorted = array_merge($pinnedProducts, $returnSorted);

        $page = (int)Tools::getValue('p', 1);
        $perPage = (int)Tools::getValue('n', 10);
        $pagination = $this->paginate($returnSorted, $page, $perPage);

        $result->setProducts($pagination['products']);
        $result->setTotalProductsCount($pagination['total']);
    }

    private function validateSToken($id_awvisualmerchandising)
    {
        $live_preview = Tools::getValue('live_preview');
        $secure_token = Tools::getValue('secure_token');

        $expectedToken = Tools::hash(
            'Awmerchandising' .
                $id_awvisualmerchandising .
                'live_preview=1' .
                'secure_token=' .
                Configuration::get('PS_SHOP_DOMAIN') .
                Tools::substr(Tools::hash(Configuration::get('PS_SHOP_DOMAIN')), 0, 10)
        );

        if ($live_preview != 1 || $secure_token !== $expectedToken) {
            return false;
        }
        return true;
    }

    public function getMerchandisingRules($categoryId, $id_awvisualmerchandising)
    {
        $sql = 'SELECT *
            FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_rules
            WHERE id_awvisualmerchandising = ' . (int)$id_awvisualmerchandising . '
            ORDER BY sort ASC';
        $rules = Db::getInstance()->executeS($sql);

        $allAttributes = $this->getAttributes();
        $allFeatures = $this->getFeatures();

        foreach ($rules as &$rule) {
            $rule['rule_data'] = json_decode($rule['rule_data'], true);

            if (isset($rule['rule_data']['attributes']) && is_array($rule['rule_data']['attributes'])) {
                $attributesValues = [];
                foreach ($rule['rule_data']['attributes'] as $groupName => $attrIds) {
                    if (isset($allAttributes[$groupName])) {
                        foreach ($attrIds as $id) {
                            foreach ($allAttributes[$groupName] as $attribute) {
                                if ($attribute['id'] == $id) {
                                    $attributesValues[$groupName][] = $attribute;
                                }
                            }
                        }
                    }
                }
                $rule['rule_data']['attributes_values'] = $attributesValues;
                unset($rule['rule_data']['attributes']);
            }

            if (isset($rule['rule_data']['features']) && is_array($rule['rule_data']['features'])) {
                $featuresValues = [];
                foreach ($rule['rule_data']['features'] as $featureId => $featureValue) {
                    foreach ($allFeatures as $features) {
                        if ($features['id_feature'] == $featureId) {
                            $selectedFeature = null;
                            foreach ($features['values'] as $value) {
                                if ($value['id_feature_value'] == $featureValue) {
                                    $selectedFeature = $value;
                                    break;
                                }
                            }
                            if ($selectedFeature !== null) {
                                $featuresValues[$featureId] = $selectedFeature;
                                $featuresValues[$featureId]['featurename'] = $features['name'];
                            }
                        }
                    }
                }
                $rule['rule_data']['features_values'] = $featuresValues;
                unset($rule['rule_data']['features']);
            }

            if (isset($rule['rule_data']['brand']) && $rule['rule_data']['brand']) {
                $brandId = (int)$rule['rule_data']['brand'];
                $brandName = Db::getInstance()->getValue("SELECT name FROM " . _DB_PREFIX_ . "manufacturer WHERE id_manufacturer = " . $brandId);
                $rule['rule_data']['brand_name'] = $brandName;
            }

            if (isset($rule['rule_data']['supplier']) && $rule['rule_data']['supplier']) {
                $supplierId = (int)$rule['rule_data']['supplier'];
                $supplierName = Db::getInstance()->getValue("SELECT name FROM " . _DB_PREFIX_ . "supplier WHERE id_supplier = " . $supplierId);
                $rule['rule_data']['supplier_name'] = $supplierName;
            }

            if (isset($rule['rule_data']['newest']) && $rule['rule_data']['newest']) {
                $rule['rule_data']['newest'] = (int)$rule['rule_data']['newest'];
            }

            if (isset($rule['rule_data']['discounted']) && $rule['rule_data']['discounted']) {
                $rule['rule_data']['discounted'] = (int)$rule['rule_data']['discounted'];
            }
        }

        $nonBuryRules = [];
        $buryRules = [];
        foreach ($rules as &$rule) {
            if ($rule['rule_type'] == 'bury') {
                $buryRules[] = $rule;
            } else {
                $nonBuryRules[] = $rule;
            }
        }

        $rules = array_merge($buryRules, $nonBuryRules);
        return $rules;
    }

    public function getAttributes()
    {
        $query = new DbQuery();
        $query->select('a.id_attribute, al.name, agl.name as group_name');
        $query->from('attribute', 'a');
        $query->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute AND al.id_lang = ' . (int)$this->context->language->id);
        $query->leftJoin('attribute_group_lang', 'agl', 'a.id_attribute_group = agl.id_attribute_group AND agl.id_lang = ' . (int)$this->context->language->id);
        $query->orderBy('agl.name ASC, al.name ASC');

        $attributes = Db::getInstance()->executeS($query);

        $groupedAttributes = [];
        foreach ($attributes as $attribute) {
            $groupedAttributes[$attribute['group_name']][] = [
                'id' => $attribute['id_attribute'],
                'name' => $attribute['name']
            ];
        }
        return $groupedAttributes;
    }

    public function getFeatures()
    {
        $query = new DbQuery();
        $query->select('f.id_feature, fl.name');
        $query->from('feature', 'f');
        $query->leftJoin('feature_lang', 'fl', 'f.id_feature = fl.id_feature AND fl.id_lang = ' . (int)$this->context->language->id);
        $query->orderBy('fl.name ASC');

        $features = Db::getInstance()->executeS($query);

        foreach ($features as &$feature) {
            $featureValues = Db::getInstance()->executeS("SELECT fvl.id_feature_value, fvl.value FROM " . _DB_PREFIX_ . "feature_value_lang fvl INNER JOIN " . _DB_PREFIX_ . "feature_value fv ON fvl.id_feature_value = fv.id_feature_value WHERE fv.id_feature = " . (int)$feature['id_feature'] . " AND fvl.id_lang = " . (int)$this->context->language->id);
            $feature['values'] = $featureValues;
        }

        return $features;
    }

    public function applyMerchandisingRules($id_category, $id_awvisualmerchandising, $rules)
    {
        $buried = [];
        $boosted = [];
        $remaining = [];
        $products = $this->getCategoryProducts($id_category, $id_awvisualmerchandising);

        $countAll = count($products);

        foreach ($products as $product) {
            $maxBuriedScore = 0;
            $maxBoostedScore = 0;

            foreach ($rules as $rule) {
                $matchCount = $this->countMatchedConditions($product, $rule);
                if ($rule['rule_type'] === 'bury') {
                    if ($matchCount > $maxBuriedScore) {
                        $maxBuriedScore = $matchCount;
                    }
                } elseif ($rule['rule_type'] === 'boost') {
                    if ($matchCount > $maxBoostedScore) {
                        $maxBoostedScore = $matchCount;
                    }
                }
            }

            if ($maxBuriedScore > 0) {
                $buried[] = ['product' => $product, 'match_score' => $maxBuriedScore];
            } elseif ($maxBoostedScore > 0) {
                $boosted[] = ['product' => $product, 'match_score' => $maxBoostedScore];
            } else {
                $remaining[] = $product;
            }
        }

        $newBoosted = [];
        foreach ($rules as $key => &$rule) {
            $rule['matching_products'] = [];
            if ($rule['rule_type'] === 'boost') {
                foreach ($boosted as $bproduct) {
                    $product = $bproduct['product'];
                    $matchCondition = $this->getMatchedConditions($product, $rule);

                    if ($matchCondition) {
                        $newBoosted[$key][$product['id_product']]['product'] = $product;
                        $newBoosted[$key][$product['id_product']]['matchCondition'] = $matchCondition;
                        $newBoosted[$key][$product['id_product']]['match_count_old'] = $bproduct['match_score'];
                    }
                }
            }
        }

        $boosted = $this->processAndSortBoostedProducts($newBoosted, $countAll);

        usort($buried, function ($a, $b) {
            if ($a['match_score'] === $b['match_score']) {
                $aLow = isset($a['product']['lowstock']) ? $a['product']['lowstock'] : 0;
                $bLow = isset($b['product']['lowstock']) ? $b['product']['lowstock'] : 0;
                if ($aLow === $bLow) {
                    return strtotime($a['product']['date_add']) <=> strtotime($b['product']['date_add']);
                }
                return $aLow <=> $bLow;
            }
            return $a['match_score'] <=> $b['match_score'];
        });

        usort($remaining, function ($a, $b) {
            return strtotime($b['date_add']) <=> strtotime($a['date_add']);
        });

        $boostedProducts = array_column($boosted, 'product');
        $buriedProducts = array_column($buried, 'product');

        return array_merge($boostedProducts, $remaining, $buriedProducts);
    }

    public function getCategoryProducts($id_category, $id_awvisualmerchandising)
    {
        $allProductsQuery = '
        SELECT DISTINCT 
            p.id_product,
            p.date_add,
            pl.name,
            p.reference AS sku,
            p.ean13,
            p.id_manufacturer,
            m.name AS manufacturer_name,
            i.id_image,
            s.id_supplier,
            s.name AS supplier_name,
            COALESCE(al.id_attribute, 0) AS attribute_id, 
            COALESCE(al.name, "") AS attribute,
            COALESCE(fvl.id_feature_value, 0) AS feature_id, 
            COALESCE(fvl.value, "") AS feature,
            IF(DATEDIFF(NOW(), p.date_add) <= 30, 1, 0) AS is_new,
            IF(pd.id_product IS NOT NULL, 1, 0) AS is_discounted,
            IF(sa.quantity <= 5, 1, 0) AS lowstock
        FROM ' . _DB_PREFIX_ . 'product p
        LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl 
            ON p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . '
        LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m 
            ON p.id_manufacturer = m.id_manufacturer
        LEFT JOIN ' . _DB_PREFIX_ . 'image i 
            ON p.id_product = i.id_product AND i.cover = 1
        LEFT JOIN ' . _DB_PREFIX_ . 'product_supplier ps 
            ON p.id_product = ps.id_product
        LEFT JOIN ' . _DB_PREFIX_ . 'supplier s 
            ON ps.id_supplier = s.id_supplier
        LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute pa 
            ON p.id_product = pa.id_product
        LEFT JOIN ' . _DB_PREFIX_ . 'product_attribute_combination pac 
            ON pa.id_product_attribute = pac.id_product_attribute
        LEFT JOIN ' . _DB_PREFIX_ . 'attribute_lang al 
            ON pac.id_attribute = al.id_attribute AND al.id_lang = ' . (int)$this->context->language->id . '
        LEFT JOIN ' . _DB_PREFIX_ . 'feature_product fp 
            ON p.id_product = fp.id_product
        LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl 
            ON fp.id_feature_value = fvl.id_feature_value AND fvl.id_lang = ' . (int)$this->context->language->id . '
        LEFT JOIN ' . _DB_PREFIX_ . 'specific_price pd 
            ON p.id_product = pd.id_product
        LEFT JOIN ' . _DB_PREFIX_ . 'stock_available sa 
            ON p.id_product = sa.id_product AND sa.id_product_attribute = 0
        WHERE p.id_category_default = (' . $id_category . ')
        AND p.id_product NOT IN (
            SELECT id_product FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_pinned_product
            WHERE id_awvisualmerchandising = ' . (int)$id_awvisualmerchandising . '
            UNION
            SELECT id_product FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_hidden_product
            WHERE id_awvisualmerchandising = ' . (int)$id_awvisualmerchandising . '
        )
        ';

        $allProducts = Db::getInstance()->executeS($allProductsQuery);

        $groupedData = [];

        foreach ($allProducts as $item) {
            $productId = $item['id_product'];
            $attributeId = (int)$item['attribute_id'];
            $featureId = (int)$item['feature_id'];

            if (!isset($groupedData[$productId])) {
                $groupedData[$productId] = [
                    'id_product' => $productId,
                    'name' => $item['name'],
                    'date_add' => $item['date_add'],
                    'sku' => $item['sku'],
                    'ean13' => $item['ean13'],
                    'id_manufacturer' => $item['id_manufacturer'],
                    'manufacturer_name' => $item['manufacturer_name'],
                    'id_image' => $item['id_image'],
                    'id_supplier' => $item['id_supplier'],
                    'supplier_name' => $item['supplier_name'],
                    'is_new' => $item['is_new'],
                    'is_discounted' => $item['is_discounted'],
                    'lowstock' => $item['lowstock'],
                    'attributes' => [],
                    'features' => []
                ];
            }

            if ($attributeId > 0 && !in_array($attributeId, $groupedData[$productId]['attributes'])) {
                $groupedData[$productId]['attributes'][] = $attributeId;
            }

            if ($featureId > 0 && !in_array($featureId, $groupedData[$productId]['features'])) {
                $groupedData[$productId]['features'][] = $featureId;
            }
        }

        return array_values($groupedData);
    }

    public function processAndSortBoostedProducts($data, $totalCount)
    {
        $flatList = [];

        foreach ($data as $group) {
            foreach ($group as $productId => $productData) {
                $matchPercentage = 0;

                foreach ($productData['matchCondition'] as $condition) {
                    $matchPercentage += $condition['percentage'];
                }

                $flatList[] = [
                    'productId' => $productId,
                    'product' => $productData['product'],
                    'matchCondition' => $productData['matchCondition'],
                    'match_count_old' => $productData['match_count_old'],
                    'total_percentage' => $matchPercentage
                ];
            }
        }

        $grouped = [];
        foreach ($flatList as $item) {
            $percent = $item['total_percentage'];
            if (!isset($grouped[$percent])) {
                $grouped[$percent] = [];
            }
            $grouped[$percent][] = $item;
        }

        krsort($grouped);

        $finalSorted = [];

        foreach ($grouped as $percentage => $items) {
            usort($items, function ($a, $b) {
                return $b['match_count_old'] <=> $a['match_count_old'];
            });

            $limit = round(($percentage / 100) * $totalCount);

            $top = array_slice($items, 0, $limit);
            $rest = array_slice($items, $limit);

            $finalSorted = array_merge($finalSorted, $top, $rest);
        }

        $uniqueProducts = [];
        foreach ($finalSorted as $item) {
            if (!isset($uniqueProducts[$item['productId']])) {
                $uniqueProducts[$item['productId']] = [
                    'product' => $item['product'],
                    'match_score' => $item['match_count_old']
                ];
            }
        }
        $finalSorted = array_values($uniqueProducts);
        return $finalSorted;
    }

    public function getMatchedConditions($product, $rule)
    {
        $ruleData = $rule['rule_data'];
        $matches = [];

        if (!empty($ruleData['brand']) && $product['id_manufacturer'] == $ruleData['brand']) {
            $matches[] = [
                'condition' => 'brand',
                'percentage' => 0,
            ];
        }

        if (!empty($ruleData['supplier']) && $product['id_supplier'] == $ruleData['supplier']) {
            $matches[] = [
                'condition' => 'supplier',
                'percentage' => 0,
            ];
        }

        if (!empty($ruleData['newest']) && $product['is_new'] == 1) {
            $matches[] = [
                'condition' => 'newest',
                'percentage' => 0,
            ];
        }

        if (!empty($ruleData['discounted']) && $product['is_discounted'] == 1) {
            $matches[] = [
                'condition' => 'discounted',
                'percentage' => 0,
            ];
        }

        if (!empty($ruleData['lowstock']) && $product['lowstock'] == 1) {
            $matches[] = [
                'condition' => 'lowstock',
                'percentage' => 0,
            ];
        }

        if (!empty($ruleData['attributes_values']) && !empty($product['attributes'])) {
            foreach ($ruleData['attributes_values'] as $group => $values) {
                $percentage = isset($ruleData['attributes_ranges'][$group]) ? (int)$ruleData['attributes_ranges'][$group] : 0;
                foreach ($values as $attribute) {
                    if (in_array($attribute['id'], $product['attributes'])) {
                        $matches[] = [
                            'condition' => 'attribute',
                            'group' => $group,
                            'attribute_id' => $attribute['id'],
                            'percentage' => $percentage,
                        ];
                    }
                }
            }
        }

        if (!empty($ruleData['features_values']) && !empty($product['features'])) {
            foreach ($ruleData['features_values'] as $idvalue => $feature) {
                $idFeature = $feature['id_feature_value'];
                $percentage = 1;
                $percentage = isset($ruleData['features_ranges'][$idvalue]) ? (int)$ruleData['features_ranges'][$idvalue] : 0;

                if (in_array($idFeature, $product['features'])) {
                    $matches[] = [
                        'condition' => 'feature',
                        'feature_id' => $idFeature,
                        'percentage' => $percentage,
                    ];
                }
            }
        }

        return $matches;
    }

    public function countMatchedConditions($product, $rule)
    {
        $ruleData = $rule['rule_data'];
        $score = 0;

        if (!empty($ruleData['brand']) && $product['id_manufacturer'] == $ruleData['brand']) {
            $score += 100;
        }

        if (!empty($ruleData['supplier']) && $product['id_supplier'] == $ruleData['supplier']) {
            $score += 100;
        }

        if (!empty($ruleData['newest']) && $product['is_new'] == 1) {
            $score += 100;
        }

        if (!empty($ruleData['discounted']) && $product['is_discounted'] == 1) {
            $score += 100;
        }

        if (!empty($ruleData['lowstock']) && $product['lowstock'] == 1) {
            $score += 100;
        }

        if (!empty($ruleData['attributes_values']) && !empty($product['attributes'])) {
            foreach ($ruleData['attributes_values'] as $group => $values) {
                $percentage = isset($ruleData['attributes_ranges'][$group]) ? (int)$ruleData['attributes_ranges'][$group] : 1;
                foreach ($values as $attribute) {
                    if (in_array($attribute['id'], $product['attributes'])) {
                        $score += 100 * ($percentage / 100);
                    }
                }
            }
        }

        if (!empty($ruleData['features_values']) && !empty($product['features'])) {
            foreach ($ruleData['features_values'] as $feature) {
                $idFeature = $feature['id_feature_value'];
                $percentage = 1;
                if (isset($ruleData['features_ranges'])) {
                    foreach ($ruleData['features_ranges'] as $fid => $r) {
                        if ($fid == $feature['featurename'] || $fid == $feature['id_feature_value']) {
                            $percentage = (int)$r;
                            break;
                        }
                    }
                }
                if (in_array($idFeature, $product['features'])) {
                    $score += 100 * ($percentage / 100);
                }
            }
        }

        return $score;
    }

    private function paginate(array $products, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $paginated = array_slice($products, $offset, $perPage);

        return [
            'products' => $paginated,
            'total' => count($products),
        ];
    }
}
