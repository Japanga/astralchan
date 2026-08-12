// index.js
const express = require('express');
const phpExpress = require('php-express')({ binPath: 'php' });
const app = express();

// Set up the PHP engine
app.engine('php', phpExpress.engine);
app.all(/.+\\.php$/, phpExpress.router);

// Route the root directory directly to your index.php
app.get('/', (req, res) => {
    res.render('index.php');
});

app.listen(3000, () => {
    console.log('Server is running on http://localhost:3000');
});
