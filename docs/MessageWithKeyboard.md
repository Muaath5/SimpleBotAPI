# Message with keyboard
There's two type of keyboards..
- `InlineKeyboard`, Which is a keyboard sticks under the message, Useful for link buttons, polls, And submit messages.
- `ReplyKeyboardMarkup` sticks under chat input, Useful for text commands

## `InlineKeyboard`
The parameter `reply_markup` is used for keyboard. Its value should be a JSON string, So you'll need to use `json_encode()` with arrays (To be same as classes).

```php
$Keyboard = ['inline_keyboard' => [
    # The first row
    [
        [
            'text' => 'Google',
            'url' => 'https://google.com/'
        ]
    ],

    # Second row
    [
        [
            'text' => 'Bing',
            'url' => 'https://bing.com/'
        ]
    ],

    # Third row
    [
        [
            'text' => 'Duck Duck Go',
            'url' => 'https://ddg.gg/'
        ]
    ],
]];

# Sending with json_encode method
$Bot->SendMessage([
    'chat_id' => 1265170068,
    'text' => 'Some famous search engines:'
    'reply_markup' => json_encode($Keyboard)
]);
```

## ReplyKeyboardMarkup
These keyboards when one of them is pressed, A message will be sent with same text in the button.
### Sending
```php
$Keyboard = [
    'resize_keyboard' => true, // Like this it'll takes size as the button text
    'one_time_keyboard' => false, // If true, Means after clicking a button the keyboard wouldn't be shown
    'selective' => true, // This option useful in groups, This means the keyboard will be shown Only for person in reply & mentioned users
    'keyboard' => [
        # First row
        [
            # This button if sent, It'll send the user's contact
            ['text' => 'Sign in', 'request_contact' => true]
        ],

        # Second row
        [
            # If this button sent, Current user's location will be sent
            ['text' => 'Local statstics', 'request_location' => true]
        ],

        # Third row
        [
            # There are two buttons
            ['text' => 'Global statstics'],
            ['text' => 'COVID-19 Websites']
        ]
    ],
];
$Bot->SendMessage([
    'chat_id' => 1265170068,
    'text' => 'Welcome! This bot created for getting statstics about COVID-19',
    'reply_markup' => json_encode($Keyboard)
]);
```

### Deleting
If you set `one_time_keyboard=false`, Then your bot should delete it manually, Or it'll be shown all the time.

```php
$KeyboardRemove = [
    'remove_keyboard' => true
];
$Bot->SendMessage([
    'chat_id' => 1265170068,
    'text' => 'Poll finished!!',
    'reply_markup' => json_encode($KeyboardRemove)
]);
```

[Go to next document?](https://muaath5.github.io/SimpleBotAPI/CallbackQueries)
===
Anything missed, unclear, Or not working? Contact @Muaath_5!