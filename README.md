<img src="https://i.imgur.com/yuaJXq3.png" width="300">


AstralChan is a lightweight PHP imageboard application. AstralChan was originally developed on XAMPP Control Panel and Apache, but I found several other methods of being able to deploy the site project files. Thanks to AeonFree hosting, a live instance of the .PHP files is visitable at any time at [https://astralchan.hstn.me/index.php](https://astralchan.hstn.me/index.php) !

<img src="https://i.imgur.com/6cRz8kJ.png" width="300">

Boards are created fast and instantly, any folder in the <i><b>"boards/"</b></i> directory is turned into clickable links across all of the other boards and the front home page. Title data is retrieved from a titles.txt in each board.

<img src="https://i.imgur.com/gjwTiad.png" width="300">

Custom captcha system means no Cloudflare or Google reCaptcha systems are needed.

<img src="https://i.imgur.com/EEiEk6p.png" width="430">

Page layout style is highly customizable, and a complex "tagging" system allows for directly replying to # number IDs from previous posts. Image file sizes/formats are automatically displayable. Users can directly create "threads", and then reply to them with new images in subpages.


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

<img src="https://i.imgur.com/JF0UpuW.png" width="300">

At the inside of each board folder directory is an <i><b>"/admin.php"</b></i> file, where users enter in a private password stored only in the server-end version of the file where you define the <b>your_admin_password</b> hash (meaning regular users are completely unable to access it through methods like View page source.)
<img src="https://i.imgur.com/iuGrHZ2.png" width="460">

Once you are inside, you can succesfully enter the admin command console, and familiarize yourself with the impressive amount of tools and methods of removing posts.

<img src="https://i.imgur.com/u6paCWE.png" width="460">

Posts are either straight up deleted from the .json files, or more public methods like the red ban message or trash can icon can be applied to the posts.

<img src="https://i.imgur.com/C6pc9JE.png" width="460">

The trash canning of posts connects in to the archiving functionalities of AstralChan. On real imageboards, posts are often deleted from the live thread but are still stored in the archive and displayed as removed, and a similar effect is achieved here. When a thread is archived, it is not instantly removed. And the replies can be deleted/removed before the thread is finished. But no new replies can be made to threads in the archive.

## Pruning/auto-archiving
<img src="https://i.imgur.com/OP7CVZ0.png" width="460">
All boards support auto-archiving, but /b/ is currently the only board on the live site that is configured to properly "prune" threads after 24 hours. Manual archiving was quickly proven obsolete when the automatic archiving function was developed. Auto archiving was added to all boards for the efficiency, but pruning has only been added to /b/ because of wanting to preserve higher traffic threads on the other boards. To create a true pruning function without offline functions proved to be truly challenging, but by automatically backing up threads from the thread.php and routinely clearing old posts from posts.json in the thread.php, a very efficiently functioning machine for automatically purging old posts was developed.

## Shadowmasking: IP-level banning system
<img src="https://i.imgur.com/4qgiAuz.png" width="460">
"Shadowmasking" is an internal AstralChan code word for creating X hash strings from the user's IP address that can be stored in the server and used for hard banning users and rendering them unable to post to the service, beyond simply just deleting their posts. Guests to AstralChan's live site are valued, and the chances of you being shadowmask-banned are very slim. But the feature is still there if other site hosters need to use it.
