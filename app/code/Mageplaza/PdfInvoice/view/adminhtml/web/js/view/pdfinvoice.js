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
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
define([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'mage/translate',
    'Mageplaza_PdfInvoice/js/lib/codemirror',
    'Mageplaza_PdfInvoice/js/mode/xml/xml',
    'Mageplaza_PdfInvoice/js/view/variables'
], function ($, modal, $t, CodeMirror) {
    "use strict";

    $.widget('mageplaza.pdfinvoice', {
        options: {
            loadTemplateUrl: '',
            previewTemplateUrl: '',
            templateType: ''
        },
        codeMirror: '',
        variables: null,

        /**
         * This method constructs a new widget.
         * @private
         */
        _create: function () {
            this.codeMirror = CodeMirror.fromTextArea(document.getElementById("template_html"), {
                lineNumbers: true,
                autofocus: true
            });
            this.initObserve();
        },

        /**
         * Init observe
         */
        initObserve: function () {
            $("#mp-form-key").val(window.FORM_KEY);
            this.codeMirror.on('change', function (cMirror) {
                $("#template_html").val(cMirror.getValue());
            });
            this.loadTemplate();
            this.insertVariable();
            this.changeImageUrl();
            this.checkEditHtmlOrPreview();
        },

        /**
         * insert variable
         */
        insertVariable: function () {
            var self = this;
            $("#insert_variable").click(function () {
                if (self.variables == null) {
                    self.variables = JSON.parse($('#variables').val());
                }
                if (self.variables) {
                    Variables.setEditor(self.codeMirror);
                    Variables.openVariableChooser(self.variables);
                }
            });
        },

        /**
         * Check edit html or preview
         */
        checkEditHtmlOrPreview: function () {
            var element = ".field-template_html button";
            $(element).click(function () {
                $(element).removeClass("active");
                $(this).addClass("active");
                if (this.id == 'template_html_bt') {
                    $("#iframe").hide();
                    $(".CodeMirror.cm-s-default").show();
                } else {
                    $("#pdf-html").val($("#template_html").val());
                    $("#pdf-css").val($("#template_styles").val());
                    $("#mp-submit").trigger('click');
                    $(".CodeMirror.cm-s-default").hide();
                    $("#iframe").contents().find('html').html('<p>' + $t('Loading....') + '</p>');
                    $("#iframe").show();
                }
            });
        },

        /**
         * Change image url
         */
        changeImageUrl: function () {
            $("#default_template").change(function () {
                var imageUrls = JSON.parse($("#images-urls").val());
                $("#mp-image").attr('src', imageUrls[$("#default_template").val()]);
            })
        },

        /**
         * Load template
         */
        loadTemplate: function () {
            var self = this;

            $("#load_template").click(function () {
                $(".field-template_html button").removeClass('active');
                $("#template_html_bt").addClass('active').show();
                $(".CodeMirror.cm-s-default").show();
                $("#iframe").hide();

                var params = {
                    templateId: $("#default_template").val(),
                    templateType: self.options.templateType
                };
                self.sendAjax(self, params, self.options.loadTemplateUrl);
            });
        },

        /**
         * Send Ajax
         * @param params
         * @param url
         */
        sendAjax: function (self, params, url) {
            $.ajax({
                method: 'POST',
                url: url,
                data: params,
                showLoader: true
            }).done(function (response) {
                if (response.status) {
                    self.codeMirror.setValue(response.templateHtml);
                    $("#template_styles").val(response.templateCss);
                }
            }).always(function () {
            });
        }
    });

    return $.mageplaza.pdfinvoice;
});
