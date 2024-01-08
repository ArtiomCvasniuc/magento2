/* ===========================================================
 * bootstrap-tooltip.js v2.1.1
 * http://twitter.github.com/bootstrap/javascript.html#tooltips
 * Inspired by the original jQuery.tipsy by Jason Frame
 * ===========================================================
 * Copyright 2012 Twitter, Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */


!function (jQuery) {

  "use strict"; // jshint ;_;


 /* TOOLTIP PUBLIC CLASS DEFINITION
  * =============================== */

  var Tooltip = function (element, options) {
    this.init('tooltip', element, options)
  }

  Tooltip.prototype = {

    constructor: Tooltip

  , init: function (type, element, options) {
      var eventIn
        , eventOut

      this.type = type
      this.jQueryelement = jQuery(element)
      this.options = this.getOptions(options)
      this.enabled = true

      if (this.options.trigger == 'click') {
        this.jQueryelement.on('click.' + this.type, this.options.selector, jQuery.proxy(this.toggle, this))
      } else if (this.options.trigger != 'manual') {
        eventIn = this.options.trigger == 'hover' ? 'mouseenter' : 'focus'
        eventOut = this.options.trigger == 'hover' ? 'mouseleave' : 'blur'
        this.jQueryelement.on(eventIn + '.' + this.type, this.options.selector, jQuery.proxy(this.enter, this))
        this.jQueryelement.on(eventOut + '.' + this.type, this.options.selector, jQuery.proxy(this.leave, this))
      }

      this.options.selector ?
        (this._options = jQuery.extend({}, this.options, { trigger: 'manual', selector: '' })) :
        this.fixTitle()
    }

  , getOptions: function (options) {
      options = jQuery.extend({}, jQuery.fn[this.type].defaults, options, this.jQueryelement.data())

      if (options.delay && typeof options.delay == 'number') {
        options.delay = {
          show: options.delay
        , hide: options.delay
        }
      }

      return options
    }

  , enter: function (e) {
      var self = jQuery(e.currentTarget)[this.type](this._options).data(this.type)

      if (!self.options.delay || !self.options.delay.show) return self.show()

      clearTimeout(this.timeout)
      self.hoverState = 'in'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'in') self.show()
      }, self.options.delay.show)
    }

  , leave: function (e) {
      var self = jQuery(e.currentTarget)[this.type](this._options).data(this.type)

      if (this.timeout) clearTimeout(this.timeout)
      if (!self.options.delay || !self.options.delay.hide) return self.hide()

      self.hoverState = 'out'
      this.timeout = setTimeout(function() {
        if (self.hoverState == 'out') self.hide()
      }, self.options.delay.hide)
    }

  , show: function () {
      var jQuerytip
        , inside
        , pos
        , actualWidth
        , actualHeight
        , placement
        , tp

      if (this.hasContent() && this.enabled) {
        jQuerytip = this.tip()
        this.setContent()

        if (this.options.animation) {
          jQuerytip.addClass('fade')
        }

        placement = typeof this.options.placement == 'function' ?
          this.options.placement.call(this, jQuerytip[0], this.jQueryelement[0]) :
          this.options.placement

        inside = /in/.test(placement)

        jQuerytip
          .remove()
          .css({ top: 0, left: 0, display: 'block' })
          .appendTo(inside ? this.jQueryelement : document.body)

        pos = this.getPosition(inside)

        actualWidth = jQuerytip[0].offsetWidth
        actualHeight = jQuerytip[0].offsetHeight

        switch (inside ? placement.split(' ')[1] : placement) {
          case 'bottom':
            tp = {top: pos.top + pos.height, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'top':
            tp = {top: pos.top - actualHeight, left: pos.left + pos.width / 2 - actualWidth / 2}
            break
          case 'left':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left - actualWidth}
            break
          case 'right':
            tp = {top: pos.top + pos.height / 2 - actualHeight / 2, left: pos.left + pos.width}
            break
        }

        jQuerytip
          .css(tp)
          .addClass(placement)
          .addClass('in')
      }
    }

  , setContent: function () {
      var jQuerytip = this.tip()
        , title = this.getTitle()

      jQuerytip.find('.tooltip-inner')[this.options.html ? 'html' : 'text'](title)
      jQuerytip.removeClass('fade in top bottom left right')
    }

  , hide: function () {
      var that = this
        , jQuerytip = this.tip()

      jQuerytip.removeClass('in')

      function removeWithAnimation() {
        var timeout = setTimeout(function () {
          jQuerytip.off(jQuery.support.transition.end).remove()
        }, 500)

        jQuerytip.one(jQuery.support.transition.end, function () {
          clearTimeout(timeout)
          jQuerytip.remove()
        })
      }

      jQuery.support.transition && this.jQuerytip.hasClass('fade') ?
        removeWithAnimation() :
        jQuerytip.remove()

      return this
    }

  , fixTitle: function () {
      var jQuerye = this.jQueryelement
      if (jQuerye.attr('title') || typeof(jQuerye.attr('data-original-title')) != 'string') {
        jQuerye.attr('data-original-title', jQuerye.attr('title') || '').removeAttr('title')
      }
    }

  , hasContent: function () {
      return this.getTitle()
    }

  , getPosition: function (inside) {
      return jQuery.extend({}, (inside ? {top: 0, left: 0} : this.jQueryelement.offset()), {
        width: this.jQueryelement[0].offsetWidth
      , height: this.jQueryelement[0].offsetHeight
      })
    }

  , getTitle: function () {
      var title
        , jQuerye = this.jQueryelement
        , o = this.options

      title = jQuerye.attr('data-original-title')
        || (typeof o.title == 'function' ? o.title.call(jQuerye[0]) :  o.title)

      return title
    }

  , tip: function () {
      return this.jQuerytip = this.jQuerytip || jQuery(this.options.template)
    }

  , validate: function () {
      if (!this.jQueryelement[0].parentNode) {
        this.hide()
        this.jQueryelement = null
        this.options = null
      }
    }

  , enable: function () {
      this.enabled = true
    }

  , disable: function () {
      this.enabled = false
    }

  , toggleEnabled: function () {
      this.enabled = !this.enabled
    }

  , toggle: function () {
      this[this.tip().hasClass('in') ? 'hide' : 'show']()
    }

  , destroy: function () {
      this.hide().jQueryelement.off('.' + this.type).removeData(this.type)
    }

  }


 /* TOOLTIP PLUGIN DEFINITION
  * ========================= */

  jQuery.fn.tooltip = function ( option ) {
    return this.each(function () {
      var jQuerythis = jQuery(this)
        , data = jQuerythis.data('tooltip')
        , options = typeof option == 'object' && option
      if (!data) jQuerythis.data('tooltip', (data = new Tooltip(this, options)))
      if (typeof option == 'string') data[option]()
    })
  }

  jQuery.fn.tooltip.Constructor = Tooltip

  jQuery.fn.tooltip.defaults = {
    animation: true
  , placement: 'top'
  , selector: false
  , template: '<div class="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
  , trigger: 'hover'
  , title: ''
  , delay: 0
  , html: true
  }

}(window.jQuery);

jQuery(document).ready(function () {
 jQuery('.button.btn-cart').attr('data-original-title',jQuery('button.btn-cart').attr('title'));
 jQuery('a.shopping-cart').attr('data-original-title',jQuery('a.shopping-cart').attr('title'));
 jQuery('a.view-detail').attr('data-original-title',jQuery('a.view-detail').attr('title'));
 jQuery('.email-friend-a').attr('data-original-title',jQuery('.email-friend-a').attr('title'));
 jQuery('.form-search button.button').attr('data-original-title',jQuery('button.btn-cart').attr('title'));
 jQuery('.link-wishlist').attr('data-original-title',jQuery('.link-wishlist').attr('title'));
 jQuery('.link-compare').attr('data-original-title',jQuery('.link-compare').attr('title'));
 jQuery('.btn-navbar').attr('data-original-title',jQuery('.btn-navbar').attr('title'));
 jQuery('.brand_item').attr('data-original-title',jQuery('.brand_item').attr('title'));
 jQuery('button.btn-cart,.form-search button.button,a.shopping-cart, .btn-navbar, .link-wishlist, .link-compare,a.view-detail,.email-friend-a,.brand_item').attr('rel', 'tooltip');
 jQuery("[rel=tooltip]").tooltip(); 
  });