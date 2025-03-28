/**
 * Check if the input value is empty or contains only whitespace.
 * If the value is empty, it sets the error message in the corresponding error element.
 *
 * @param {string} value - The input value to be checked.
 * @param {object} errorElement - The jQuery object or DOM element where the error message will be displayed.
 * @param {string} errorMessage - The error message to display if the input is empty.
 * @returns {number} - Returns 1 if the value is empty (validation failed), otherwise 0.
 *
 * @example
 * // Usage example
 * let $nameInput = $('#name'); // Assume there's an input field with id 'name'
 * let nameErrMsg = 'Name is required';
 * let hasError = isEmpty($nameInput.val(), $nameInput.siblings('.err'), nameErrMsg);
 * if (hasError) {
 *     // Handle the error case (e.g., prevent form submission)
 * }
 * @sampleHTML
 * <div>
        <label for="name">Name:</label>
        <input type="text" id="name" name="emr_name" />
        <span class="err" style="color: red;"></span>
    </div>
 */
function isEmpty(value, errorElement, errorMessage) {
    // Check if the input value is empty or contains only spaces.
    if (!value.trim()) {
        // Display the error message in the corresponding error element (e.g., a <span> next to the input).
        if (errorElement && errorMessage) {
            errorElement.text(errorMessage);
        }
        // Return 1 to indicate that the validation failed (input is empty).
        return 1;
    }
    // If the input is not empty, clear any error message from the error element.
    if (errorElement) {
        errorElement.text(''); // Clear error if valid
    }
    // Return 0 to indicate that the validation passed (input is not empty).
    return 0;
}

/**
 * Check if the input value contains only alphabet characters and spaces.
 * If the value contains non-alphabetic characters, it sets the error message in the corresponding error element.
 *
 * @param {string} value - The input value to be checked.
 * @param {object} errorElement - The jQuery object or DOM element where the error message will be displayed.
 * @param {string} errorMessage - The error message to display if the input contains invalid characters.
 * @returns {number} - Returns 1 if the value contains invalid characters (validation failed), otherwise 0.
 *
 * @example
 * // Usage example
 * let $nameInput = $('#name'); // Assume there's an input field with id 'name'
 * let nameErrMsg_invalid = 'Only alphabets and spaces are allowed';
 * let hasError = isInvalidName($nameInput.val(), $nameInput.siblings('.err'), nameErrMsg_invalid);
 * if (hasError) {
 *     // Handle the error case (e.g., prevent form submission)
 * }
 */
function isInvalidName(value, errorElement, errorMessage) {
    // Use a regular expression to check if the input value contains only alphabetic characters and spaces.
    // ^[A-Za-z\s]+$ : Ensures the string contains only uppercase/lowercase letters and spaces.
    if (!/^[A-Za-z\s]+$/.test(value.trim())) {
        // Display the error message in the corresponding error element (e.g., a <span> next to the input).
        if (errorElement && errorMessage) {
            errorElement.text(errorMessage);
        }
        // Return 1 to indicate that the validation failed (invalid characters present).
        return 1;
    }
    // If the input contains only valid characters, clear any error message from the error element.
    if (errorElement) {
        errorElement.text(''); // Clear error if valid
    }
    // Return 0 to indicate that the validation passed (input is valid).
    return 0;
}
/**
 * Check if the input value is a valid 10-digit phone number.
 * If the value is not exactly 10 digits, it sets the error message in the corresponding error element.
 *
 * @param {string} value - The input value to be checked.
 * @param {object} [errorElement] - The jQuery object or DOM element where the error message will be displayed (optional).
 * @param {string} [errorMessage] - The error message to display if the phone number is invalid (optional).
 * @returns {number} - Returns 1 if the value is invalid (not 10 digits), otherwise 0.
 *
 * @example
 * let $phoneInput = $('#phone');
 * let hasError = isInvalidPhone($phoneInput.val(), $phoneInput.siblings('.err'), 'Must be 10 digits');
 */
function isInvalidPhone(value, errorElement, errorMessage) {
    if (!/^\d{10}$/.test(value.trim())) {
        // Display error message if an errorElement and errorMessage are provided
        if (errorElement && errorMessage) {
            errorElement.text(errorMessage);
        }
        return 1;
    }
    if (errorElement) {
        errorElement.text(''); // Clear error if valid
    }
    return 0;
}

function isInvalidEmail(value, errorElement, errorMessage) {
    // Check if the email is valid using a basic regex
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailRegex.test(value.trim())) {
        // Display error message if an errorElement and errorMessage are provided
        if (errorElement && errorMessage) {
            errorElement.text(errorMessage);
        }
        return 1;
    }
    if (errorElement) {
        errorElement.text(''); // Clear error if valid
    }
    return 0;
}

function isNotEmpty(value) {
    return value.trim() !== '';
}



//restrict enter submission
// $("form").on("keydown", function(event) {
//     if (event.key === "Enter") {
//         event.preventDefault(); // Prevents the default form submission
//     }
// });
$("form").on("keydown", function(event) {
    if (event.key === "Enter" && !$(event.target).is("textarea")) {
        event.preventDefault(); // Prevents the default form submission
    }
});

// $('input.phone-input-restrict').on('input', function () {
//     // Restrict to numeric input and limit the length to 10 digits
//     this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
// });

$(document).on('input', '.phone-input-restrict', function () {
    // Restrict to numeric input and limit to 10 digits
    this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
});
