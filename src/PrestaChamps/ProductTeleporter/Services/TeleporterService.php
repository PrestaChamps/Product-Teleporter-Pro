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

namespace PrestaChamps\ProductTeleporter\Services;

use \Db;
use \Shop;
use \Image;
use \Product;
use PrestaShopDatabaseException;

/**
 * Class ProductTeleporterService
 *
 * @package PrestaChamps\Teleporter\Services
 */
class TeleporterService
{
    /**
     * @var Product $product
     */
    protected $product;

    /**
     * @var Shop $shop
     */
    protected $shop;

    public function __construct(Product $product, Shop $shop)
    {
        $this->product = $product;
        $this->shop = $shop;
    }

    /**
     * Associate a product and it's components to a shop
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function associate()
    {
        $this->associateObjectModel($this->product);
        $this->associateImages();
        $this->associateCombinations();
        return true;
    }

    /**
     * Dissociate a product and it's components from a shop
     *
     * @return bool
     */
    public function dissociate()
    {
        $this->dissociateImages();
        $this->dissociateCombinations();
        return Db::getInstance()->execute(
            'DELETE FROM `ps_product_shop` WHERE ' .
            "`ps_product_shop`.`id_product` = {$this->product->id} " .
            "AND `ps_product_shop`.`id_shop` = {$this->shop->id}"
        );
    }

    /**
     * Get product image objects
     *
     * @return Image[]
     * @throws PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getImages()
    {
        $query = new \DbQuery();
        $query->from('image');
        $query->select('*');
        $query->where("id_product = {$this->product->id}");

        return Image::hydrateCollection(\Image::class, Db::getInstance()->executeS($query));
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function associateImages()
    {
        foreach ($this->getImages() as $image) {
            $this->associateObjectModel($image);
        }
    }

    protected function dissociateImages()
    {
        $table = _DB_PREFIX_ . 'image_shop';
        return Db::getInstance()->execute(
            "DELETE FROM `{$table}` WHERE " .
            "`id_product` = {$this->product->id} AND" .
            "`id_shop` = {$this->shop->id}"
        );
    }

    protected function dissociateCombinations()
    {
        $table = _DB_PREFIX_ . 'product_attribute_shop';
        return Db::getInstance()->execute(
            "DELETE FROM `{$table}` WHERE " .
            "`id_product` = {$this->product->id} AND" .
            "`id_shop` = {$this->shop->id}"
        );
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function associateCombinations()
    {
        foreach ($this->getCombinations() as $combination) {
            $this->associateObjectModel($combination);
        }
    }

    /**
     * Get product combination objects
     *
     * @return \Combination[]
     * @throws PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    protected function getCombinations()
    {
        $query = new \DbQuery();
        $query->from('product_attribute');
        $query->select('*');
        $query->where("id_product = {$this->product->id}");

        return \Combination::hydrateCollection(\Combination::class, Db::getInstance()->executeS($query));
    }

    /**
     * @param \ObjectModel $object
     *
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    private function associateObjectModel(\ObjectModel $object)
    {
        $reflection = new \ReflectionObject($object);
        $property = $reflection->getStaticPropertyValue('definition');

        if (!$object->id) {
            return false;
        }
        $data = [];
        $definition = $property;
        if (!$object->isAssociatedToShop($this->shop->id)) {
            foreach ($definition['fields'] as $field => $fieldOptions) {
                if (isset($fieldOptions['shop']) && $fieldOptions['shop'] === true) {
                    $data[$field] = $object->$field;
                }
            }
            $data[$definition['primary']] = (int)$object->id;
            $data['id_shop'] = $this->shop->id;
            if ($data) {
                return Db::getInstance()->insert($definition['table'] . '_shop', $data);
            }
        }

        return true;
    }
}
