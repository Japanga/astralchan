//greentextandyoutube.js
 function makeYouTubeLinksClickable() {
    // Regular expression to find common YouTube URL formats
    var youtubeRegex = /(https?:\/\/(?:www\.)?(?:youtube\.com\/watch\?v=|youtu\.be\/)([a-zA-Z0-9_-]{11}))/g;

    // Find all paragraph elements on the page
    var paragraphs = document.getElementsByTagName('p');

    for (var i = 0; i < paragraphs.length; i++) {
        var paragraph = paragraphs[i];
        var originalText = paragraph.innerHTML;

        // Replace the plain URL text with an anchor tag
        var linkedText = originalText.replace(youtubeRegex, function(match, url) {
            // The match is the full URL found by the regex
            return '<a href="' + url + '">' + url + '</a>';
        });

        // Update the paragraph's HTML content only if a replacement was made
        if (linkedText !== originalText) {
            paragraph.innerHTML = linkedText;
        }
       // Check if the trimmed text content starts with '>'
        if (paragraph.textContent.startsWith('>')) {
            // If it does, change the text color to green
            paragraph.style.color = 'green';
        }
    }
}

// Run the function when the window loads
window.onload = makeYouTubeLinksClickable;
