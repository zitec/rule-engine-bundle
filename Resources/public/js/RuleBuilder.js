"use strict";
var ruleBuilders = {};
(function ($) {
    $.fn.ruleBuilder = function (options) {
        var id = $(this).data("rule-builder-id");
        if (options === "data") {
            return (typeof ruleBuilders[id] === "undefined") ? {} : ruleBuilders[id].collectData();
        } else {
            return $(this).each(function () {
                // Validate options
                if (typeof options.fields === 'undefined' || options.fields.length === 0) {
                    throw new Error('Missing definitions');
                }
                var ruleData = (typeof options.data !== "undefined" && options.data !== '') ?
                    JSON.parse(options.data) : {};

                if (id) {
                    ruleBuilders[id].remove();
                    delete ruleBuilders[id];
                }

                var builder = new RuleBuilder(this, options.fields, ruleData);
                ruleBuilders[builder.getId()] = builder;
                $(this).data("rule-builder-id", builder.getId());
            });
        }
    };

    /**
     * @param element
     * @param {Object} fields
     * @param {Object} ruleData
     * @constructor
     */
    function RuleBuilder(element, fields, ruleData) {
        this.id = guid();
        this.element = $(element);
        this.mainGroup = new Group(new Definitions(fields), ruleData, false);
        this.mainGroup.setChangeCallback(function () {
            this.element.trigger('rule-change');
        }.bind(this));

        this.appendHTML();
    }

    RuleBuilder.prototype = {
        /**
         * Returns the builder's id
         * @returns {*}
         */
        getId: function () {
            return this.id;
        },
        appendHTML: function () {
            this.element.html(this.mainGroup.appendHtml(this.element));
        },
        /**
         * Serialize the current condition
         */
        collectData: function () {
            return this.mainGroup.serialize();
        },
        remove: function () {
            this.mainGroup.remove();
        }
    }

})(jQuery);
