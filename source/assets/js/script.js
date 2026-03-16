// Store initial text field values keyed by field id
const _initial_values = {};

// Backup store for text field values when "None" is checked
const cspFieldBackups = {};

/**
 * Given a CSP directive base key (e.g. "generate_csp_custom_child"), return
 * the text input and the set of _allow_unsafe checkboxes for that directive.
 *
 * The field framework nests group fields:
 *   text:       name="wpsh_settings[csp_group_{baseKey}][{baseKey}]"
 *   checkboxes: name="wpsh_settings[csp_group_{baseKey}][{baseKey}_allow_unsafe][]"
 */
function getCspTextField(baseKey) {
    return document.querySelector(
        `[name="wpsh_settings[csp_group_${baseKey}][${baseKey}]"]`
    );
}

function getCspCheckboxes(baseKey) {
    return document.querySelectorAll(
        `[name="wpsh_settings[csp_group_${baseKey}][${baseKey}_allow_unsafe][]"]`
    );
}

/**
 * Extract the baseKey from a checkbox's name attribute.
 * e.g. wpsh_settings[csp_group_generate_csp_custom_child][generate_csp_custom_child_allow_unsafe][]
 *   → generate_csp_custom_child
 */
function getBaseKeyFromCheckbox(checkbox) {
    const match = checkbox.name.match(/\[csp_group_([^\]]+)\]\[/);
    return match ? match[1] : null;
}

document.addEventListener('DOMContentLoaded', function () {

    // Capture initial text field values for all CSP directives
    setTimeout(function () {
        document.querySelectorAll('input[type="text"][id^="generate_csp_custom_"]').forEach(function (el) {
            _initial_values[el.id] = el.value || '';
        });
    }, 250);

    // Track user edits to CSP text fields
    document.querySelectorAll('input[type="text"][id^="generate_csp_custom_"]').forEach(function (el) {
        el.addEventListener('change', updateInitialValue);
        el.addEventListener('blur',   updateInitialValue);
        el.addEventListener('keyup',  updateInitialValue);
    });

    // "Allow All" checkbox for Access-Control-Allow-Methods
    const amaa = document.querySelector('[name="wpsh_settings[include_acam_methods][]"][value="*"]');
    if (amaa) {
        amaa.addEventListener('click', function () {
            const isChecked = this.checked;
            document.querySelectorAll('[name="wpsh_settings[include_acam_methods][]"]').forEach(function (cb) {
                cb.checked = isChecked;
            });
        });
    }

    // Wire up the "None" mutual-exclusion logic for CSP checkboxes
    handleNoneCSP();
});

// Handle preset selection
document.addEventListener('change', function (e) {
    if (e.target.matches('[name="wpsh_settings[apply_csp_preset]"]')) {
        const presetKey = e.target.value;

        if (!presetKey || presetKey === 'none') return;

        if (typeof wpshPresets === 'undefined') {
            console.error('wpshPresets not defined');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'wpsh_load_preset');
        formData.append('preset_key', presetKey);
        formData.append('nonce', wpshPresets.nonce);

        fetch(wpshPresets.ajaxurl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.settings) {
                    applyPresetSettings(data.data.settings);
                } else {
                    console.error('Error loading preset:', data);
                }
            })
            .catch(error => console.error('Error loading preset:', error));
    }
});

function applyPresetSettings(settings) {

    // Clear all CSP text fields and checkboxes before applying preset values
    document.querySelectorAll('input[type="text"][id^="generate_csp_custom_"]').forEach(function (field) {
        field.value = '';
    });
    document.querySelectorAll('input[type="checkbox"][name*="generate_csp_custom_"]').forEach(function (field) {
        field.checked = false;
    });

    Object.keys(settings).forEach(function (key) {
        const value    = settings[key];
        const isUnsafe = key.endsWith('_allow_unsafe');

        if (isUnsafe) {
            // Checkbox group — name="wpsh_settings[csp_group_{baseKey}][{key}][]"
            const baseKey    = key.replace('_allow_unsafe', '');
            const checkboxes = getCspCheckboxes(baseKey);

            checkboxes.forEach(function (cb) {
                cb.checked = Array.isArray(value) && value.includes(parseInt(cb.value, 10));
            });

        } else {
            // Try nested group text field first, then fall back to top-level field
            let field = getCspTextField(key);

            if (!field) {
                field = document.querySelector(`[name="wpsh_settings[${key}]"]`);
            }

            if (!field) return;

            if (field.type === 'radio') {
                document.querySelectorAll(`[name="${field.name}"]`).forEach(function (radio) {
                    radio.checked = (radio.value === String(value));
                });
            } else if (field.type === 'checkbox') {
                field.checked = Boolean(value);
            } else {
                field.value = value !== null && value !== undefined ? value : '';
            }

            // Let the framework react to the change (e.g. conditional visibility)
            field.dispatchEvent(new Event('change', { bubbles: true }));
        }
    });
}

function updateInitialValue(e) {
    if (_initial_values[e.target.id] !== undefined) {
        _initial_values[e.target.id] = e.target.value || '';
    }
}

function backupAndClearFields(checkbox) {
    const baseKey = getBaseKeyFromCheckbox(checkbox);
    if (!baseKey) return;

    const field = getCspTextField(baseKey);
    if (!field) return;

    if (cspFieldBackups[baseKey] === undefined) {
        cspFieldBackups[baseKey] = field.value;
    }

    field.value = '';

    if (_initial_values[baseKey] !== undefined) {
        _initial_values[baseKey] = '';
    }
}

function restoreFields(checkbox) {
    const baseKey = getBaseKeyFromCheckbox(checkbox);
    if (!baseKey) return;

    const field = getCspTextField(baseKey);
    if (!field) return;

    if (cspFieldBackups[baseKey] !== undefined) {
        field.value = cspFieldBackups[baseKey];

        if (_initial_values[baseKey] !== undefined) {
            _initial_values[baseKey] = cspFieldBackups[baseKey];
        }

        delete cspFieldBackups[baseKey];
    }
}

// Mutual exclusion: "None" (value=3) clears other checkboxes (and backs up the
// text field); checking anything else unchecks "None" (and restores the text field).
function handleNoneCSP() {
    document.addEventListener('change', function (e) {
        const target = e.target;

        if (target.type !== 'checkbox' || !target.name.includes('_allow_unsafe')) return;

        const baseKey = getBaseKeyFromCheckbox(target);
        if (!baseKey) return;

        const siblings = getCspCheckboxes(baseKey);
        const isNone   = target.value === '3';
        const checked  = target.checked;

        if (isNone && checked) {
            // "None" checked → uncheck everything else and clear the text field
            siblings.forEach(function (cb) {
                if (cb !== target) cb.checked = false;
            });
            backupAndClearFields(target);

        } else if (!isNone && checked) {
            // Something else checked → uncheck "None" and restore the text field
            siblings.forEach(function (cb) {
                if (cb.value === '3') cb.checked = false;
            });
            if (Object.keys(cspFieldBackups).length > 0) {
                restoreFields(target);
            }
        }
    });
}

// Remove duplicate space-separated tokens
String.prototype.remDups = function () {
    const set = new Set(this.split(' '));
    return [...set].join(' ');
};