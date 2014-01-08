/*
 * This file is part of the Sonatra package.
 *
 * (c) François Pluchino <francois.pluchino@sonatra.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

+function ($) { 'use strict';

    // SIDEBAR CLASS DEFINITION
    // ========================

    var Sidebar = function (element, options) {
        this.guid       = jQuery.guid;
        this.options    = $.extend({}, Sidebar.DEFAULTS, options);
        this.$element   = $(element);
        this.$wrapper   = $('.' + this.options.classWrapper, this.$element);
        this.$menu      = $('.' + this.options.classMenu, this.$wrapper);
        this.isOpen     = false;
        this.eventType  = this.mobileCheck() ? 'touchstart' : 'click';
        
        if(!this.mobileCheck() && this.options.openOnHover) {
            this.$element.on('mouseover.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.openMini, this));
            this.$element.on('mouseout.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.closeMini, this));

            this.$menu.on('mouseover.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.open, this));
        }

        this.$element.on(this.eventType + '.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.toggle, this));
        $(window).on( 'keyup.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.keyboardAction, this));

        if (this.$wrapper.hasClass('sidebar-open')) {
            this.isOpen = true;
            $(document).on(this.eventType + '.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.closeExternal, this));
        }
    };

    Sidebar.DEFAULTS = {
        classToggle:   'sidebar-toggle',    
        classWrapper:  'sidebar-wrapper',
        classMenu:     'sidebar-menu',
        classOpenMini: 'sidebar-open-mini',
        classOpen:     'sidebar-open',
        openOnHover:   true,
        keyboardEvent: {
            ctrlKey:       true,
            shiftKey:      false,
            altKey:        true,
            keyCode:       'S'.charCodeAt(0)
        }
    };

    Sidebar.prototype.mobileCheck = function (_relatedTarget) {
        var check = false;

        (function (a) {
            if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) {
            check = true;
            }

        })(navigator.userAgent || navigator.vendor || window.opera);

        return check;
    };

    Sidebar.prototype.keyboardAction = function(event) {
        if (!event instanceof jQuery.Event) {
            return;
        }

        var kbe = this.options.keyboardEvent;

        if (       event.shiftKey == kbe.shiftKey
                && event.ctrlKey  == kbe.ctrlKey
                && event.altKey   == kbe.altKey
                && event.keyCode  == kbe.keyCode) {
            this.toggle(event);
        }
    };

    Sidebar.prototype.openMini = function () {
        $('.' + this.options.classWrapper, this.$element).addClass(this.options.classOpenMini);
    };

    Sidebar.prototype.closeMini = function () {
        $('.' + this.options.classWrapper, this.$element).removeClass(this.options.classOpenMini);
    };

    Sidebar.prototype.toggle = function (event) {
        if (event) {
            var $parents = $(event.target).parents('.' + this.options.classWrapper);
            event.stopPropagation();

            if ($(event.target).hasClass(this.options.classToggle) || $(event.target).parents('.' + this.options.classToggle).size() > 0) {
                event.preventDefault();
            }

            if ($parents.size() > 0) {
                return;
            }
        }

        if (this.isOpen) {
            this.close();

        } else {
            this.open();
        }
    }

    Sidebar.prototype.closeExternal = function (event) {
        event.stopPropagation();
        event.preventDefault();

        this.close();
    };

    Sidebar.prototype.open = function () {
        if (this.isOpen) {
            return;
        }

        this.isOpen = true;
        $('.' + this.options.classWrapper, this.$element).addClass(this.options.classOpen);
        $(document).on(this.eventType + '.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.closeExternal, this));

        this.closeMini();
    };

    Sidebar.prototype.close = function () {
        if (!this.isOpen) {
            return;
        }

        this.isOpen = false;

        $('.' + this.options.classWrapper, this.$element).removeClass(this.options.classOpen);
        $(document).off(this.eventType + '.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.closeExternal, this));

        this.closeMini();
    };

    Sidebar.prototype.destroy = function () {
        if(!this.mobileCheck()) {
            this.$element.off('mouseover.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.openMini, this));
            this.$element.off('mouseout.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.closeMini, this));

            this.$menu.off('mouseover.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.open, this));
        }

        $(document).off(this.eventType + '.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.closeExternal, this));
        $(window).off( 'keyup.st.sidebar' + this.guid, $.proxy(Sidebar.prototype.keyboardAction, this));
    };


    // SIDEBAR PLUGIN DEFINITION
    // =========================

    var old = $.fn.sidebar;

    $.fn.sidebar = function (option, _relatedTarget) {
        return this.each(function () {
            var $this   = $(this);
            var data    = $this.data('st.sidebar');
            var options = typeof option == 'object' && option;

            if (!data && option == 'destroy') {
                return;
            }

            if (!data) {
                $this.data('st.sidebar', (data = new Sidebar(this, options)));
            }

            if (typeof option == 'string') {
                data[option]();
            }
        });
    };

    $.fn.sidebar.Constructor = Sidebar;


    // SIDEBAR NO CONFLICT
    // ===================

    $.fn.sidebar.noConflict = function () {
        $.fn.sidebar = old;

        return this;
    };


    // SIDEBAR DATA-API
    // ================

    $(window).on('load', function () {
        $('[data-sidebar="true"]').each(function () {
            var $this = $(this);
            $this.sidebar($this.data());
        });
    });

}(jQuery);