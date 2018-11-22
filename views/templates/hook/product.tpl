{*
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
 *}
<div class="panel">
    <div class="panel-heading">
        {l s='Transfer products to other shops' mod='productteleporterpro'}
    </div>
    <div class="panel-body">
        {foreach $shops as $shop}
            <a class="btn btn-primary btn-teleport {if $product->isAssociatedToShop($shop.id_shop)}associated{/if}"
               data-shop="{$shop.id_shop}" data-product="{$product->id}">
                <i class="icon icon-check"></i>
                {$shop.name}
            </a>
        {/foreach}
    </div>
</div>

<style>
    .btn-teleport i {
        display: none;
    }

    .btn-teleport.associated i {
        display: inline-block;
    }
</style>