/**
 * Class that handles the Group
 * @param {Definitions} fieldDefinitions
 * @param {boolean} canBeRemoved
 * @param {Object} ruleData
 * @param {callable} changeCallback
 * @constructor
 */
var Group = function (fieldDefinitions, ruleData, canBeRemoved) {
    this.fieldDefinitions = fieldDefinitions;
    this.canBeRemoved = (typeof canBeRemoved === 'undefined' ? true : canBeRemoved);
    this.ruleData = ruleData;
    this.id = guid();

    this.type = jQuery.isEmptyObject(ruleData) ? Group.TYPE_ALL : ruleData.logic_operator;
    this.title = jQuery.isEmptyObject(ruleData) ? '' : ruleData.title;
    this.wrapper = $("<div>").attr('id', this.id).addClass(this.type).addClass('conditional');

    this.items = {};
    this.processRuleData();
};

// Group "constants"
Group.TYPE_ALL = 'all';
Group.TYPE_ANY = 'any';
Group.TYPE_NONE = 'none';
Group.ITEM_GROUP = 'group';
Group.ITEM_CONDITION = 'cond';

/**
 *
 * @param {function} callback
 */
Group.prototype.setRemoveCallback = function (callback) {
    this.removeCallback = callback;
};
/**
 *
 * @param {function} callback
 */
Group.prototype.setChangeCallback = function (callback) {
    this.changeCallback = callback;
    for (var itemId in this.items) {
        this.items[itemId].setChangeCallback(callback);
    }
};

/**
 * Returns the html that contains group definition
 * @returns {null|*|jQuery}
 */
Group.prototype.appendHtml = function (parentWrapper) {
    parentWrapper.append(this.wrapper);
    if (this.canBeRemoved) {
        this.wrapper.append(this.getRemoveGroupElement());
    }
    this.wrapper.append(this.getGroupTypeSelect());
    this.wrapper.append(this.getAddConditionButton());
    this.wrapper.append(this.getAddGroupButton());
    this.wrapper.append(this.getEditableTitle());
    for (var itemId in this.items) {
        this.items[itemId].appendHtml(this.wrapper);
    }
};

/**
 * Initialized existing group from serialized data
 */
Group.prototype.processRuleData = function () {
    if (!$.isEmptyObject(this.ruleData)) {
        this.ruleData.items.forEach(function (item) {
            switch (item.item_type) {
                case Group.ITEM_GROUP:
                    this.addItem(new Group(this.fieldDefinitions, item.data, true), false);
                    break;
                case Group.ITEM_CONDITION:
                    this.addItem(new Condition(this.fieldDefinitions, item.data), false);
                    break;
            }
        }.bind(this));
    }
};

/**
 * Returns JQuery object that represents the select object for group type
 * @returns {*|jQuery|HTMLElement}
 */
Group.prototype.getGroupTypeSelect = function () {
    var selectWrapper = $("<div>", {"class": "all-any-none-wrapper"});
    var select = $("<select>", {"class": "all-any-none " + this.type, 'data-sonata-select2': false});
    select.append($("<option>", {"value": Group.TYPE_ALL, "text": "All", "selected": this.type === Group.TYPE_ALL}));
    select.append($("<option>", {"value": Group.TYPE_ANY, "text": "Any", "selected": this.type === Group.TYPE_ANY}));
    select.append($("<option>", {"value": Group.TYPE_NONE, "text": "None", "selected": this.type === Group.TYPE_NONE}));
    select.on('change', function (e) {
        this.wrapper.removeClass(this.type);
        this.type = $(e.target).val();
        this.wrapper.addClass(this.type);
    }.bind(this));
    selectWrapper.append(select);
    return selectWrapper;
};

/**
 * Returns JQuery object that represents the add condition button
 * @returns {*|jQuery|HTMLElement}
 */
Group.prototype.getAddConditionButton = function () {
    var btn = $("<a>", {href: 'javascript://', class: 'add-condition', text: 'Add Condition'});
    btn.append("<i class='glyphicon glyphicon-plus'></i>");
    btn.on('click', function (e) {
        this.addItem(new Condition(this.fieldDefinitions, {}), true);
    }.bind(this));
    return btn;
};

/**
 * Returns the JQuery object that represents the add group button
 * @returns {*|jQuery|HTMLElement}
 */
Group.prototype.getAddGroupButton = function () {
    var btn = $("<a>", {href: 'javascript://', class: 'add-condition-group', text: 'Add Group'});
    btn.append("<i class='glyphicon glyphicon-th-list'></i>")
    btn.on('click', function (e) {
        this.addItem(new Group(this.fieldDefinitions, {}, true), true);
    }.bind(this));
    return btn;
};

/**
 * Returns a Jquery object that represents the remove group button
 * @returns {*|jQuery}
 */
Group.prototype.getRemoveGroupElement = function () {
    var removeEl = $('<a>', {
        class: 'remove-group-button remove',
        href: 'javascript://'
    }).data('id', this.id);
    removeEl.append("<i class='glyphicon glyphicon-remove'></i>");

    removeEl.on('click', function (e) {
        this.remove();
        if (typeof this.removeCallback === 'function') {
            this.removeCallback(this.id);
        }
    }.bind(this));
    return removeEl;
};

/**
 * @returns {jQuery}
 */
Group.prototype.getEditableTitle = function () {
    var input = $('<input/>', {type: 'text', class: 'group-title-edit', value: this.title, placeholder: 'Group title'});
    input.change(function (e) {
        this.title = e.target.value;
    }.bind(this));
    return input;
};

/**
 * @param {Group|Condition} item
 * @param {boolean} render
 */
Group.prototype.addItem = function (item, render) {
    var itemId = item.getId();
    this.items[itemId] = item;
    item.setRemoveCallback(function () {
        delete this.items[itemId];
        if (typeof this.changeCallback === 'function') {
            this.changeCallback(this.id);
        }
    }.bind(this));
    if (render) {
        item.appendHtml(this.wrapper);
    }
};

/**
 * Removes this group and his associated groups and conditions
 */
Group.prototype.remove = function () {
    for (var id in this.items) {
        this.items[id].remove();
    }
    this.wrapper.remove();
};

/**
 * Returns the group id
 * @returns {null|string|*}
 */
Group.prototype.getId = function () {
    return this.id;
};

/**
 * Serialized the current group
 * @returns {{}}
 */
Group.prototype.serialize = function () {
    if ($.isEmptyObject(this.items)) {
        return {};
    }
    var serialized = {
        logic_operator: this.type,
        title: this.title,
        items: []
    };
    for (var id in this.items) {
        var itemData = this.items[id].serialize();
        if (!$.isEmptyObject(itemData)) {
            var itemType;
            switch (true) {
                case this.items[id] instanceof Group:
                    itemType = Group.ITEM_GROUP;
                    break;
                case this.items[id] instanceof Condition:
                    itemType = Group.ITEM_CONDITION;
                    break;
            }
            serialized.items.push({item_type: itemType, data: itemData});
        }
    }
    return serialized;
};
