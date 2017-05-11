# SteamGameBanner
A webpage that loads the game a certain Steam player is playing and displays the game's banner. 

The purpose is for it to be used as an [OBS](https://obsproject.com/) overlay when swapping between multiple games, this to avoid having to change banners manually.

## Setup
1. You need a HTTP server, local or remote, with **PHP 7.x**. It's as easy as installing [XAMPP](https://www.apachefriends.org/download.html). Set it to auto-start Apache if you want convenience.
2. Clone this repo to a folder in your server www-root (like `C:/xampp/htdocs`) or download the project and unpack it there, name the folder something reasonable.
	* If you cloned you might want to duplicate the `config.php` in the root to `config.local.php` so your settings will not be overwritten if you pull updates.
3. In the config file...
    1. Insert your [Steam Web API Key](https://steamcommunity.com/dev/apikey), you can register with "_localhost_" as domain if you don't have one.
    2. Insert your Steam User ID, you can find it [here](http://steamidfinder.com/) or [here](https://steamid.io), you should copy the value named `steamID64`. 
    3. Optional: set the intervalMs to the delay between loads. It's a good idea to keep the rate low to not reach your API call limit and be kind to the Steam servers. Defaults to 30000 which is every 30 seconds.
    4. Optional: to have a banner in place when a game is not detected put an image in a `/res/` folder in the root, insert the filename only as `placeholder` in the config.

## Usage
* If you use OBS Studio you add a _BrowserSource_ to your scene, insert the URL to the local page, set a size and FPS to 1 and you are done! 
    * The image scales automatically to fill the view, to get all of it to be visible you should use a ratio close to 92:43, original image size is 460x215 px. (2016-11-26)