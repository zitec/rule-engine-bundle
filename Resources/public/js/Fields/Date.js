/**
 * Created by george.calcea on 12/27/2016.
 */

var DateTimeType = BaseField.extend({
    defaultDatetimepickerOptions: {
        pickDate: true,
        pickTime: false
    },
    init: function(options) {
        this._super(options);
        this.options.datetimepicker = this.options.datetimepicker || {};
        this.datetimepickerOptions = $.extend(true, {}, this.defaultDatetimepickerOptions, this.options.datetimepicker);
    },
    getHtml: function () {
        var input = $('<input>').addClass('rule-date form-control').attr('id', this.id).val(this.value);
        input.datetimepicker(this.datetimepickerOptions);

        input.change(function (e) {
            this.value = $(e.target).val();
            this.validCheck();
            this.triggerChange();
        }.bind(this));

        return input;
    },
    validate: function () {
        return Date.parse(this.value) > 0;
    }
});
