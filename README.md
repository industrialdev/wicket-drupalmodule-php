# Wicket Drupal module

Uses the Industrial PHP Wicket SDK
https://github.com/industrialdev/wicket-sdk-php.git

## Installation

### repositories method

Add to your main composer.json (merging with your existing repositories array if it exists):

```json
"repositories": [
    {
      "type": "git",
      "url": "https://github.com/industrialdev/wicket-drupalmodule-php.git"
    },
    {
      "type": "git",
      "url": "https://github.com/industrialdev/wicket-sdk-php.git"
    }
]
```

Then run `composer require industrialdev/wicket-drupalmodule-php`



## Important Note

This module is common to all installs of Drupal using Wicket. There is usually, for now, a lib/wicket.php within the theme with logic specific to each client, but any code changes to this module should be able to be made to all clients that use this module.

## Initial Setup


### Drupal CAS
Install Drupal CAS module: **composer require drupal/cas**

Enable module: **drush en -y cas** or from the admin -> extend screen

In the CAS settings (**/admin/config/people/cas**) configure these options:
 - CAS Protocol version: **3.0 or higher**
 - Hostname: **[wicket-client-name]-login.staging.wicketcloud.com** OR **[wicket-client-name]-login.wicketcloud.com** for production 
 - Under "User Account Handling", "Automatically register users": **checked**
    - Email address assignment set to "**Use a CAS attribute that contains the user's complete email address.**" The value in the field should be "**email**"

Under log out behaviour, check "**Drupal logout triggers CAS logout**"


### Enable Wicket Drupal modules (the Drupal Wicket module depends on CAS being there):
---------------------------------------------
You should at least enable these 2 to start:

**drush en -y wicket**

**drush en -y wicket_cas_set_unique_identifier**




Wicket Module Configuration
------------------------
Fill out these fields here: admin/config/wicket/settings
 - API Endpoint, usually is **tps://[client-name]-api.staging.wicketcloud.com or https://[client-name]-api.wicketcloud.com for production

 - APP Key - not being used, type anything for now
 
 - JWT Secret Key - provided by wicket devs
 
 - Person ID - the admin user UUID the code can use to make admin-type calls. Otherwise the current logged in user is typically used for most operations. Provided by wicket devs
 
 - Parent ORG - Top level organization used for creating new people on the create account form. This is the "alternate name" found in Wicket under "Organizations" for the top most organization. 
 
 - Wicket Admin - The address of the admin interface. Ex: https://[client-name]-admin.staging.wicketcloud.com or https://[client-name]-admin.wicketcloud.com for production


# Available Submodules

## Base Wicket Plugin
Enable the "Wicket" plugin in the wordpress admin. This is required for any of the sub-plugins below. Enter the relevant API credentials on the provided settings form in the backend. Beyond containing the settings form, this plugin provides helper functions as well.

If needed, you can also enable the other plugins below to extend functionality.

## Wicket CAS Role Sync

Requires WP-CASSIFY plugin *AND* the "Base Wicket Plugin"

This will work on user login. Deletes existing user roles then re-adds based on what's set on the user in Wicket. If the roles don't exist in
Wordpress, they will be created on the fly

## Wicket Update Password

Requires the "Base Wicket Plugin". Provides a widget with a form to update the persons password. This is a widget in Wordpress. It's suggested to install widget context plugin to be able to restrict which pages it can go on.

## Wicket Contact Information

Requires the "Base Wicket Plugin". Provides the React widget form from Wicket admin to update person contact information. This is a widget in Wordpress. It's suggested to install widget context plugin to be able to restrict which pages it can go on.

## Wicket Create Account Form

Requires the "Base Wicket Plugin". Provides a widget with a form to create a new person. This is a widget in Wordpress. It's suggested to install widget context plugin to be able to restrict which pages it can go on. 

To create a modified version of this form, it is advisable to disable this plugin, copy the plugin file "wicket_create_account.php" outside of the wicket plugin folder, rename it and adjust the include path at the top of the file within your new copy to continue to pull in the settings form. It might be a good idea while renaming the file to also rename the functions and class within as well. This isn't stricly required but might be a good idea to visually separate the plugin as being custom/your own. 

Also, if needing to run both the core form plugin and your custom one, the functions will need to be renamed as well.

## Wicket Manage Preferences Form

Requires the "Base Wicket Plugin". Provides a widget with a form to update person preferences. This is a widget in Wordpress. It's suggested to install wicket context plugin to be able to restrict which pages it can go on. 

To create a modified version of this form, it is advisable to disable this plugin, copy the plugin file "wicket_manage_preferences.php" outside of the wicket plugin folder and rename it. It might be a good idea while renaming the file to also rename the functions and class within as well. This isn't stricly required but might be a good idea to visually separate the plugin as being custom/your own. 

Also, if needing to run both the core form plugin and your custom one, the functions will need to be renamed as well.

## CAS SETUP
Within the CAS settings, /wp/wp-admin/options-general.php?page=wp-cassify.php
