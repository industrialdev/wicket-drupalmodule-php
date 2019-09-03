# Wicket Drupal 8 module

Uses the Industrial PHP Wicket SDK
https://github.com/industrialdev/wicket-sdk-php.git

## Installation

### repositories method

Add to your main composer.json (merging with your existing repositories array if it exists):

```json
"repositories": [
    {
      "type": "git",
      "url": "https://github.com/industrialdev/wicket-drupal8module-php.git"
    },
    {
      "type": "git",
      "url": "https://github.com/industrialdev/wicket-sdk-php.git"
    }
]
```

Then run `composer require industrialdev/wicket-drupal8module-php`

### merge-plugin method

Note that this method is deprecated.

Basically, it's this:

https://www.drupal.org/docs/develop/using-composer/managing-dependencies-for-a-custom-project#s-use-the-wikimedia-composer-merge-plugin

You would clone the repository manually, edit the main composer.json and add:

```json
"merge-plugin": {
    "include": [
        "modules/custom/wicket-drupal8module-php/composer.json"
    ],
},
```

Then do a composer update in the root

## Important Note

This module is common to all installs of Drupal 8 using Wicket. There is usually, for now, a lib/wicket.php within the theme with logic specific to each client, but any code changes to this module should be able to be made to all clients that use this module.
