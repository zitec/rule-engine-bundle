/**
 * Operator class
 * @param definition
 * @constructor
 */
var Operator = function (definition) {
    this.name = definition.name;
    this.label = definition.label;
    this.fieldType = definition.fieldType;
    this.fieldOptions = definition.fieldOptions || {};
};

/**
 * @returns {string}
 */
Operator.prototype.getName = function () {
    return this.name;
};

/**
 * @returns {string}
 */
Operator.prototype.getLabel = function () {
    return this.label;
};

/**
 * @returns {string}
 */
Operator.prototype.getFieldType = function () {
    return this.fieldType;
};

/**
 * @returns {{}}
 */
Operator.prototype.getFieldOptions = function () {
    return this.fieldOptions;
};
