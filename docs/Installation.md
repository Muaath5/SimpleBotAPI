# Installation

## Prerequists
- PHP 8.0 Or Higher
- cURL PHP extension enabled

## Installing via composer
Run this command in the command line:
```sh
composer require muaath5/simple-bot-api
```

## Installing via downloading
1. Clone the repo by `git clone https://github.com/Muaath5/SimpleBotAPI.git`
2. Copy `src/` directory to your project
3. In `composer.json`, Add in `autoload` field, In `psr-4` field: `"SimpleBotAPI\\": "src/", "SimpleBotAPI\\Exceptions\\": "src/Exceptions/"`