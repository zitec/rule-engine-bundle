/**
 * Created by george.calcea on 12/27/2016.
 */

/**
 * Class that represented the input type text for condition builder
 * @param options
 * @param ruleData
 * @constructor
 */
var DateTimeInterval = Interval.extend({
    datetimepickerOptions: {},
    init: function (options) {
        this._super(options);
        this.options.datetimepicker = this.options.datetimepicker || {};
        this.datetimepickerOptions = $.extend(true, {}, this.getDefaultDatetimepickerOptions(), this.options.datetimepicker);
    },
    getDefaultDatetimepickerOptions: function () {
        return {
            pickDate: true,
            pickTime: false
        }
    },
    getHtml: function () {
        var intervalContainer = this.html = $('<div>').addClass('rule-date-interval input-group row').attr('id', this.id);
        var fromInput = $('<input>').addClass('rule-interval-from form-control').val(this.value.from);
        var toInput = $('<input>').addClass('rule-interval-to form-control').val(this.value.to);

        var fromInputGroup = $("<div class='input-group col-lg-6 col-md-6 col-sm-6 col-xs-6'></div>");
        fromInputGroup.append("<span class='input-group-addon'>From:</span>");
        fromInputGroup.append(fromInput);

        var toInputGroup = $("<div class='input-group col-lg-6 col-md-6 col-sm-6 col-xs-6'></div>");
        toInputGroup.append("<span class='input-group-addon'>To:</span>");
        toInputGroup.append(toInput);


        intervalContainer.append(fromInputGroup).append(toInputGroup);

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
    appendHtml: function (container) {
        this.container[this.id] = container;
        if (this.html === null) {
            this.html = this.getHtml();
        }
        this.html.find('input').datetimepicker(this.datetimepickerOptions);
        container.append(this.html);
    },
    validate: function () {
        var from = this.options.datetimepicker.pickDate === false ? '1970-01-01 ' + this.value.from : this.value.from;
        var to = this.options.datetimepicker.pickDate === false ? '1970-01-01 ' + this.value.to : this.value.to;
        return Date.parse(from) > 0 && Date.parse(to) > 0 && Date.parse(from) < Date.parse(to);
    }
});
