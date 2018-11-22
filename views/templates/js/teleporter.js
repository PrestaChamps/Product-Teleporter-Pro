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

$(document).ready(function () {
    $(document).on('click', '.btn-teleport', function (event) {
        var productId = $(this).data('product');
        var shopId = $(this).data('shop');
        var element = $(this);
        $.ajax({
            url: teleportUrl,
            data: {
                shopId: shopId,
                productId: productId
            },
            success: function (result) {
                if (result.associated) {
                    $(element).addClass('associated');
                } else {
                    $(element).removeClass('associated');
                }
                toastr.success(result.message)
            }
        });
    })
});