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
    {$is17|var_dump}
    <div class="panel-body">
        {foreach $shops as $shop}
            {*<a class="btn btn-primary btn-teleport {if $product->isAssociatedToShop($shop.id_shop)}associated{/if}"*}
               {*data-shop="{$shop.id_shop}" data-product="{$product->id}">*}
                {*<i class="icon icon-check"></i>*}
                {*{$shop.name}*}
            {*</a>*}
            <div class="btn-group" role="group" aria-label="{$shop.name}">
                <a type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" data-method="remove"
                        class="btn btn-warning btn-teleport">
                    {if !$is17}<i class="icon-plus"></i>{else}<i class="material-icons">remove_circle</i>{/if}
                </a>
                <a type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" class="btn btn-default"
                        disabled>
                    {$shop.name}
                </a>
                <a type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" data-method="add"
                        class="btn btn-success btn-teleport">
                    {if !$is17}<i class="icon-minus"></i>{else}<i class="material-icons">add_circle</i>{/if}
                </a>
            </div>
        {/foreach}
    </div>
</div>
