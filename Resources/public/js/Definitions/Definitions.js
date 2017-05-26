/**
 * Condition definitions manager.
 *
 * @param definitions
 * @constructor
 */
var Definitions = function (definitions) {
    this.properties = {};
    var _this = this;
    $.each(definitions, function (key, field) {
        _this.properties[field.name] = new Parameter(field);
    });
};

/**
 * @param name
 * @returns {Parameter}
 */
Definitions.prototype.getParameterDefinition = function(name) {
    return this.properties[name];
};

/**
 * @returns {Parameter}
 */
Definitions.prototype.getFirstConditionDefinition = function() {
    for (var name in this.properties) {
        return this.properties[name];
    }
};

/**
 * return {{name: label}}
 */
Definitions.prototype.getParametersList = function() {
    var parameters = {};
    for (var name in this.properties) {
        parameters[name] = this.properties[name].label;
    }
    return parameters;
};
