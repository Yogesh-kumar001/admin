<?php
/**
 * 2007-2025 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2025 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License (AFL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once _PS_MODULE_DIR_ . 'awvisualmerchandising/classes/Merchandising.php';

class AdminMerchandisingController extends ModuleAdminController
{
    public $translator;
    public $actions;

    public function __construct()
    {
        $this->table = 'awvisualmerchandising';
        $this->identifier = 'id_awvisualmerchandising';
        $this->className = 'Merchandising';
        $this->bootstrap = true;
        parent::__construct();
        $this->fields_list = [
            'id_awvisualmerchandising' => [
                'title' => $this->module->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'name' => [
                'title' => $this->module->l('Name'),
            ],
            'description' => [
                'title' => $this->module->l('Description'),
            ],
            'category_name' => [
                'title' => $this->module->l('Category'),
                'filter_key' => 'category!name',
            ],
            'active' => [
                'title' => $this->module->l('Published'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'class' => 'fixed-width-sm',
            ],
            'date_add' => [
                'title' => $this->module->l('Date Added'),
                'type' => 'datetime',
            ],
            'date_upd' => [
                'title' => $this->module->l('Date Updated'),
                'type' => 'datetime',
            ],
        ];
        $this->actions = ['edit'];
        $this->addJS($this->module->getPathUri() . 'views/js/sweetalert2.all.min.js');
        $this->addCSS($this->module->getPathUri() . 'views/css/sweetalert2.min.css');
        $this->addCSS($this->module->getPathUri() . 'views/css/select2.min.css');
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        $this->page_header_toolbar_btn['new_merchandising'] = [
            'href' => self::$currentIndex . '&addawvisualmerchandising&token=' . $this->token,
            'desc' => $this->module->l('Add new'),
            'icon' => 'process-icon-new',
        ];
    }

    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        global $cookie;
        $this->_select = 'cl.name as category_name';
        $this->_join = 'LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (a.id_category = cl.id_category AND cl.id_lang = ' . (int) $this->context->language->id . ')';
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
    }

    public function renderForm()
    {
        $categories = Category::getCategories($this->context->language->id, true, false);
        $id_awvisualmerchandising = Tools::getValue('id_awvisualmerchandising');

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Merchandising'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->module->l('Name'),
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->module->l('Description'),
                    'name' => 'description',
                    'autoload_rte' => false,
                ],
            ],
            'submit' => [
                'title' => $this->module->l('Save'),
            ],
        ];

        $output = '';
        if (!$id_awvisualmerchandising) {
            $this->fields_form['input'][] = [
                'type' => 'select',
                'label' => $this->module->l('Category'),
                'name' => 'id_category',
                'options' => [
                    'query' => $categories,
                    'id' => 'id_category',
                    'name' => 'name',
                ],
                'required' => true,
            ];
            $this->fields_form['input'][] = [
                'type' => 'free',
                'label' => $this->module->l('Information'),
                'name' => 'info_text',
                'desc' => $this->module->l('Once added, you can add pinned products and define rules.'),
            ];
        } else {
            $output = $this->renderPanels($id_awvisualmerchandising);
        }

        $this->addJqueryUI('ui.sortable');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/product_search.js?v=' . time());
        $this->addJS($this->module->getPathUri() . 'views/js/select2.min.js');
        $this->addCss($this->module->getPathUri() . 'views/css/product_search.css');
        $output .= parent::renderForm();
        return $output;
    }

    private function renderPanels($id_awvisualmerchandising)
    {
        return $this->renderRightProductPanel($id_awvisualmerchandising) .
            $this->renderLeftRulesPanel($id_awvisualmerchandising);
    }

    private function renderLeftRulesPanel($id_awvisualmerchandising)
    {
        $allCategories = Category::getCategories($this->context->language->id, true, false);

        $categoryIndex = [];
        foreach ($allCategories as $cat) {
            $categoryIndex[$cat['id_category']] = $cat;
        }

        $groupedCategories = [];
        foreach ($allCategories as $cat) {
            $parentId = $cat['id_parent'];
            if (isset($categoryIndex[$parentId])) {
                $groupLabel = $categoryIndex[$parentId]['name'];
            } else {
                $groupLabel = $this->module->l('Root');
            }
            $groupedCategories[$groupLabel][] = [
                'id' => $cat['id_category'],
                'name' => $cat['name'],
            ];
        }

        $categories = $groupedCategories;

        $selectedCategory = Db::getInstance()->getValue(
            '
            SELECT id_category
            FROM ' . _DB_PREFIX_ . 'awvisualmerchandising
            WHERE id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising
        );
        $brands = Db::getInstance()->executeS('SELECT id_manufacturer as id, name FROM ' . _DB_PREFIX_ . 'manufacturer');
        $suppliers = Db::getInstance()->executeS('SELECT id_supplier as id, name FROM ' . _DB_PREFIX_ . 'supplier');

        $merchandising = Db::getInstance()->getRow('SELECT desktop_rows, mobile_rows FROM ' . _DB_PREFIX_ . 'awvisualmerchandising WHERE id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising);

        $this->context->smarty->assign([
            'id_awvisualmerchandising' => $id_awvisualmerchandising,
            'pathUri' => $this->module->getPathUri(),
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'desktopItems' => isset($merchandising['desktop_rows']) ? (int) $merchandising['desktop_rows'] : 3,
            'mobileItems' => isset($merchandising['mobile_rows']) ? (int) $merchandising['mobile_rows'] : 2,
            'ruleType' => 'boost',
            'productFeature' => [],
            'attributes' => $this->getAttributes(),
            'features' => $this->getFeatures(),
            'rules' => $this->getRules(),
            'brands' => $brands,
            'suppliers' => $suppliers,
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/left-rules-panel.tpl');
    }

    private function renderRightProductPanel($id_awvisualmerchandising)
    {
        $merchandising = Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . 'awvisualmerchandising WHERE id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising
        );
        $domainHash = Tools::substr(Tools::hash(Configuration::get('PS_SHOP_DOMAIN')), 0, 10);
        $sToken = Tools::hash('Awmerchandising' . $id_awvisualmerchandising . 'live_preview=1secure_token=' . Configuration::get('PS_SHOP_DOMAIN') . $domainHash);
        $this->context->smarty->assign([
            'searchAction' => $this->context->link->getAdminLink('AdminMerchandising') . '&ajax=1&action=searchProducts',
            'id_awvisualmerchandising' => $id_awvisualmerchandising,
            'pinnedProducts' => $this->getPinnedProductsList($id_awvisualmerchandising),
            'ProductsList' => $this->getCategoryProducts($id_awvisualmerchandising),
            'pathUri' => $this->module->getPathUri(),
            'LocalPath' => $this->module->getLocalPath(),
            'desktopItems' => isset($merchandising['desktop_rows']) ? (int) $merchandising['desktop_rows'] : 3,
            'mobileItems' => isset($merchandising['mobile_rows']) ? (int) $merchandising['mobile_rows'] : 2,
            'categoryLink' => isset($merchandising['id_category'])
                ? $this->context->link->getCategoryLink(
                    new Category($merchandising['id_category'], $this->context->language->id),
                    null,
                    $this->context->language->id
                ) . '?live_preview=1&secure_token=' . $sToken
                : '',
            'active' => $merchandising['active'],
        ]);

        return $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/right-product-panel.tpl');
    }

    private function getPinnedProductsList($id_awvisualmerchandising)
    {
        $pinnedProduct = Db::getInstance()->executeS('
            SELECT pp.id_pinned_product as id, pl.link_rewrite, p.reference as sku, p.ean13, pl.name, m.name as manufacturer_name, i.id_image
            FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_pinned_product pp
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pp.id_product = pl.id_product AND pl.id_lang = ' . (int) $this->context->language->id . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'product p ON (pp.id_product = p.id_product)
            LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (p.id_manufacturer = m.id_manufacturer)
            LEFT JOIN ' . _DB_PREFIX_ . 'image i ON (pp.id_product = i.id_product AND i.cover = 1)
            WHERE pp.id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising . '
            ORDER BY pp.sort ASC
        ');
        if (!$pinnedProduct) {
            $pinnedProduct = [];
        }

        $hiddenProducts = Db::getInstance()->executeS('
            SELECT hp.id_hidden_product as id, pl.link_rewrite, p.reference as sku, p.ean13, pl.name, m.name as manufacturer_name, i.id_image
            FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_hidden_product hp
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (hp.id_product = pl.id_product AND pl.id_lang = ' . (int) $this->context->language->id . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'product p ON (hp.id_product = p.id_product)
            LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (p.id_manufacturer = m.id_manufacturer)
            LEFT JOIN ' . _DB_PREFIX_ . 'image i ON (hp.id_product = i.id_product AND i.cover = 1)
            WHERE hp.id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising . '
            ORDER BY hp.id_hidden_product ASC
        ');

        if (!$hiddenProducts) {
            $hiddenProducts = [];
        }
        foreach ($hiddenProducts as &$hidden) {
            $hidden['hidden'] = true;
        }

        $mergedProducts = array_merge($pinnedProduct, $hiddenProducts);

        return $mergedProducts;
    }

    public function getCategoryProducts($id_awvisualmerchandising, $page = 0)
    {
        $limit = 20;
        $offset = $page * $limit;

        $categoryQuery = '
        SELECT id_category FROM ' . _DB_PREFIX_ . 'awvisualmerchandising
        WHERE id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising . '
        LIMIT 1
    ';
        $module = Module::getInstanceByName('awvisualmerchandising');
        if ($module && method_exists($module, 'getMerchandisingRules') && method_exists($module, 'applyMerchandisingRules')) {
            $rules = $module->getMerchandisingRules($categoryQuery, $id_awvisualmerchandising);
            $sortedProducts = $module->applyMerchandisingRules($categoryQuery, $id_awvisualmerchandising, $rules);
            $products = array_slice($sortedProducts, $offset, $limit);

            foreach ($products as &$product) {
                $product['image'] = $this->context->link->getImageLink(
                    'product',
                    $product['id_image'],
                    'home_default'
                );
            }
            return $products;
        }
        return [];
    }
    public function countMatchedConditions($product, $rule)
    {
        $ruleData = $rule['rule_data'];
        $count = 0;

        if (!empty($ruleData['brand']) && $product['id_manufacturer'] == $ruleData['brand']) {
            ++$count;
        }

        if (!empty($ruleData['supplier']) && $product['id_supplier'] == $ruleData['supplier']) {
            ++$count;
        }

        if (!empty($ruleData['newest']) && $product['is_new'] == 1) {
            ++$count;
        }

        if (!empty($ruleData['discounted']) && $product['is_discounted'] == 1) {
            ++$count;
        }
        if (!empty($ruleData['lowstock']) && $product['lowstock'] == 1) {
            ++$count;
        }

        if (!empty($ruleData['attributes_values'])) {
            $flatAttributeIds = [];
            foreach ($ruleData['attributes_values'] as $attributes) {
                $flatAttributeIds = array_merge($flatAttributeIds, array_column($attributes, 'id'));
            }
            $count += count(array_intersect($flatAttributeIds, $product['attributes']));
        }

        if (!empty($ruleData['features_values']) && !empty($product['features'])) {
            $ruleFeatureIds = array_column($ruleData['features_values'], 'id_feature_value');
            $count += count(array_intersect($ruleFeatureIds, $product['features']));
        }

        return $count;
    }

    public function ajaxProcessGetPinnedProducts()
    {
        $id_awvisualmerchandising = (int) Tools::getValue('id_awvisualmerchandising');
        $this->context->smarty->assign([
            'id_awvisualmerchandising' => $id_awvisualmerchandising,
            'pinnedProducts' => $this->getPinnedProductsList($id_awvisualmerchandising),
            'pathUri' => $this->module->getPathUri(),
            'LocalPath' => $this->module->getLocalPath(),
            'link' => $this->context->link,
        ]);
        echo $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/pinned-products.tpl');
        exit;
    }

    public function ajaxProcessSearchProducts()
    {
        $query = pSQL(Tools::getValue('query'));

        $products = Db::getInstance()->executeS('
            SELECT p.id_product, pl.name, p.reference as sku, p.ean13, m.name as manufacturer_name, i.id_image
            FROM ' . _DB_PREFIX_ . 'product p
            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (p.id_product = pl.id_product AND pl.id_lang = ' . (int) $this->context->language->id . ')
            LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (p.id_manufacturer = m.id_manufacturer)
            LEFT JOIN ' . _DB_PREFIX_ . 'image i ON (p.id_product = i.id_product AND i.cover = 1)
            WHERE pl.name LIKE "%".$query."%"
            OR p.reference LIKE "%".$query."%"
            OR p.ean13 LIKE "%".$query."%"
            LIMIT 20
        ');

        foreach ($products as &$product) {
            $product['image'] = $this->context->link->getImageLink('product', $product['id_image'], 'home_default');
        }

        exit(json_encode($products));
    }

    public function ajaxProcessUpdatePinnedProductPosition()
    {
        $positions = Tools::getValue('positions');
        if (is_array($positions)) {
            foreach ($positions as $sort => $idPinnedProduct) {
                Db::getInstance()->update(
                    'awvisualmerchandising_pinned_product',
                    ['sort' => (int) $sort],
                    'id_pinned_product = ' . (int) $idPinnedProduct
                );
            }

            exit(json_encode(['success' => true]));
        }

        exit(json_encode(['success' => false, 'message' => 'Invalid input']));
    }

    public function ajaxProcesshideProduct()
    {
        $id_merchandising = (int) Tools::getValue('id_awvisualmerchandising');
        $id_product = (int) Tools::getValue('id_product');

        $exists = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*)'
                . ' FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_hidden_product'
                . ' WHERE id_awvisualmerchandising = ' . $id_merchandising . ' AND id_product = ' . $id_product
        );
        if ($exists) {
            exit(json_encode(['success' => false, 'message' => 'Product is already hidden.']));
        }
        if ($id_merchandising && $id_product) {
            Db::getInstance()->insert('awvisualmerchandising_hidden_product', [
                'id_awvisualmerchandising' => $id_merchandising,
                'id_product' => $id_product,
            ]);
            exit(json_encode(['success' => true, 'message' => 'Product hidden successfully.']));
        }
        exit(json_encode(['success' => false, 'message' => 'Invalid input']));
    }

    public function ajaxProcessAddPinnedProduct()
    {
        $id_merchandising = (int) Tools::getValue('id_awvisualmerchandising');
        $id_product = (int) Tools::getValue('id_product');

        $exists = (int) Db::getInstance()->getValue(
            'SELECT COUNT(*)'
                . ' FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_pinned_product'
                . ' WHERE id_awvisualmerchandising = ' . $id_merchandising . ' AND id_product = ' . $id_product
        );
        if ($exists) {
            exit(json_encode(['success' => false, 'message' => 'Product is already pinned.']));
        }
        $sort = (int) Db::getInstance()->getValue(
            'SELECT MAX(sort) + 1'
                . ' FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_pinned_product'
                . ' WHERE id_awvisualmerchandising = ' . $id_merchandising
        );
        if ($id_merchandising && $id_product) {
            Db::getInstance()->insert('awvisualmerchandising_pinned_product', [
                'id_awvisualmerchandising' => $id_merchandising,
                'id_product' => $id_product,
                'sort' => $sort,
            ]);
            exit(json_encode(['success' => true, 'message' => 'product added successfully.']));
        }
    }

    public function ajaxProcessRemoveHiddenProduct()
    {
        $id_hidden_product = (int) Tools::getValue('id_product');
        $id_merchandising = (int) Tools::getValue('id_awvisualmerchandising');

        if (!$id_hidden_product || !$id_merchandising) {
            exit(json_encode(['success' => false, 'message' => 'Invalid product ID or merchandising ID']));
        }

        $deleted = Db::getInstance()->delete(
            'awvisualmerchandising_hidden_product',
            'id_hidden_product = ' . $id_hidden_product . ' AND id_awvisualmerchandising = ' . $id_merchandising
        );
        if ($deleted) {
            exit(json_encode(['success' => true, 'message' => 'Hidden product removed successfully.']));
        }
        exit(json_encode(['success' => false, 'message' => 'Failed to remove hidden product.']));
    }

    public function ajaxProcessRemovePinnedProduct()
    {
        $id_pinned_product = (int) Tools::getValue('id_product');
        $id_merchandising = (int) Tools::getValue('id_awvisualmerchandising');

        if (!$id_pinned_product || !$id_merchandising) {
            exit(json_encode(['success' => false, 'message' => 'Invalid product ID or merchandising ID']));
        }

        $deleted = Db::getInstance()->delete(
            'awvisualmerchandising_pinned_product',
            'id_pinned_product = ' . $id_pinned_product . ' AND id_awvisualmerchandising = ' . $id_merchandising
        );
        if ($deleted) {
            exit(json_encode(['success' => true, 'message' => 'Pinned product removed successfully.']));
        }
        exit(json_encode(['success' => false, 'message' => 'Failed to remove pinned product.']));
    }

    public function ajaxProcessUpdateCategory()
    {
        $category_id = (int) Tools::getValue('category_id');
        $id_awvisualmerchandising = (int) Tools::getValue('id_awvisualmerchandising');

        if ($category_id && $id_awvisualmerchandising) {
            $updated = Db::getInstance()->update(
                'awvisualmerchandising',
                ['id_category' => $category_id],
                'id_awvisualmerchandising = ' . $id_awvisualmerchandising
            );
            if ($updated) {
                Db::getInstance()->delete(
                    'awvisualmerchandising_pinned_product',
                    'id_awvisualmerchandising = ' . $id_awvisualmerchandising
                );
                Db::getInstance()->delete(
                    'awvisualmerchandising_hidden_product',
                    'id_awvisualmerchandising = ' . $id_awvisualmerchandising
                );
                exit(json_encode([
                    'success' => true,
                    'message' => 'Category updated and associated pinned products deleted successfully.',
                ]));
            } else {
                exit(json_encode([
                    'success' => false,
                    'message' => 'Failed to update category.',
                ]));
            }
        }

        exit(json_encode(['success' => false, 'message' => 'Invalid input']));
    }

    public function ajaxProcessGetCategoryProduct()
    {
        $id_awvisualmerchandising = (int) Tools::getValue('id_awvisualmerchandising');
        $page = (int) Tools::getValue('page', 1);

        $products = $this->getCategoryProducts($id_awvisualmerchandising, $page);

        if (!$products) {
            exit(json_encode(['success' => false, 'message' => 'No products found']));
        }
        exit(json_encode(['success' => true, 'ProductsList' => $products]));
    }

    public function getAttributes()
    {
        $query = new DbQuery();
        $query->select('a.id_attribute, al.name, agl.name as group_name');
        $query->from('attribute', 'a');
        $query->leftJoin('attribute_lang', 'al', 'a.id_attribute = al.id_attribute AND al.id_lang = ' . (int) $this->context->language->id);
        $query->leftJoin('attribute_group_lang', 'agl', 'a.id_attribute_group = agl.id_attribute_group AND agl.id_lang = ' . (int) $this->context->language->id);
        $query->orderBy('agl.name ASC, al.name ASC');

        $attributes = Db::getInstance()->executeS($query);

        $groupedAttributes = [];
        foreach ($attributes as $attribute) {
            $groupedAttributes[$attribute['group_name']][] = [
                'id' => $attribute['id_attribute'],
                'name' => $attribute['name'],
            ];
        }

        return $groupedAttributes;
    }

    public function getFeatures()
    {
        $query = new DbQuery();
        $query->select('f.id_feature, fl.name');
        $query->from('feature', 'f');
        $query->leftJoin('feature_lang', 'fl', 'f.id_feature = fl.id_feature AND fl.id_lang = ' . (int) $this->context->language->id);
        $query->orderBy('fl.name ASC');

        $features = Db::getInstance()->executeS($query);

        foreach ($features as &$feature) {
            $featureValues = Db::getInstance()->executeS('SELECT fvl.id_feature_value, fvl.value FROM ' . _DB_PREFIX_ . 'feature_value_lang fvl INNER JOIN ' . _DB_PREFIX_ . 'feature_value fv ON fvl.id_feature_value = fv.id_feature_value WHERE fv.id_feature = ' . (int) $feature['id_feature'] . ' AND fvl.id_lang = ' . (int) $this->context->language->id);
            $feature['values'] = $featureValues;
        }

        return $features;
    }

    public function ajaxProcessupdateRuleSort()
    {
        $id_awvisualmerchandising = (int) Tools::getValue('id_awvisualmerchandising');
        $rules = Tools::getValue('sortedIds', []);

        if ($id_awvisualmerchandising && is_array($rules)) {
            foreach ($rules as $sort => $ruleId) {
                Db::getInstance()->update(
                    'awvisualmerchandising_rules',
                    ['sort' => (int) $sort],
                    'id = ' . (int) $ruleId . ' AND id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising
                );
            }
            exit(json_encode(['success' => true]));
        }
        exit(json_encode(['success' => false, 'message' => 'Invalid input']));
    }

    public function getRules($id_awvisualmerchandising = null)
    {
        if (!$id_awvisualmerchandising) {
            $id_awvisualmerchandising = Tools::getValue('id_awvisualmerchandising');
        }
        if (!$id_awvisualmerchandising) {
            return [];
        }
        $sql = 'SELECT *
            FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_rules
            WHERE id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising . '
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
                            $selectedFeature = [];
                            foreach ($features['values'] as $value) {
                                if ($value['id_feature_value'] == $featureValue) {
                                    $selectedFeature = $value;
                                    break;
                                }
                            }
                            $featuresValues[$featureId] = $selectedFeature;
                            $featuresValues[$featureId]['featurename'] = $features['name'];
                        }
                    }
                }
                $rule['rule_data']['features_values'] = $featuresValues;
                unset($rule['rule_data']['features']);
            }

            if (isset($rule['rule_data']['brand']) && $rule['rule_data']['brand']) {
                $brandId = (int) $rule['rule_data']['brand'];
                $brandName = Db::getInstance()->getValue('SELECT name FROM ' . _DB_PREFIX_ . 'manufacturer WHERE id_manufacturer = ' . $brandId);
                $rule['rule_data']['brand_name'] = $brandName;
            }

            if (isset($rule['rule_data']['supplier']) && $rule['rule_data']['supplier']) {
                $supplierId = (int) $rule['rule_data']['supplier'];
                $supplierName = Db::getInstance()->getValue('SELECT name FROM ' . _DB_PREFIX_ . 'supplier WHERE id_supplier = ' . $supplierId);
                $rule['rule_data']['supplier_name'] = $supplierName;
            }
            if (isset($rule['rule_data']['newest']) && $rule['rule_data']['newest']) {
                $rule['rule_data']['newest'] = (int) $rule['rule_data']['newest'];
            }
            if (isset($rule['rule_data']['discounted']) && $rule['rule_data']['discounted']) {
                $rule['rule_data']['discounted'] = (int) $rule['rule_data']['discounted'];
            }
            if (isset($rule['rule_data']['lowstock']) && $rule['rule_data']['lowstock']) {
                $rule['rule_data']['lowstock'] = (int) $rule['rule_data']['lowstock'];
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

        $rules = array_merge($nonBuryRules, $buryRules);

        return $rules;
    }

    public function ajaxProcessLoadAttributes()
    {
        $features = Tools::getValue('features');
        $values = [];

        if (!empty($features)) {
            $featureIds = implode(',', array_map('intval', $features));
            $query = new DbQuery();
            $query->select('af.id_attribute, al.name');
            $query->from('attribute', 'af');
            $query->leftJoin('attribute_lang', 'al', 'af.id_attribute = al.id_attribute AND al.id_lang = ' . (int) $this->context->language->id);
            $query->where('af.id_attribute_group IN (' . $featureIds . ')');
            $query->orderBy('al.name ASC');

            $results = Db::getInstance()->executeS($query);
            foreach ($results as $result) {
                $values[] = [
                    'id' => $result['id_attribute'],
                    'name' => $result['name'],
                ];
            }
        }

        exit(json_encode($values));
    }

    public function ajaxProcessSaveRule()
    {
        $id_awvisualmerchandising = Tools::getValue('id_awvisualmerchandising');
        $ruleType = Tools::getValue('ruleType');
        $ruleSegment = Tools::getValue('ruleSegment');

        $attributeValues = Tools::getValue('attributeValues', []);

        $attributeRanges = Tools::getValue('range', []);
        foreach ($attributeRanges as $key => $range) {
            if (!isset($attributeValues[$key])) {
                unset($attributeRanges[$key]);
            }
        }

        $features = Tools::getValue('features', []);
        $allFeatureRanges = Tools::getValue('range_feature', []);

        foreach ($allFeatureRanges as $featureId => $range) {
            if (!isset($features[$featureId]) || empty($features[$featureId])) {
                unset($allFeatureRanges[$featureId]);
            }
        }

        $brand = Tools::getValue('brand');
        $supplier = Tools::getValue('supplier');
        $newestRule = Tools::getValue('newestRule') ? 1 : 0;
        $discountedRule = Tools::getValue('discountedRule') ? 1 : 0;
        $lowstockRule = Tools::getValue('lowstock') ? 1 : 0;
        $brandRange = (int) Tools::getValue('brand_range');
        $supplierRange = (int) Tools::getValue('supplier_range');
        $newestRange = (int) Tools::getValue('newest_range');
        $discountedRange = (int) Tools::getValue('discounted_range');
        $lowstockRange = (int) Tools::getValue('lowstock_range');
        $brandRange = (int) Tools::getValue('brand_range');
        $supplierRange = (int) Tools::getValue('supplier_range');
        $newestRange = (int) Tools::getValue('newest_range');
        $discountedRange = (int) Tools::getValue('discounted_range');
        $lowstockRange = (int) Tools::getValue('lowstock_range');

        $brandRange = (int) Tools::getValue('brand_range');
        $supplierRange = (int) Tools::getValue('supplier_range');
        $newestRange = (int) Tools::getValue('newest_range');
        $discountedRange = (int) Tools::getValue('discounted_range');
        $lowstockRange = (int) Tools::getValue('lowstock_range');

        if (!$id_awvisualmerchandising || !$ruleType || !$ruleSegment) {
            exit(json_encode(['success' => false, 'message' => 'Missing required parameters.']));
        }

        $ruleData = ['segment' => $ruleSegment];

        switch ($ruleSegment) {
            case 'attribute':
                $ruleData['attributes'] = $attributeValues;
                $ruleData['attributes_ranges'] = $attributeRanges;
                break;
            case 'feature':
                $ruleData['features'] = $features;
                $ruleData['features_ranges'] = $allFeatureRanges;
                break;
            case 'brand':
                $ruleData['brand'] = $brand;
                $ruleData['brand_range'] = $brandRange;
                break;
            case 'supplier':
                $ruleData['supplier'] = $supplier;
                $ruleData['supplier_range'] = $supplierRange;
                break;
            case 'newest':
                $ruleData['newest'] = $newestRule;
                $ruleData['newest_range'] = $newestRange;
                break;
            case 'discounted':
                $ruleData['discounted'] = $discountedRule;
                $ruleData['discounted_range'] = $discountedRange;
                break;
            case 'lowstock':
                $ruleData['lowstock'] = $lowstockRule;
                $ruleData['lowstock_range'] = $lowstockRange;
                break;
        }

        $ruleData = json_encode($ruleData);

        $db = Db::getInstance();
        $maxSort = (int) Db::getInstance()->getValue('SELECT MAX(sort) FROM ' . _DB_PREFIX_ . 'awvisualmerchandising_rules WHERE id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising);
        $newSort = ++$maxSort;
        $success = $db->insert('awvisualmerchandising_rules', [
            'id_awvisualmerchandising' => (int) $id_awvisualmerchandising,
            'rule_type' => pSQL($ruleType),
            'sort' => $newSort,
            'rule_data' => pSQL($ruleData),
        ]);

        if ($success) {
            $id_rule = $db->Insert_ID();
            exit(json_encode(['success' => true, 'message' => 'Rule saved successfully.', 'id_rule' => $id_rule]));
        } else {
            exit(json_encode(['success' => false, 'message' => 'Failed to save rule.']));
        }
    }

    public function ajaxProcessUpdateRule()
    {
        $ruleId = (int) Tools::getValue('ruleId');
        $id_awvisualmerchandising = Tools::getValue('id_awvisualmerchandising');
        $ruleType = Tools::getValue('ruleType');
        $ruleSegment = Tools::getValue('ruleSegment');
        $attributeValues = Tools::getValue('attributeValues', []);
        $features = Tools::getValue('features', []);
        $brand = Tools::getValue('brand');
        $supplier = Tools::getValue('supplier');
        $newestRule = Tools::getValue('newestRule') ? 1 : 0;
        $discountedRule = Tools::getValue('discountedRule') ? 1 : 0;
        $lowstockRule = Tools::getValue('lowstock') ? 1 : 0;

        $brandRange = (int) Tools::getValue('brand_range');
        $supplierRange = (int) Tools::getValue('supplier_range');
        $newestRange = (int) Tools::getValue('newest_range');
        $discountedRange = (int) Tools::getValue('discounted_range');
        $lowstockRange = (int) Tools::getValue('lowstock_range');

        if (!$ruleId || !$id_awvisualmerchandising || !$ruleType || !$ruleSegment) {
            exit(json_encode(['success' => false, 'message' => 'Missing required parameters.']));
        }

        $attributeRanges = Tools::getValue('range', []);
        foreach ($attributeRanges as $key => $range) {
            if (!isset($attributeValues[$key])) {
                unset($attributeRanges[$key]);
            }
        }

        $allFeatureRanges = Tools::getValue('range_feature', []);

        foreach ($allFeatureRanges as $featureId => $range) {
            if (!isset($features[$featureId]) || empty($features[$featureId])) {
                unset($allFeatureRanges[$featureId]);
            }
        }

        $ruleData = ['segment' => $ruleSegment];

        switch ($ruleSegment) {
            case 'attribute':
                $ruleData['attributes'] = $attributeValues;
                $ruleData['attributes_ranges'] = $attributeRanges;
                break;
            case 'feature':
                $ruleData['features'] = $features;
                $ruleData['features_ranges'] = $allFeatureRanges;
                break;
            case 'brand':
                $ruleData['brand'] = $brand;
                $ruleData['brand_range'] = $brandRange;
                break;
            case 'supplier':
                $ruleData['supplier'] = $supplier;
                $ruleData['supplier_range'] = $supplierRange;
                break;
            case 'newest':
                $ruleData['newest'] = $newestRule;
                $ruleData['newest_range'] = $newestRange;
                break;
            case 'discounted':
                $ruleData['discounted'] = $discountedRule;
                $ruleData['discounted_range'] = $discountedRange;
                break;
            case 'lowstock':
                $ruleData['lowstock'] = $lowstockRule;
                $ruleData['lowstock_range'] = $lowstockRange;
                break;
        }

        $ruleData = json_encode($ruleData);

        $db = Db::getInstance();
        $success = $db->update('awvisualmerchandising_rules', [
            'rule_type' => pSQL($ruleType),
            'rule_data' => pSQL($ruleData),
        ], 'id = ' . (int) $ruleId);

        if ($success) {
            exit(json_encode(['success' => true, 'message' => 'Rule updated successfully.']));
        } else {
            exit(json_encode(['success' => false, 'message' => 'Failed to update rule.']));
        }
    }

    public function ajaxProcessLoadRules()
    {
        $id_awvisualmerchandising = Tools::getValue('id_awvisualmerchandising');
        if (!$id_awvisualmerchandising) {
            $id_awvisualmerchandising = 7;
        }
        $rules = $this->getRules($id_awvisualmerchandising);

        $this->context->smarty->assign([
            'rules' => $rules,
            'pathUri' => $this->module->getPathUri(),
        ]);

        echo $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/rules.tpl');
        exit;
    }

    public function ajaxProcessUpdateRows()
    {
        $id_awvisualmerchandising = (int) Tools::getValue('id_awvisualmerchandising');
        $desktop_rows = (int) Tools::getValue('desktop_rows');
        $mobile_rows = (int) Tools::getValue('mobile_rows');

        if ($id_awvisualmerchandising) {
            $result = Db::getInstance()->update(
                'awvisualmerchandising',
                [
                    'desktop_rows' => $desktop_rows,
                    'mobile_rows' => $mobile_rows,
                    'date_upd' => date('Y-m-d H:i:s'),
                ],
                'id_awvisualmerchandising = ' . (int) $id_awvisualmerchandising
            );

            if ($result) {
                exit(json_encode(['success' => true]));
            }
        }

        exit(json_encode(['success' => false]));
    }

    public function ajaxProcessDeleteRule()
    {
        $id_rule = (int) Tools::getValue('ruleId');
        if (!$id_rule) {
            exit(json_encode(['success' => false, 'message' => 'Invalid rule ID.']));
        }

        $deleted = Db::getInstance()->delete('awvisualmerchandising_rules', 'id = ' . $id_rule);
        if ($deleted) {
            exit(json_encode(['success' => true, 'message' => 'Rule deleted successfully.']));
        } else {
            exit(json_encode(['success' => false, 'message' => 'Failed to delete rule.']));
        }
    }

    public function ajaxProcessLoadEditModal()
    {
        $ruleId = (int) Tools::getValue('ruleId');
        if (!$ruleId) {
            exit(json_encode(['success' => false, 'message' => 'Invalid rule ID']));
        }

        $rule = Db::getInstance()->getRow(
            '
            SELECT * FROM `' . _DB_PREFIX_ . 'awvisualmerchandising_rules`
            WHERE `id` = ' . (int) $ruleId
        );
        if (!$rule) {
            exit(json_encode(['success' => false, 'message' => 'Rule not found']));
        }

        $rule['rule_data'] = json_decode($rule['rule_data'], true);

        $attributes = isset($rule['rule_data']['attributes']) ? $rule['rule_data']['attributes'] : [];
        $features = isset($rule['rule_data']['features']) ? $rule['rule_data']['features'] : [];
        $brand = isset($rule['rule_data']['brand']) ? $rule['rule_data']['brand'] : false;
        $supplier = isset($rule['rule_data']['supplier']) ? $rule['rule_data']['supplier'] : false;
        $newest = isset($rule['rule_data']['newest']) ? $rule['rule_data']['newest'] : 0;
        $discounted = isset($rule['rule_data']['discounted']) ? $rule['rule_data']['discounted'] : 0;
        $brands = Db::getInstance()->executeS('SELECT id_manufacturer as id, name FROM ' . _DB_PREFIX_ . 'manufacturer');
        $suppliers = Db::getInstance()->executeS('SELECT id_supplier as id, name FROM ' . _DB_PREFIX_ . 'supplier');

        $this->context->smarty->assign([
            'rule' => $rule,
            'attributes' => $this->getAttributes(),
            'features' => $this->getFeatures(),
            'brands' => $brands,
            'suppliers' => $suppliers,
            'selected_attributes' => $attributes,
            'selected_features' => $features,
            'selected_brand' => $brand,
            'selected_supplier' => $supplier,
            'newest' => $newest,
            'discounted' => $discounted,
            'lowstock' => $rule['rule_data']['lowstock'] ?? 0,
            'segment' => $rule['rule_data']['segment'] ?? '',
            'pathUri' => $this->module->getPathUri(),
        ]);

        echo $this->context->smarty->fetch($this->module->getLocalPath() . 'views/templates/admin/edit_rule.tpl');
        exit;
    }

    public function ajaxProcessUpdatePublishStatus()
    {
        $status = (int) Tools::getValue('status');
        $id_awvisualmerchandising = (int) Tools::getValue('id_awvisualmerchandising');
        if ($id_awvisualmerchandising) {
            $updated = Db::getInstance()->update(
                'awvisualmerchandising',
                [
                    'active' => $status,
                    'date_upd' => date('Y-m-d H:i:s'),
                ],
                'id_awvisualmerchandising = ' . $id_awvisualmerchandising
            );

            if ($updated) {
                exit(json_encode(['success' => true, 'message' => 'Status updated successfully.']));
            } else {
                exit(json_encode(['success' => false, 'message' => 'Failed to update status.']));
            }
        }

        exit(json_encode(['success' => false, 'message' => 'Invalid input']));
    }
}
