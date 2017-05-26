/**
 * Created by george.calcea on 12/27/2016.
 */

var fieldsTypesFactory = (function () {
    var TEXT_TYPE = 'text';
    var DATETIME_TYPE = 'datetime';
    var SELECT_TYPE = 'select';
    var INTERVAL_TYPE = 'interval';
    var DATETIME_INTERVAL_TYPE = 'datetime_interval';
    var AUTOCOMPLETE = 'autocomplete';

    var Factory = function () {

    };

    /**
     * Returns an instance for rule input type
     * @param type
     * @param options
     * @param ruleData
     * @returns {*}
     */
    Factory.prototype.factory = function (type, options) {
        switch (type) {
            case TEXT_TYPE:
                return new TextType(options);
            case DATETIME_TYPE:
                return new DateTimeType(options);
            case SELECT_TYPE:
                return new SelectType(options);
            case INTERVAL_TYPE:
                return new Interval(options);
            case DATETIME_INTERVAL_TYPE:
                return new DateTimeInterval(options);
            case AUTOCOMPLETE:
                return new AutocompleteType(options);
        }
    };

    return new Factory();
})();
