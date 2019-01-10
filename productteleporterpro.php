<?php

/**
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    PrestaChamps <leo@prestachamps.com>
 * @copyright PrestaChamps
 * @license   commercial
 */
class Productteleporterpro extends Module
{
    public $tabs = [
        [
            'name' => 'Teleport',
            'class_name' => 'AdminTeleport',
            'visible' => false,
        ],
    ];


    public function __construct()
    {
        $this->name = 'productteleporterpro';
        $this->tab = 'administration';
        $this->version = '0.0.1';
        $this->author = 'PrestaChamps';
        $this->need_instance = 1;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product Teleporter Pro');
        $this->description = $this->l('Do what I do. Hold tight and pretend it’s a plan!');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall this awesome module?');
        $this->ps_versions_compliancy = ['min' => '1.6', 'max' => _PS_VERSION_];
        require_once $this->getLocalPath() . "vendor/autoload.php";
    }


    /**
     * Install the required tabs, configs and stuff
     *
     * @since 0.0.1
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @return bool
     */
    public function install()
    {

        parent::install();
        if (!self::isPs17()) {
            /**
             * Create tabs for retro-compatibility
             */
            $langs = \Language::getLanguages();
            foreach ($this->tabs as $tabItem) {

                $tabNameMultiLang = [];
                foreach ($langs as $language) {
                    $tabNameMultiLang[(int)$language['id_lang']] = $tabItem['name'];
                }

                $tab = new Tab();
                $tab->name = $tabNameMultiLang;
                $tab->class_name = $tabItem['class_name'];
                $tab->id_parent = 0;
                $tab->module = $this->name;

                $tab->position = 0;
                $tab->active = $tabItem['visible'] ? 1 : 0;
                $tab->save();
            }
        }
        $this->registerHook('actionAdminControllerSetMedia');
        $this->registerHook('actionProductUpdate');
        $this->registerHook('displayAdminProductsExtra');
        $this->registerHook('displayBackOfficeHeader');
        return $this->registerHook('displayBackOfficeFooter');
    }


    /**
     * Check if the current PrestaShop installation is version 1.7 or below
     *
     * @return bool
     */
    public static function isPs17()
    {
        return (bool)version_compare(_PS_VERSION_, '1.7', '>=');
    }

    /**
     *
     */
    public function hookDisplayBackOfficeHeader()
    {
        if ($this->context->controller->controller_name === 'AdminProducts') {

            Media::addJsDef([
                'teleportUrl' => $this->context->link->getAdminLink('AdminTeleport'),
            ]);
            $this->context->controller->addJquery();
            $this->context->controller->addJS('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js');
            $this->context->controller->addCSS('https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css');
            $this->context->controller->addJS($this->getLocalPath() . '/views/templates/js/teleporter.js');
        }
    }

    /**
     * @param $params
     *
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
   public function hookDisplayAdminProductsExtra($params)
    {
        $productId = isset($params['id_product']) ? $params['id_product'] : Tools::getValue('id_product');
        if (Validate::isLoadedObject($product = new Product($productId))) {
            $shops = Shop::getShops(false);
            $associatedShops =  $product->getAssociatedShops();

            $this->smarty->assign(array(
                'shops' => $shops,
                'product' => $product,
                'is17' => self::isPs17(),
                'associatedShops' => $associatedShops,
            ));

            return $this->display(__FILE__, 'product.tpl');
        }
    }
}
