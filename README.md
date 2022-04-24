<h1>Last Inventory&nbsp;<img src="https://poggit.pmmp.io/ci.shield/CodsXBlastin/LastInventory/~" alt="poggit"/></h1>
A JSON based Last Inventory System

## How To Install
1. Download the *latest* .phar file.
2. Place the .phar file inside your Server's Directory for Plugins (Referred to as /plugins/).
3. Reload/Restart your server.
4. Enjoy!

## API (for Developers)
```php
use CodsXBlastin\LastInventory\Loader;

/** 
 * $player is Defined as a PlayerObject
 * Function Returns Player Data or Alerts Console of Missing Data
 */
$example = Loader::getInstance()->get($player);
/** Returns the Player Serialized Last Inventory */
return $example["items"];
/** Returns the Player Serialized Last Armor Inventory */
return $exmaple["armor"];
/** Don't Forget to Deserialize the Returned Items */
```

## Extra Stuff
This plugin __**DOESN'T**__ support spoons/variants of PocketMine. Support for such are few and far between.

This project is licensed under the Apache License - see the [LICENSE](LICENSE) for more Information.
