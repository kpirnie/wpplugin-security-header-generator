// Store ONLY the initial user values - dynamically track all CSP fields
const _initial_values = {};

// Object to store original field values when "None" is selected
const cspFieldBackups = {};

document.addEventListener('DOMContentLoaded', function () {

    // Dynamically capture initial values for all CSP custom fields
    setTimeout(function () {
        document.querySelectorAll('[data-depend-id^="generate_csp_custom_"]').forEach(function (el) {
            const fieldId = el.getAttribute('data-depend-id');
            if (!fieldId.endsWith('_allow_unsafe')) {
                const key = fieldId.replace('generate_csp_custom_', '');
                _initial_values[key] = el.value || '';
            }
        });
    }, 250);

    // Track user modifications to fields
    document.querySelectorAll('[data-depend-id^="generate_csp_custom_"]').forEach(function (el) {
        const fieldId = el.getAttribute('data-depend-id');
        if (!fieldId.endsWith('_allow_unsafe')) {
            el.addEventListener('change', updateInitialValue);
            el.addEventListener('blur', updateInitialValue);
            el.addEventListener('keyup', updateInitialValue);
        }
    });

    // get our access methods allow all
    const amaa = document.querySelector('[data-depend-id="include_acam_methods"][value="*"]');

    // check the click event
    if (amaa) {
        amaa.addEventListener('click', function () {
            const isChecked = this.checked;
            const amacbs = document.querySelectorAll('[data-depend-id="include_acam_methods"]');
            amacbs.forEach(function (cb) {
                cb.checked = isChecked;
            });
        });
    }

    // handle the NONE CSP checkboxes
    handleNoneCSP();

});

// Handle preset selection
document.addEventListener('change', function (e) {
    if (e.target.matches('[name="wpsh_settings[apply_csp_preset]"]')) {
        const presetKey = e.target.value;

        console.log('Preset selected:', presetKey);

        if (!presetKey || presetKey === 'none') return;

        // Check if wpshPresets is defined
        if (typeof wpshPresets === 'undefined') {
            console.error('wpshPresets not defined');
            return;
        }

        console.log('Making AJAX request...');

        // Make AJAX request to get preset settings
        const formData = new FormData();
        formData.append('action', 'wpsh_load_preset');
        formData.append('preset_key', presetKey);
        formData.append('nonce', wpshPresets.nonce);

        fetch(wpshPresets.ajaxurl, {
            method: 'POST',
            body: formData
        })
            .then(response => {
                console.log('Response received:', response);
                return response.json();
            })
            .then(data => {
                console.log('Data:', data);
                if (data.success && data.data.settings) {
                    applyPresetSettings(data.data.settings);
                } else {
                    console.error('Error in response:', data);
                }
            })
            .catch(error => console.error('Error loading preset:', error));
    }
});

function applyPresetSettings(settings) {
    console.log('Applying settings:', settings);

    // First, clear all CSP fields
    document.querySelectorAll('[data-depend-id^="generate_csp_custom_"]').forEach(function (field) {
        if (field.type === 'checkbox') {
            field.checked = false;
        } else if (field.type === 'text' || field.type === 'textarea') {
            field.value = '';
        }
    });

    // Now apply the preset settings
    Object.keys(settings).forEach(function (key) {
        // Try multiple selector patterns
        let field = document.querySelector(`[name="wpsh_settings[${key}]"]`);

        if (!field) {
            field = document.querySelector(`[data-depend-id="${key}"]`);
        }

        if (!field) {
            console.log('Field not found:', key);
            return;
        }

        console.log('Found field:', key, field);

        const value = settings[key];

        // Handle different field types
        if (field.type === 'checkbox') {
            // For checkbox arrays
            const checkboxes = document.querySelectorAll(`[name="wpsh_settings[${key}][]"]`);
            if (checkboxes.length > 0) {
                checkboxes.forEach(function (cb) {
                    if (Array.isArray(value)) {
                        cb.checked = value.includes(parseInt(cb.value));
                    }
                });
            } else {
                field.checked = value;
            }
        } else if (field.type === 'radio') {
            const radios = document.querySelectorAll(`[name="wpsh_settings[${key}]"]`);
            radios.forEach(function (radio) {
                radio.checked = (radio.value === value);
            });
        } else {
            // Text, textarea, select
            field.value = value;
        }

        // Trigger change event to update UI
        field.dispatchEvent(new Event('change', { bubbles: true }));
    });
}

function updateInitialValue(e) {
    const fieldId = e.target.getAttribute('data-depend-id');
    const key = fieldId.replace('generate_csp_custom_', '');
    if (_initial_values[key] !== undefined) {
        _initial_values[key] = e.target.value || '';
    }
}

function backupAndClearFields(checkbox) {
    const checkboxId = checkbox.getAttribute('data-depend-id');
    const fieldId = getFieldIdFromCheckboxId(checkboxId);
    const field = document.querySelector(`[data-depend-id="${fieldId}"]`);

    if (!field) return;

    // Backup the current value if not already stored
    if (!cspFieldBackups[fieldId]) {
        cspFieldBackups[fieldId] = field.value;
    }

    // Clear the field
    field.value = '';

    // Update _initial_values to reflect the cleared state
    const key = fieldId.replace('generate_csp_custom_', '');
    if (_initial_values[key] !== undefined) {
        _initial_values[key] = '';
    }
}

function restoreFields(checkbox) {
    const checkboxId = checkbox.getAttribute('data-depend-id');
    const fieldId = getFieldIdFromCheckboxId(checkboxId);
    const field = document.querySelector(`[data-depend-id="${fieldId}"]`);

    if (!field) return;

    // Restore the original value if a backup exists
    if (cspFieldBackups[fieldId] !== undefined) {
        field.value = cspFieldBackups[fieldId];

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
    return checkbox.getAttribute('data-depend-id').replace(/_allow_unsafe$/, '');
}

// make sure we're only unchecking the proper others
function handleNoneCSP() {
    document.addEventListener('change', function (e) {
        const target = e.target;
        const dependId = target.getAttribute('data-depend-id');

        if (dependId && dependId.endsWith('_allow_unsafe')) {
            const groupId = dependId.replace('_allow_unsafe', '');
            const isValue3 = target.value === '3';
            const isChecked = target.checked;

            const groupCheckboxes = document.querySelectorAll(`[data-depend-id="${groupId}_allow_unsafe"]`);

            // Case 1: "None" (value="3") is checked → clear other checkboxes + backup & clear fields
            if (isValue3 && isChecked) {
                groupCheckboxes.forEach(function (cb) {
                    if (cb !== target) {
                        cb.checked = false;
                    }
                });
                backupAndClearFields(target);
            }
            // Case 2: Another checkbox is checked → uncheck "None" and restore fields
            else if (!isValue3 && isChecked) {
                groupCheckboxes.forEach(function (cb) {
                    if (cb.value === '3') {
                        cb.checked = false;
                    }
                });
                if (Object.keys(cspFieldBackups).length > 0) {
                    restoreFields(target);
                }
            }
        }
    });
}

// remove the duplicates
String.prototype.remDups = function () {
    const set = new Set(this.split(' '))
    return [...set].join(' ')
}