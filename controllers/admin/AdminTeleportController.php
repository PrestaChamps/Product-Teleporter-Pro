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

/**
 * Class AdminTeleportController
 */
class AdminTeleportController extends ModuleAdminController
{
    public function postProcess()
    {
        try {
            $productId = Tools::getValue('productId');
            $shopId = Tools::getValue('shopId');
            $product = new Product($productId);

            if (Validate::isLoadedObject($product)) {
                if (!$product->isAssociatedToShop($shopId)) {
                    $result = $product->associateTo($shopId);
                } else {
                    $result = $this->removeAssociation($shopId, $productId);
                }
                @Cache::getInstance()->flush();
                $this->ajaxDie([
                    'success' => $result,
                    'message' => "Product teleported to {$shopId}",
                    'associated' => (new Product($productId))->isAssociatedToShop($shopId), // Cache and stuff
                ]);
            } else {
                throw new Exception("Unknown product");
            }
        } catch (\Exception $exception) {
            $this->ajaxDie(['success' => false, 'message' => $exception->getMessage()]);
        }
    }

    public function ajaxDie($value = null, $controller = null, $method = null)
    {
        header('Content-Type: application/json');
        $value = json_encode($value);
        parent::ajaxDie($value, $controller, $method);
    }

    public function removeAssociation($idShop, $idObject)
    {
        return Db::getInstance()->execute("DELETE FROM `ps_product_shop` WHERE `ps_product_shop`.`id_product` = {$idObject} AND `ps_product_shop`.`id_shop` = {$idShop}");
    }
}