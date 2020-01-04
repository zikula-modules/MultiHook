'use strict';

var editedObjectType;
var editedEntityId;
var editForm;
var formButtons;
var triggerValidation = true;

function zikulaMultiHookTriggerFormValidation() {
    zikulaMultiHookExecuteCustomValidationConstraints(editedObjectType, editedEntityId);

    if (!editForm.get(0).checkValidity()) {
        // This does not really submit the form,
        // but causes the browser to display the error message
        editForm.find(':submit').first().click();
    }
}

function zikulaMultiHookHandleFormSubmit(event) {
    if (triggerValidation) {
        zikulaMultiHookTriggerFormValidation();
        if (!editForm.get(0).checkValidity()) {
            event.preventDefault();
            return false;
        }
    }

    // hide form buttons to prevent double submits by accident
    formButtons.each(function (index) {
        jQuery(this).addClass('d-none');
    });

    return true;
}

/**
 * Initialises an entity edit form.
 */
function zikulaMultiHookInitEditForm(mode, entityId) {
    if (jQuery('.zikulamultihook-edit-form').length < 1) {
        return;
    }

    editForm = jQuery('.zikulamultihook-edit-form').first();
    editedObjectType = editForm.attr('id').replace('EditForm', '');
    editedEntityId = entityId;

    if (jQuery('#moderationFieldsSection').length > 0) {
        jQuery('#moderationFieldsContent').addClass('d-none');
        jQuery('#moderationFieldsSection legend').addClass('pointer').click(function (event) {
            if (jQuery('#moderationFieldsContent').hasClass('d-none')) {
                jQuery('#moderationFieldsContent').removeClass('d-none');
                jQuery(this).find('i').removeClass('fa-expand').addClass('fa-compress');
            } else {
                jQuery('#moderationFieldsContent').addClass('d-none');
                jQuery(this).find('i').removeClass('fa-compress').addClass('fa-expand');
            }
        });
    }

    var allFormFields = editForm.find('input, select, textarea');
    allFormFields.change(function (event) {
        zikulaMultiHookExecuteCustomValidationConstraints(editedObjectType, editedEntityId);
    });

    formButtons = editForm.find('.form-buttons input');
    if (editForm.find('.btn-danger').length > 0) {
        editForm.find('.btn-danger').first().bind('click keypress', function (event) {
            if (!window.confirm(Translator.__('Do you really want to delete this entry?'))) {
                event.preventDefault();
            }
        });
    }
    editForm.find('button[type=submit]').bind('click keypress', function (event) {
        triggerValidation = !jQuery(this).attr('formnovalidate');
    });
    editForm.submit(zikulaMultiHookHandleFormSubmit);

    if ('create' !== mode) {
        zikulaMultiHookTriggerFormValidation();
    }
}

