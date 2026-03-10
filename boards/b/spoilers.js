//spoilers.js 
        // Function to process the entire body content and create spoiler elements
        function processSpoilers() {
            const bodyHtml = document.body.innerHTML;
            // Use a regular expression to find all occurrences of [spoiler]...[/spoiler]
            const updatedHtml = bodyHtml.replace(
                /\[spoiler\](.*?)\[\/spoiler\]/g,
                '<span class="spoiler" onclick="toggleReveal(this, event)">$1</span>'
            );
            document.body.innerHTML = updatedHtml;
        }

        // Function to toggle the 'revealed' class on click, good for mobile
        function toggleReveal(element, event) {
            event.preventDefault();
            element.classList.toggle('revealed');
        }

        // Run the processing function when the page loads
        window.onload = processSpoilers;