/**
 * Created by george.calcea on 12/27/2016.
 */

/**
 * Class that represents one condition with operators associated from condition builder
 * @param {{name:string, label: string, description: string, operators: {}}} conditionDefinition
 * @constructor
 */
var Parameter = function (conditionDefinition) {
    this.definition = conditionDefinition;
    this.name = conditionDefinition.name;
    this.label = conditionDefinition.label;
    this.description = conditionDefinition.description;
    this.initOperators();
};

/**
 * Builds operators
 */
Parameter.prototype.initOperators = function () {
    this.operators = {};
    var _this = this;
    $.each(this.definition.operators, function (key, operator) {
        _this.operators[operator.name] = new Operator(operator);
    });
};

/**
 * @returns {string}
 */
Parameter.prototype.getName = function () {
    return this.name;
};

/**
 * @returns {string}
 */
Parameter.prototype.getLabel = function () {
    return this.label;
};

/**
 * @returns {string}
 */
Parameter.prototype.getDescription = function () {
    return this.description;
};

/**
 * @param name
 * @returns {Operator}
 */
Parameter.prototype.getOperatorDefinition = function(name) {
    return this.operators[name];
};

/**
 * @returns {Operator}
 */
Parameter.prototype.getFirstOperatorDefinition = function() {
    for (var name in this.operators) {
        return this.operators[name];
    }
};

/**
 * @returns {{}}
 */
Parameter.prototype.getOperatorsList = function() {
    var operators = {};
    for (var name in this.operators) {
        operators[name] = this.operators[name].label;
    }
    return operators;
};
