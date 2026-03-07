<img src="https://i.imgur.com/hdpxr2W.png" width="300">


AstralChan is a lightweight PHP imageboard application. AstralChan was originally developed on XAMPP Control Panel and Apache, but I found several other methods of being able to deploy the site project files. Thanks to AeonFree hosting, a live instance of the .PHP files is visitable at any time at [https://astralchan.hstn.me/index.php](https://astralchan.hstn.me/index.php) !

<img src="https://i.imgur.com/6cRz8kJ.png" width="300">

Boards are created fast and instantly, any folder in the <i><b>"boards/"</b></i> directory is turned into clickable links across all of the other boards and the front home page. Title data is retrieved from a titles.txt in each board.

<img src="https://i.imgur.com/zsklVNK.png" width="300">

Custom captcha system means no Cloudflare or Google reCaptcha systems are needed.

<img src="https://i.imgur.com/VpenrTz.png" width="430">

Page layout style is highly customizable, and a complex "tagging" system allows for directly replying to # number IDs from previous posts. Image file sizes/formats are automatically displayable.

<img src="https://i.imgur.com/ZhRF1Td.png" width="300">

Catalog style view displays all images uploaded to that board's <i><b>"upload/"</b></i>, folder.

## How to deploy AstralChan method 1: XAMPP Control Panel
<img src="https://i.imgur.com/YmOiBsG.png" width="300">

1. Download the [XAMPP Control Panel](https://www.apachefriends.org/download.html) (developed on 3.3.0)
2. Extract the contents of AstralChan to <i><b>"C:xampp/htdocs/"</b></i>
3. Open the XAMPP Control Panel in your Apache server
4. Visit the imageboard by going to <i><b>localhost/index.php</b></i>!

## How to deploy AstralChan method 2: PHP command prompt
<img src="https://i.imgur.com/cttFxBu.png" width="300">

1. Use cd to get to the directory of your AstralChan install.
2. Temporarily change the system %PATH% directory to the location of your PHP install in command prompt.
```
set  PATH=%PATH%;C:\path\to\php
```
3. Deploy the AstralChan server to localhost with the following command!
```
php -S localhost:8000
```

## How to deploy AstralChan method 3: Deploy automatically in PyPHPDeploy
<img src="https://i.imgur.com/WLg3WaR.png" width="300">
While working in PHP and with AstralChan, I created a much simpler formula for deploying PHP apps that simplifies method 2 into a visual form that uses all of the same commands without having to manually enter them, and the user just has to get to their PHP and project directories from the file browser and set a custom localhost port. PyPHPDeploy can be found in my repository at https://github.com/Japanga/PyPHPDeploy/

## Moderation/Admin Tools: Deleting Posts

<img src="https://i.imgur.com/5K5PmSv.png" width="300">

At the inside of each board folder directory is an <i><b>"/admin.php"</b></i> file, where users enter in a private password stored only in the server-end version of the file where you define the <b>your_admin_password</b> hash (meaning regular users are completely unable to access it through methods like View page source.)
<img src="https://i.imgur.com/iuGrHZ2.png" width="460">

<img src="https://i.imgur.com/AEuw69y.png" width="460">

Once you are inside, you can successfully use the "Delete" option next to all posts to either remove the post directly or use a public ban message.

<img src="https://i.imgur.com/ykW1uWG.png" width="460">
