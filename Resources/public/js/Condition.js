/**
 * Created by george.calcea on 12/27/2016.
 */

/**
 * Class that handles the condition tree (condition type, operator, value)
 * @param {Definitions} fieldDefinitions
 * @param {Object} ruleData
 * @param {callable} changeCallback
 * @constructor
 */
var Condition = function (fieldDefinitions, ruleData) {
    this.fieldDefinitions = fieldDefinitions;
    this.ruleData = ruleData;
    this.id = guid();
    this.init();
};

/**
 * Init internal parameters
 */
Condition.prototype.init = function () {
    var descriptionIcon = $("<a href='#' class='help-icon' data-toggle='tooltip' data-placement='right'><i class='glyphicon glyphicon-question-sign'></i></a>");

    this.wrapper = $('<div>', {class: 'condition-wrapper row'});
    this.parameterSelectWrapper = $('<div>', {class: 'condition-select-wrapper col-sm-3'});
    this.wrapper.append(this.parameterSelectWrapper);
    this.wrapper.append(descriptionIcon);
    this.operatorSelectWrapper = $('<div>', {class: 'condition-operators-wrapper col-sm-3'});
    this.wrapper.append(this.operatorSelectWrapper);
    this.valueFieldWrapper = $('<div>', {class: 'condition-values-wrapper col-sm-3'});
    this.wrapper.append(this.valueFieldWrapper);
    this.help = descriptionIcon;

    if ($.isEmptyObject(this.ruleData)) {
        this.selectedCondition = this.fieldDefinitions.getFirstConditionDefinition();
        this.selectedOperator = this.selectedCondition.getFirstOperatorDefinition();
    } else {
        this.selectedCondition = this.fieldDefinitions.getParameterDefinition(this.ruleData.name);
        this.selectedOperator = this.selectedCondition.getOperatorDefinition(this.ruleData.operator);
    }

    this.help.attr("title",this.selectedCondition.getDescription());
    this.help.tooltip();
    this.generateFieldObject();
};

/**
 * Returns the HTML for current condition manager
 * @param {jQuery} parentWrapper
 */
Condition.prototype.appendHtml = function (parentWrapper) {
    parentWrapper.append(this.wrapper);
    this.wrapper.prepend(this.getRemoveButtonHtml());
    this.appendConditionsSelect(this.parameterSelectWrapper.empty());
    this.appendOperatorsSelect(this.operatorSelectWrapper.empty());
    this.field.appendHtml(this.valueFieldWrapper.empty());
};

/**
 * Returns an Jquery object for conditions select
 * @param {jQuery} wrapper
 */
Condition.prototype.appendConditionsSelect = function (wrapper) {
    var select = $('<select>', {class: 'conditions-select'});
    var propertiesList = this.fieldDefinitions.getParametersList();
    for (var name in propertiesList) {
        var optionOptions = {selected: this.selectedCondition.getName() === name};
        var option = $('<option>', optionOptions).val(name).text(propertiesList[name]);
        select.append(option);
    }
    select.on('change', function (e) {
        this.updateParameter($(e.target).val());
        this.appendOperatorsSelect(this.operatorSelectWrapper.empty());
        this.field.appendHtml(this.valueFieldWrapper.empty());
        var conditionDescription = this.selectedCondition.getDescription();
        this.help.attr({
            "title":conditionDescription,
            "data-original-title":conditionDescription
        }).tooltip();
        if (typeof this.changeCallback === 'function') {
            this.changeCallback(this.id);
        }
    }.bind(this));
    wrapper.append(select);
    select.select2();
};

/**
 * Returns an Jquery object for operators select
 * @param {jQuery} wrapper
 */
Condition.prototype.appendOperatorsSelect = function (wrapper) {
    var operators = this.selectedCondition.getOperatorsList();
    var select = $('<select>', {class: 'operator-select'});
    for (var operatorName in operators) {
        var optionOptions = {selected: this.selectedOperator.getName() === operatorName};
        var option = $('<option>', optionOptions).val(operatorName).text(operators[operatorName]);
        select.append(option);
    }
    select.on('change', function (e) {
        this.updateOperator($(e.target).val());
        this.field.appendHtml(this.valueFieldWrapper.empty());
        if (typeof this.changeCallback === 'function') {
            this.changeCallback(this.id);
        }
    }.bind(this));
    wrapper.append(select);
    select.select2();
};

/**
 * Initializes the Field object.
 */
Condition.prototype.generateFieldObject = function () {
    this.field = fieldsTypesFactory.factory(
        this.selectedOperator.getFieldType(),
        this.selectedOperator.getFieldOptions()
    );
    if (typeof this.changeCallback === 'function') {
        this.field.setChangeCallback(this.changeCallback);
    }
    if (!$.isEmptyObject(this.ruleData)) {
        this.field.setValue(this.ruleData.value);
    }
};

/**
 * Callback for operator change
 * @param {string} newOperator
 */
Condition.prototype.updateOperator = function (newOperator) {
    this.selectedOperator = this.selectedCondition.getOperatorDefinition(newOperator);
    this.generateFieldObject();
};

/**
 * Callback for condition change
 * @param {string} newParameter
 */
Condition.prototype.updateParameter = function (newParameter) {
    this.selectedCondition = this.fieldDefinitions.getParameterDefinition(newParameter);
    this.selectedOperator = this.selectedCondition.getFirstOperatorDefinition();
    this.generateFieldObject();
};

/**
 * Returns an Jquery object for remove condition button
 * @returns {*|jQuery|HTMLElement}
 */
Condition.prototype.getRemoveButtonHtml = function () {
    var removeEl = $('<a>', {
        class: 'remove-condition-button remove',
        id: this.id,
        href: 'javascript://'
    });
    removeEl.append("<i class='glyphicon glyphicon-remove'>");
    removeEl.on('click', function (e) {
        this.remove();
    }.bind(this));
    return removeEl;
};

/**
 *
 * @param {function} callback
 */
Condition.prototype.setRemoveCallback = function (callback) {
    this.removeCallback = callback;
};

/**
 *
 * @param {function} callback
 */
Condition.prototype.setChangeCallback = function (callback) {
    this.changeCallback = callback;
    this.field.setChangeCallback(callback);
};

/**
 * Callback that is executed on remove condition
 */
Condition.prototype.remove = function () {
    this.wrapper.remove();
    if (typeof this.removeCallback === 'function') {
        this.removeCallback(this.id);
    }
};

/**
 * Returns the manager id
 * @returns {*|null}
 */
Condition.prototype.getId = function () {
    return this.id;
};

/**
 * Serialized the current manager
 * @returns {*}
 */
Condition.prototype.serialize = function () {
    if (this.selectedOperator === null || this.selectedCondition === null) {
        return {};
    }
    return {
        name: this.selectedCondition.getName(),
        operator: this.selectedOperator.getName(),
        value: this.field.getValue()
    };
};
