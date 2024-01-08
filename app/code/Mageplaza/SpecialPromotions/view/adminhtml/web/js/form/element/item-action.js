/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (_, uiRegistry, select) {
    'use strict';

    return select.extend({
        /**
         * @inheritDoc
         */
        initialize: function () {
            this._super();

            this.updateNotice();

            return this;
        },

        /**
         * Hide fields on coupon tab
         */
        onUpdate: function () {
            this.updateNotice();
        },

        updateNotice: function () {
            var itemActionQty = uiRegistry.get('sales_rule_form.sales_rule_form.actions.item_action_qty');
            if (this.value() === 'cheapest') {
                itemActionQty.notice(itemActionQty.cheapest);
            } else if (this.value() === 'expensive') {
                itemActionQty.notice(itemActionQty.expensive);
            }
        },
    });
});
