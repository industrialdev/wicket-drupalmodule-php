# Wicket Drupal 8 module

## Installation

Basically, it's this:

https://www.drupal.org/docs/develop/using-composer/managing-dependencies-for-a-custom-project

I've usually chosen to use the merge-plugin approach by editing the main composer.json and putting this in:

```json
"merge-plugin": {
    "include": [
        "modules/custom/wicket-drupal8module-php/composer.json"
    ],
},
```

Then do a composer update in the root

It uses the Industrial PHP Wicket SDK
https://github.com/industrialdev/wicket-sdk-php.git

## Important Note

This module is common to all installs of Drupal 8 using Wicket. There is usually, for now, a lib/wicket.php within the theme with logic specific to each client, but any code changes to this module should be able to be made to all clients that use this module.
