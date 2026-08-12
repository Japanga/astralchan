// index.js
const { exec } = require('child_process');
const path = require('path');

// Get absolute path to index.php in the root directory
const phpFilePath = path.join(__dirname, 'index.php');

// Execute the PHP file using the local PHP CLI
exec(`php "${phpFilePath}"`, (error, stdout, stderr) => {
    if (error) {
        console.error(`Execution Error: ${error.message}`);
        return;
    }
    if (stderr) {
        console.error(`PHP Error: ${stderr}`);
        return;
    }
    // Print the rendered HTML output from the PHP file
    console.log(stdout); 
});
