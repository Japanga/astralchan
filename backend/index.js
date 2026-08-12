// index.js
const { exec } = require('child_process');
const path = require('path');

// Resolve the path to index.php in the same root directory
const phpFilePath = path.join(__dirname, 'index.php');

// Execute the PHP file using the local PHP command-line tool
exec(`php "${phpFilePath}"`, (error, stdout, stderr) => {
    if (error) {
        console.error(`Execution Error: ${error.message}`);
        return;
    }
    if (stderr) {
        console.error(`PHP Error: ${stderr}`);
        return;
    }
    // Print the evaluated output of the PHP file to your console
    console.log(stdout);
});
