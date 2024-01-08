/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_PdfInvoice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
    'jquery/ui',
    'prototype'
], function (jQuery, $t) {

    window.Variables = {
        textareaElementId: null,
        variablesContent: null,
        dialogWindow: null,
        dialogWindowId: 'variables-chooser',
        overlayShowEffectOptions: null,
        overlayHideEffectOptions: null,
        insertFunction: 'Variables.insertVariable',
        editor: null,

        /**
         * Reset data
         */
        resetData: function () {
            this.variablesContent = null;
            this.dialogWindow = null;
        },

        /**
         * Set editor
         * @param editor
         */
        setEditor: function (editor) {
            this.editor = editor;
        },

        /**
         * Open variable chooser
         * @param variables
         */
        openVariableChooser: function (variables) {
            if (this.variablesContent == null && variables) {
                this.variablesContent = '<ul class="insert-variable">';
                variables.each(function (variableGroup) {
                    if (variableGroup.label && variableGroup.value) {
                        this.variablesContent += '<li><b>' + variableGroup.label + '</b></li>';
                        (variableGroup.value).each(function (variable) {
                            if (variable.value && variable.label) {
                                this.variablesContent += '<li>' +
                                    this.prepareVariableRow(variable.value, variable.label) + '</li>';
                            }
                        }.bind(this));
                    }
                }.bind(this));
                this.variablesContent += '</ul>';
            }
            if (this.variablesContent) {
                this.openDialogWindow(this.variablesContent);
            }
        },

        /**
         * Open popup
         * @param variablesContent
         */
        openDialogWindow: function (variablesContent) {
            var windowId = this.dialogWindowId;
            jQuery('<div id="' + windowId + '">' + Variables.variablesContent + '</div>').modal({
                title: $t('Insert Variable...'),
                type: 'slide',
                buttons: [],
                closed: function (e, modal) {
                    modal.modal.remove();
                }
            });

            jQuery('#' + windowId).modal('openModal');

            variablesContent.evalScripts.bind(variablesContent).defer();
        },

        /**
         * Close popup
         */
        closeDialogWindow: function () {
            jQuery('#' + this.dialogWindowId).modal('closeModal');
        },

        /**
         * Prepare variable row
         * @param varValue
         * @param varLabel
         * @returns {string}
         */
        prepareVariableRow: function (varValue, varLabel) {
            var value = (varValue).replace(/"/g, '&quot;').replace(/'/g, '\\&#39;');
            var content = '<a href="#" onclick="' + this.insertFunction + '(\'' + value + '\');return false;">' + varLabel + '</a>';
            return content;
        },

        /**
         * Inser variable to editor at cursor
         * @param value
         */
        insertVariable: function (value) {
            var windowId = this.dialogWindowId;
            jQuery('#' + windowId).modal('closeModal');
            var doc = this.editor.getDoc();
            var cursor = doc.getCursor();
            doc.replaceRange(value, cursor);

        }
    };


    return window.Variables;
});