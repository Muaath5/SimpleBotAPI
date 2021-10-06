# Installation

## Prerequists
- cURL PHP extension enabled
### Suggested
- PHP 8.0

## Installing via composer
Run this command in the command line in your project directory:
```sh
composer require muaath5/simple-bot-api
```

## Installing via downloading
1. Clone the repo by `git clone https://github.com/Muaath5/SimpleBotAPI.git`
2. Copy `src/` directory to your project
3. In `composer.json`, Add in `autoload` field, In `psr-4` field: `"SimpleBotAPI\\": "src/", "SimpleBotAPI\\Exceptions\\": "src/Exceptions/"`

[Go to next docuemnt?](https://muaath5.github.io/SimpleBotAPI/CreatingBot)