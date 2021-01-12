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
Fill out these fields here: **admin/config/wicket/settings**
 - API Endpoint, usually is **https://[client-name]-api.staging.wicketcloud.com** OR **https://[client-name]-api.wicketcloud.com** for production

 - APP Key - not being used, type anything for now
 
 - JWT Secret Key - provided by wicket devs
 
 - Person ID - the admin user UUID the code can use to make admin-type calls. Otherwise the current logged in user is typically used for most operations. Provided by wicket devs
 
 - Parent ORG - Top level organization used for creating new people on the create account form. This is the "alternate name" found in Wicket under "Organizations" for the top most organization. 
 
 - Wicket Admin - The address of the admin interface. Ex: **https://[client-name]-admin.staging.wicketcloud.com** OR **https://[client-name]-admin.wicketcloud.com** for production


# Available Submodules

## Base Wicket Module
Enable the "Wicket" module in the Drupal admin. This is required for any of the sub-modules below. Enter the relevant API credentials on the provided settings form in the backend. Beyond containing the settings form, this module provides helper functions as well.

If needed, you can also enable the other submodules below to extend functionality.

## Wicket CAS Role Sync

Requires the Drupal CAS module (https://www.drupal.org/project/cas) *AND* the "Base Wicket Module"

This will work on user login. Deletes existing user roles then re-adds based on what's set on the user in Wicket. If the roles don't exist in
Drupal, they will be created on the fly then assigned to the user.

## Wicket CAS Set Unique Identifier

Requires the Drupal CAS module (https://www.drupal.org/project/cas) *AND* the "Base Wicket Module"

This attempts to solve the issue of users updating their email in Wicket and thus breaking that link in Drupal. Ultimately we want to use the Wicket PersonUuid as the CAS Username value (which seeks to update the account if it finds it upon login). The module by default only offers the option to assign the email value, not the CAS Username. So this module will on account creation as well as additional logins, update the email address to be what's in Wicket, set the CAS Username to be the Wicket Person's UUID and update the username to be the email as well (same as the email address field).

## Wicket Update Password

Requires the "Base Wicket Module". Provides a Drupal block with a form to update the persons password.

## Wicket Contact Information

Requires the "Base Wicket Module". Provides the React widget form from Wicket admin to update person contact information. This is a block in Drupal.

## Wicket Create Account Form

Requires the "Base Wicket Module". Provides a Drupal route (/create-account) with a form to create a new person. Also contains a settings page to configure some things related to this page at **/admin/config/wicket-create-account/settings**


## CAS SETUP
Within the CAS settings, /wp/wp-admin/options-general.php?page=wp-cassify.php
