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
         <div class="alert alert-warning">
            {l s='After a product transfer, the product is only visible in backoffice. If you want to show the product also in front office, you need to associate the product to a category in the shop' mod='productteleporterpro'}
        </div>
    </div>
    <div class="panel-body">
        {foreach $shops as $shop}
            {*<a class="btn btn-primary btn-teleport {if $product->isAssociatedToShop($shop.id_shop)}associated{/if}"*}
               {*data-shop="{$shop.id_shop}" data-product="{$product->id}">*}
                {*<i class="icon icon-check"></i>*}
                {*{$shop.name}*}
            {*</a>*}

            {assign var="EnabledForThisShop" value="0"}
            {foreach $enabledforshops as $thisshop}
                {if $thisshop ==  $shop.id_shop}
                    {$EnabledForThisShop = 1}
                {/if}
            {/foreach}

            <div class="btn-group" role="group" aria-label="{$shop.name}">
               
                <a type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" class="btn btn-default"
                        disabled>
                    {$shop.name}
                </a>
               {if $EnabledForThisShop == 1} 
                    <div class="btn-group teleporter-btn-group" id="status" data-toggle="buttons">
                        <label class="btn btn-default btn-on-1 btn-sm active btn-teleport" type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" data-method="add">
                        <input type="radio" value="1" name="" checked="checked">ON</label>
                        <label class="btn btn-default btn-off-1 btn-sm btn-teleport" type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" data-method="remove">
                        <input type="radio" value="0" name="">OFF</label>
                    </div>
                {else}
                     <div class="btn-group teleporter-btn-group" id="status" data-toggle="buttons">
                        <label class="btn btn-default btn-on-1 btn-sm btn-teleport" type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" data-method="add">
                        <input type="radio" value="1" name="">ON</label>
                        <label class="btn btn-default btn-off-1 btn-sm active btn-teleport" type="button" data-shop="{$shop.id_shop}" data-product="{$product->id}" data-method="remove">
                        <input type="radio" value="0" name="" checked="checked">OFF</label>
                    </div>
                {/if}

            </div> </br> </br></br>
        {/foreach}
    </div>
</div>
