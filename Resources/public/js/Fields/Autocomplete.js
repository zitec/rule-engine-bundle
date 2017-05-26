var AutocompleteType = BaseField.extend({
    autocompletePath: '/admin/rule-engine/autocomplete',
    init: function (options) {
        this.value = [];
        this._super(options);
        this.options.select2Options = this.options.select2Options || {};
        this.select2Options = $.extend(true, {}, this.getDefaultSelect2Options(), this.options.select2Options);
    },
    setValue: function (value) {
        if (Array.isArray(value)) {
            this.value = value;
        }
    },
    getDefaultSelect2Options: function () {
        return {
            minimumInputLength: 2,
            placeholder: 'Start typing...',
            multiple: true,
            ajax: {
                cache: true,
                dataType: 'json',
                url: this.autocompletePath + '?mode=like&key=' + this.options.autocomplete,
                data: function (term, page) {
                    return {'q': term, 'page': page};
                },
                results: function (data, page, query) {
                    return {results: data.items, more: data.more};
                },
                initSelection: function (element, callback) {

                }
            }
        }
    },
    getHtml: function () {
        return $('<input>').attr('type', 'hidden').attr('id', this.id);
    },
    appendHtml: function (container) {
        this.container[this.id] = container;
        var html = this.getHtml();
        container.append(html);
        html.change(function (e) {
            this.value = $(e.target).select2('val');
            this.validCheck();
            this.triggerChange();
        }.bind(this));
        if (this.value.length) {
            this.populateValues(html);
        }
        html.select2(this.select2Options);
    },
    populateValues: function (html) {
        var _this = this;
        html.val(this.value.join(','));
        this.select2Options.initSelection = function (html, callback) {
            $.ajax({
                url: _this.autocompletePath,
                data: {
                    mode: 'label',
                    key: _this.options.autocomplete,
                    q: _this.value
                },
                dataType: "json"
            })
                .done(function (data) {
                    callback(data);
                })
                .fail(function () {
                    alert('Could not get labels!');
                });
        };
    },
    validate: function() {
        return this.value.length > 0;
    }
});
