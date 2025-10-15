# VoiceCraft-PocketMine

PocketMine plugin/implementation of VoiceCraft Server Sided Positioning

**Current state**: UNFINISHED  
We're actively developing. The plugin doesn't exist yet!

See [TODO.md](TODO.md) for the current status!


## How to set up

1. Go to https://github.com/AvionBlock/VoiceCraft/wiki/Installing-the-Server
2. Follow the tutorial on how to setup a server. Skip the parts related to installing addon and related to installing module for BDS, as this is the replacement for that
3. Start VoiceCraft server and you will be given server key
4. Copy VoiceCraft server key into plugin's config
5. Start your PocketMine server and if you did everything right you will see that plugin connected successfully

## How to connect

1. Go to https://github.com/AvionBlock/VoiceCraft/wiki/Installing-the-Client
2. Follow the tutorial on how to install client on your device. Skip the parts related to binding
3. Once you connect to server you will be given binding code
4. Join PocketMine server and execute command `/voicecraft:bind [your_bind_key]`
5. Once you've done everything you will be connected to VoiceCraft server and you can talk to others


# Developers

## Using source code

When you have the DevTools plugin installed in pocketmine (it should come with it by default)
then you should be able to just upload the whole repo as a folder to your plugins folder.


## Using .phar file (easier)

To build the project into a .phar, you'll need to have php (8) with `php_yaml` extension installed.  
Then run the following command:
```sh
php -dphar.readonly=0 .\tools\DevToolsConsoleScript.php --make ./ --out VoiceCraft.phar
```

Then upload the `VoiceCraft.phar` to the `plugins` folder in your server.


## PHPStan analysis
This repository shows an example setup for standalone local analysis of a plugin using [PHPStan](https://phpstan.org).

It uses [Composer](https://getcomposer.org) for autoloading, allowing you to install PHPStan extensions such as [phpstan-strict-rules](https://github.com/phpstan/phpstan-strict-rules). The configuration for this can be seen in [`phpstan/composer.json`](/phpstan/composer.json).

### Setting up PHPStan
Assuming you have Composer and a compatible PHP binary available in your PATH, run:
```
cd phpstan
composer install
```

Then you can run PHPStan exactly as you would with any other project:
```
vendor/bin/phpstan analyze
```

### Updating the dependencies
```
composer update
```

### GitHub Actions
You can find a workflow suitable for analysing most plugins using this system in [`.github/workflows/main.yml`](/.github/workflows/main.yml).
