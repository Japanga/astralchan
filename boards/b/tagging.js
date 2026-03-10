//tagging.js    
document.addEventListener("DOMContentLoaded", function() {
    // Regex: Match # followed by one or more digits (\d+)
    const regex = /#(\d+)/g;
    
    // Replace function: wraps the matched text in an <a> tag
    // $1 refers to the numbers, e.g., in #123, it's 123
    document.body.innerHTML = document.body.innerHTML.replace(regex, 
        '<b><a href="#$1" class="hash-link">#$1</a></b>');
});
