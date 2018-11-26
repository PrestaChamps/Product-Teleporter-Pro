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
use \Context;
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

    /**
     * @var Context $context
     */
    protected $context;

    public function __construct(Product $product, Shop $shop, Context $context)
    {
        $this->product = $product;
        $this->shop = $shop;
        $this->context = $context;
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
            Db::getInstance()->insert(
                'image_shop',
                [
                    'id_product' => $image->id_product,
                    'id_image' => $image->id,
                    'id_shop' => $this->shop->id,
                    'cover' => (int)$image->cover,
                ],
                false,
                false,
                Db::INSERT_IGNORE
            );
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
        $table = 'product_attribute_shop';
        return Db::getInstance()->delete(
            $table,
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
     * @throws PrestaShopDatabaseException
     */
    private function associateObjectModel(\ObjectModel $object)
    {

        $reflection = new \ReflectionObject($object);
        $property = $reflection->getStaticPropertyValue('definition');

        if (!$object->id) {
            throw new \InvalidParameterException('Object does not exists in DB');
        }
        $data = [];
        $definition = $property;

        $fields = $this->getTableFields(_DB_PREFIX_ . $definition['table'] . '_shop');

        if (true || !$object->isAssociatedToShop($this->shop->id)) {

            $values = $object->getFieldsShop();
            foreach ($fields as $field) {
                if (property_exists($object, $field) && isset($values[$field])) {

                    $data[$field] = $values[$field];

                    if (!is_scalar($data[$field])) {
                        unset($data[$field]);
                    }
                }
            }
            $data[$definition['primary']] = (int)$object->id;
            $data['id_shop'] = $this->shop->id;
            if ($data) {
                $tableName = (strpos($definition['table'], '_shop') === false) ?
                    $definition['table'] . '_shop' : $definition['table'];
                if (!Db::getInstance()->insert($tableName, $data)) {
                    throw new \Exception("Can't save associate mode");
                }
            }
        }
    }

    private function getTableFields($tableName)
    {
        return array_column(Db::getInstance()->executeS("DESCRIBE $tableName"), 'Field');
    }
}
