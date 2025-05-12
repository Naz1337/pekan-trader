import './bootstrap';

import Alpine from 'alpinejs';

window.cleanNumberString = function (input) {
    // Regular expression to match a valid number string (including leading zeros)
    const numberRegex = /^0*[0-9]+$/;

    // Check if input is a valid number string
    if (typeof input === 'string' && numberRegex.test(input)) {
        // Remove leading zeros using replace with regex
        const cleaned = input.replace(/^0+/, '');
        // Handle case where input is all zeros (e.g., "0000")
        return cleaned === '' ? '0' : cleaned;
    } else {
        return null; // or return "Invalid input" if you prefer
    }
}

window.Alpine = Alpine;
Alpine.start();
