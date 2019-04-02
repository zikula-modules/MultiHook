'use strict';

function zikulaMultiHookValidateNoSpace(val) {
    var valStr;

    valStr = '' + val;

    return -1 === valStr.indexOf(' ');
}

/**
 * Runs special validation rules.
 */
function zikulaMultiHookExecuteCustomValidationConstraints(objectType, currentEntityId) {
}
