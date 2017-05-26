/**
 * Created by george.calcea on 12/27/2016.
 */

/**
 * Class that represented the input type text for condition builder
 * @param options
 * @param ruleData
 * @constructor
 */
var Interval = BaseField.extend({
    init: function (options) {
        this.value = {from: null, to: null};
        this._super(options)
    },
    setValue: function (value) {
        if (typeof value.from !== 'undefined' && typeof value.to !== 'undefined') {
            this.value = value;
        }
    },
    getHtml: function () {
        var intervalContainer = this.html = $('<div>').addClass('rule-interval').attr('id', this.id);
        var fromInput = $('<input>').addClass('rule-interval-from form-control').val(this.value.from);
        var toInput = $('<input>').addClass('rule-interval-to form-control').val(this.value.to);

        intervalContainer.append(fromInput).append(toInput);

        fromInput.change(function (e) {
            this.value.from = $(e.target).val();
            this.validCheck();
            this.triggerChange();
        }.bind(this));
        toInput.change(function (e) {
            this.value.to = $(e.target).val();
            this.validCheck();
            this.triggerChange();
        }.bind(this));

        return intervalContainer;
    },
    validate: function () {
        return this.value.from !== null && this.value.from !== ''
            && this.value.to !== null && this.value.to !== ''
            && this.value.from < this.value.to;
    }
});
