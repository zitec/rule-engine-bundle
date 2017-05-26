/**
 * Created by george.calcea on 12/27/2016.
 */

/**
 * Class that represented the input type text for condition builder
 * @param options
 * @param ruleData
 * @constructor
 */
var BaseField = Class.extend({
    id: null,
    value: null,
    options: {},
    html: null,
    container: [],

    init: function (options) {
        this.id = 'rule-' + guid();
        this.options = options || {};
    },

    setValue: function (value) {
        this.value = value;
    },

    getHtml: function () {
        throw "Your element needs to implement the getHtml method!";
    },

    appendHtml: function (container) {
        this.container[this.id] = container;

        if (this.html === null) {
            this.html = this.getHtml();
        }

        container.append(this.html);
    },

    remove: function () {
        if (this.html === null) {
            return;
        }
        this.html.remove();
    },

    getValue: function () {
        if (!this.validCheck()) {
            throw 'Invalid value';
        } else {
            return this.value;
        }
    },

    validCheck: function() {
        var valid = this.validate();
        console.log(this.container);
        if (!valid) {
            this.container[this.id].addClass('rule-condition-error');
            $(document).trigger('rule-error');
        } else {
            this.container[this.id].removeClass('rule-condition-error');
            return this.value;
        }
        return valid;
    },

    validate: function () {
        throw 'Validate function not implemented for field of type: ' + typeof this;
    },

    setChangeCallback: function(callback) {
        this.changeCallback = callback;
    },

    triggerChange: function() {
        if (typeof this.changeCallback === 'function') {
            this.changeCallback(this.id);
        }
    }
});
