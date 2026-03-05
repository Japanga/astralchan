 //tagging.js
function makePatternsClickable(containerId) {
    const container = document.getElementById(containerId);
    if (!container) {
        console.error('Container element not found:', containerId);
        return;
    }

    // Regex explanation:
    // (?:#|>>): a non-capturing group to match either '#' or '>>'
    // (\\d+): a capturing group (backreference $1) to match one or more digits
    // g: global flag (match all occurrences)
    // i: case-insensitive flag (optional, but good practice)
    const patternRegex = /(?:#|>>)(\d+)/gi;

    // Get the current HTML content as a string
    let htmlContent = container.innerHTML;

    // Replace the matched patterns with anchor tags
    // The $1 refers to the first capturing group (the number itself)
    const replacedContent = htmlContent.replace(patternRegex, (match, number) => {
        // Construct the desired URL here
        const url = `#${number}`; 
        return `<a href="${url}">${match}</a>`;
    });

    // Update the container's HTML content
    container.innerHTML = replacedContent;
}

// Call the function when the page loads or the DOM is ready
document.addEventListener('DOMContentLoaded', (event) => {
    makePatternsClickable('content-container');
});