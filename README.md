# Wicket Drupal 8 module

## Installation

Basically, it's this:

https://www.drupal.org/docs/develop/using-composer/managing-dependencies-for-a-custom-project

I've usually chosen to use the merge-plugin approach by editing the main composer.json and putting this in:

```json
"merge-plugin": {
    "include": [
        "modules/custom/wicket/composer.json"
    ],
},
```

Then do a composer update in the root

It uses the Industrial PHP Wicket SDK
https://github.com/industrialdev/wicket-sdk-php.git
