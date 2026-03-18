<!DOCTYPE html>
<html>
<head>
    <title>Report Post</title>
    <!-- Import EmailJS SDK -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script type="text/javascript">
        (function(){emailjs.init("HUF7uNLpUOa6DHCzr");})(); // Initialize with your Public Key
    </script>
</head>
<body>
    <h2>Report Post #<?php echo htmlspecialchars($_GET['post_id']); ?></h2>
    <form id="report-form">
        <input type="hidden" name="post_id" value="<?php echo htmlspecialchars($_GET['post_id']); ?>">
        <label>Reason:</label>
        <select name="reason">
            <option value="spoilers">This post contains intentional spoilers</option>
            <option value="inappropriate">Not work safe image on work safe board.</option>
             <option value="offtopic">This post is off-topic.</option>
             <option value="underage">This user is underage.</option>
             <option value="trolling">Trolling outside of /b/.</option>
            <option value="racism">Racism outside of /b/.</option>
            <option value="furrygurololikonshota">Furry/guro/lolikon/shota porn outside of /b/.</option>
            <option value="dubsgets">Posting dubs/post number GET.</option>
            <option value="personalinfo">This post contains personal information ("dox").</option>
            <option value="raid">This post contains a call to invasion ("raid").</option>
             <option value="lowquality">This post is extremely low quality.</option>
              <option value="spamming">Spamming/flooding</option>
             <option value="spambot">This post appears to be an automated spambot.</option>
              <option value="begging">This post is advertising or begging.</option>
             <option value="impersonation">User impersonating AstralChan staff.</option>
            <option value="avatarsignature">Avatar or signature use</option>
            <option value="request">Request thread ('source', 'sauce', 'MOAR', etc.)</option>
                <option value="announcing">Announcing a report or a 'sage'</option>
        </select>
        <button type="submit">Send Report</button>
    </form>

    <script>
        document.getElementById('report-form').addEventListener('submit', function(event) {
            event.preventDefault();
            emailjs.sendForm('service_78vxhad', 'template_a71se9z', this)
                .then(function() {
                    alert('Report Sent!');
                    window.location.href = document.referrer; // Return to previous page
                }, function(error) { alert('Failed...', error); });
        });
    </script>
</body>
</html>
