'use strict';

function zikulaMultiHookValidateNoSpace(val) {
    var valStr;
    valStr = new String(val);

    return (valStr.indexOf(' ') === -1);
}

/**
 * Runs special validation rules.
 */
function zikulaMultiHookExecuteCustomValidationConstraints(objectType, currentEntityId) {
}
