/**
 * Created by george.calcea on 12/27/2016.
 */

var TextType = BaseField.extend({
    getHtml: function () {
        var input = $('<input>').addClass('rule-text form-control').attr('id', this.id).val(this.value);

        input.change(function (e) {
            this.value = $(e.target).val();
            this.validCheck();
            this.triggerChange();
        }.bind(this));

        return input;
    },
    validate: function () {
        return this.value !== null && this.value !== '';
    }
});
