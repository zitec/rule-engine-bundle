/**
 * Created by george.calcea on 12/27/2016.
 */

/**
 * Class that represented the input type text for condition builder
 * @param options
 * @param ruleData
 * @constructor
 */
var SelectType = BaseField.extend({
    getHtml: function () {
        var select = $('<select>').addClass('rule-select').attr('id', this.id);
        if (this.options.multiple) {
            select.attr('multiple', true);
        }
        this.options.options.forEach(function (value) {
            select.append($('<option>').val(value.key).text(value.label));
        });
        select.val(this.value);

        select.change(function (e) {
            this.value = $(e.target).val();
            this.validCheck();
            this.triggerChange();
        }.bind(this));

        return select;
    },
    appendHtml: function (container) {
        this.container[this.id] = container;
        var html = this.getHtml();
        container.append(html);
        html.css('width', '100%');
        html.select2();
    },
    validate: function () {
        return this.value !== null && this.value !== '';
    }
});
