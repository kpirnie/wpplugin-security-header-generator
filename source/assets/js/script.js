// Store ONLY the initial user values
const _initial_values = {
    styles: '',
    scripts: '',
    fonts: '',
    images: '',
    connect: '',
    frames: '',
    worker: '',
    media: ''
};

// Object to store original field values when "None" is selected
const cspFieldBackups = {};

jQuery(document).ready(function ($) {

    // Track user modifications to fields
    $('[data-depend-id^="generate_csp_custom_"]').not('[data-depend-id$="_allow_unsafe"]').on('change blur keyup', function () {
        const fieldId = $(this).attr('data-depend-id');
        const key = fieldId.replace('generate_csp_custom_', '');
        if (_initial_values[key] !== undefined) {
            _initial_values[key] = $(this).val() || '';
        }
    });

    // get our access methods allow all
    var _amaa = jQuery('[data-depend-id="include_acam_methods"][value="*"]');

    // check the click event
    _amaa.on('click', function () {

        // hold the checked
        _ischecked = jQuery(this).is(':checked');

        // get the rest of the checkboxes for this
        _amacbs = jQuery('[data-depend-id="include_acam_methods"]');

        // set the other checkboxes based on this one
        _amacbs.prop("checked", _ischecked);

    });

    // Capture initial values after page load
    setTimeout(function () {
        _initial_values.styles = jQuery('[data-depend-id="generate_csp_custom_styles"]').val() || '';
        _initial_values.scripts = jQuery('[data-depend-id="generate_csp_custom_scripts"]').val() || '';
        _initial_values.fonts = jQuery('[data-depend-id="generate_csp_custom_fonts"]').val() || '';
        _initial_values.images = jQuery('[data-depend-id="generate_csp_custom_images"]').val() || '';
        _initial_values.connect = jQuery('[data-depend-id="generate_csp_custom_connect"]').val() || '';
        _initial_values.frames = jQuery('[data-depend-id="generate_csp_custom_frames"]').val() || '';
        _initial_values.worker = jQuery('[data-depend-id="generate_csp_custom_workers"]').val() || '';
        _initial_values.media = jQuery('[data-depend-id="generate_csp_custom_media"]').val() || '';
    }, 250);

    // handle the NONE CSP checkboxes
    handleNoneCSP();

});

function backupAndClearFields(checkbox) {

    const checkboxId = checkbox.attr('data-depend-id');
    const fieldId = getFieldIdFromCheckboxId(checkboxId);
    const $field = jQuery(`[data-depend-id="${fieldId}"]`);

    // Backup the current value if not already stored
    if (!cspFieldBackups[fieldId]) {
        cspFieldBackups[fieldId] = $field.val();
    }

    // Clear the field
    $field.val('');

    // Update _initial_values to reflect the cleared state
    const key = fieldId.replace('generate_csp_custom_', '');
    if (_initial_values[key] !== undefined) {
        _initial_values[key] = '';
    }
}

function restoreFields(checkbox) {
    const checkboxId = checkbox.attr('data-depend-id');
    const fieldId = getFieldIdFromCheckboxId(checkboxId);
    const $field = jQuery(`[data-depend-id="${fieldId}"]`);

    // Restore the original value if a backup exists
    if (cspFieldBackups[fieldId] !== undefined) {
        $field.val(cspFieldBackups[fieldId]);

        // Update _initial_values to reflect the restored state
        const key = fieldId.replace('generate_csp_custom_', '');
        if (_initial_values[key] !== undefined) {
            _initial_values[key] = cspFieldBackups[fieldId];
        }

        delete cspFieldBackups[fieldId]; // Clear backup
    }
}

function getFieldIdFromCheckboxId(checkboxId) {
    return checkboxId.replace('_allow_unsafe', '');
}

// get the grouped checkboxes
function getCheckboxGroupId(checkbox) {
    return checkbox.attr('data-depend-id').replace(/_allow_unsafe$/, '');
}

// make sure we're only unchecking the proper others
function handleNoneCSP() {

    jQuery(document).on('change', '[data-depend-id$="_allow_unsafe"]', function () {
        const $changedCheckbox = jQuery(this);
        const groupId = $changedCheckbox.attr('data-depend-id').replace('_allow_unsafe', '');
        const isValue3 = $changedCheckbox.attr('value') === '3';
        const isChecked = $changedCheckbox.prop('checked');

        const $groupCheckboxes = jQuery(`[data-depend-id="${groupId}_allow_unsafe"]`);

        // Case 1: "None" (value="3") is checked → clear other checkboxes + backup & clear fields
        if (isValue3 && isChecked) {
            $groupCheckboxes.not($changedCheckbox).prop('checked', false);
            backupAndClearFields($changedCheckbox);
        }
        // Case 2: Another checkbox is checked → uncheck "None" and restore fields
        else if (!isValue3 && isChecked) {
            $groupCheckboxes.filter('[value="3"]').prop('checked', false);
            if (Object.keys(cspFieldBackups).length > 0) {
                restoreFields($changedCheckbox);
            }
        }
    });

}

// remove the duplicates
String.prototype.remDups = function () {
    const set = new Set(this.split(' '))
    return [...set].join(' ')
}