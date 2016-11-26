# SteamGameBanner
A webpage that loads the game a certain Steam player is playing and displays the game's banner. 

The purpose is for it to be used as an [OBS](https://obsproject.com/) overlay when swapping between multiple games, this to avoid having to change banners manually.

## Setup
1. You need a local HTTP server with **PHP 7.x**, it's as easy as installing [XAMPP](https://www.apachefriends.org/download.html).
2. Clone this repo to a folder in your server www root (like _C:/xampp/htdocs_) or download the project and unpack it there.
	* If you cloned you might want to duplicate the _config.php_ in the root to _config.local.php_ so your settings will not be overwritten if you pull updates.
3. In the config file insert your [Steam Web API Key](https://steamcommunity.com/dev/apikey) and your [Steam User ID](http://steamidfinder.com/) (click _Generate forum signature_ and copy the _steamid_ from the url). 
    * Optionally set the intervalMs to the delay between loads. It's a good idea to keep the rate low to not reach your API call limit and be kind to the Steam servers. Defaults to 30000 which is every 30 seconds.

## Usage
* If you use OBS Studio you add a _BrowserSource_ to your scene, insert the URL to the local page, set a size and FPS to 1 and you are done! 
    * The image scales automatically to fill the view, to get all of it to be visible you should use a ratio close to 92:43, original image size is 460x215 px. (2016-11-26)