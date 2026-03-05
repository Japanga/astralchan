<img src="https://i.imgur.com/QA71TAY.png" width="300">


AstralChan is a lightweight PHP imageboard application hosted on XAMPP and Apache. AstralChan aims to be much simpler to set-up and use right out of the box compared to most other open-source imageboard projects. Unlike those, AstralChan does not use SQL but does require the [XAMPP Control Panel](https://www.apachefriends.org/download.html) which is easily configurable to use SQL.

<img src="https://i.imgur.com/6cRz8kJ.png" width="300">

Boards are created fast and instantly, any folder in the <i><b>"boards/"</b></i> directory is turned into clickable links across all of the other boards and the front home page. Title data is retrieved from a titles.txt in each board.

<img src="https://i.imgur.com/Tr7OSrV.png" width="300">

Custom captcha system means no Cloudflare or Google reCaptcha systems are needed.

<img src="https://i.imgur.com/GAfsgNH.png" width="300">

Styles are highly customizable, and a complex "tagging" system allows for directly replying to # number IDs from previous posts.

<img src="https://i.imgur.com/ZhRF1Td.png" width="300">

Catalog style view displays all images uploaded to that board's <i><b>"upload/"</b></i>, folder.

## How to INSTALL AstralChan
<img src="https://i.imgur.com/YmOiBsG.png" width="300">

1. Download the [XAMPP Control Panel](https://www.apachefriends.org/download.html) (developed on 3.3.0)
2. Extract the contents of AstralChan to <i><b>"C:xampp/htdocs/"</b></i>
3. Open the XAMPP Control Panel in your Apache server
4. Visit the imageboard by going to <i><b>localhost/index.php</b></i>!

## Moderation/Admin Tools: Deleting Posts

<img src="https://i.imgur.com/5K5PmSv.png" width="300">

At the inside of each board folder directory is an <i><b>"/admin.php"</b></i> folder, where users enter in a private password stored only in the server-end version of the file where you define the <b>your_admin_password</b> hash (meaning regular users are completely unable to access it through methods like View page source.)
<img src="https://i.imgur.com/iuGrHZ2.png" width="460">

<img src="https://i.imgur.com/kUG4Bv0.png" width="460">

Once you are inside, you can successfully use the "Delete" option next to all posts to remove it along with any individual replies. Posts can easily be backed-up by storing copies of your database folder.
