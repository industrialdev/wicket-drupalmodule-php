# Wicket Drupal module

Download (NOT CLONE) this repo in to the modules directory of a drupal website.

Make sure the module folder is called "wicket-drupalmodule-php"

Add this to the root composer.json file:

under require:
` "industrialdev/wicket-sdk-php": "dev-master",`

under repositories, add this:
```
{
  "type": "git",
  "url": "https://github.com/industrialdev/wicket-sdk-php.git"
}
```

then run composer update to get the wicket sdk


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

Under log out behaviour, set "Log out destination" to be ```"<front>"```


### Enable Wicket Drupal modules (the Drupal Wicket module depends on CAS being there):
You should at least enable these 2 to start:

**drush en -y wicket**

**drush en -y wicket_cas_set_unique_identifier**

**drush en -y wicket_cas_role_sync**




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

This module also ignores the standard Wicket "administrator" role. So, you should setup a "Drupal Admin" role in Wicket, assign that to someone, then log in as them. Once the role is in Drupal, give it all the permissions so it will in effect be an admin user. This is because we don't want to just assume if you're an administrator in Wicket, you should be one in the CMS. Thus, a specific role is created and assigned to the person records for who should actually also be an admin in the CMS.

## Wicket CAS Set Unique Identifier

Requires the Drupal CAS module (https://www.drupal.org/project/cas) *AND* the "Base Wicket Module"

This attempts to solve the issue of users updating their email in Wicket and thus breaking that link in Drupal. Ultimately we want to use the Wicket PersonUuid as the CAS Username value (which seeks to update the account if it finds it upon login). The module by default only offers the option to assign the email value, not the CAS Username. So this module will on account creation as well as additional logins, update the email address to be what's in Wicket, set the CAS Username to be the Wicket Person's UUID and update the username to be the email as well (same as the email address field).

## Wicket Update Password

Requires the "Base Wicket Module". Provides a Drupal block with a form to update the persons password.

## Wicket Contact Information

Requires the "Base Wicket Module". Provides the React widget form from Wicket admin to update person contact information. This is a block in Drupal.

## Wicket Create Account Form

Requires the "Base Wicket Module". Provides a Drupal route (/create-account) with a form to create a new person. Also contains a settings page to configure some things related to this page at **/admin/config/wicket-create-account/settings**

## Wicket CAS Name Sync

Requires the Drupal CAS module (https://www.drupal.org/project/cas) *AND* the "Base Wicket Module". Provides a custom event subscriber that listens for CAS CasPreLoginEvent thus allowing name syncing capability on fire of that CAS login event. It will store the $person->full_name from Wicket under the Drupal user account using the key "person_full_name" via the UserData Drupal service.



## CAS Links for theme
```
{% if logged_in %}
  <li><a href="/{% if language.id != 'en' %}{{language.id}}/{% endif %}my-profile">{{'Account Center'|t}}</a></li>
  <li><a href="/caslogout">{{'Log Out'|t}}</a></li>
{% else %}
  <li><a href="/{% if language.id != 'en' %}{{language.id}}/{% endif %}create-account">{{'Register'|t}}</a></li>
  <li class="login"><a href="/{% if language.id != 'en' %}{{language.id}}/{% endif %}cas?returnto={% if language.id != 'en' %}{{language.id}}/{% endif %}account-center">{{'Login'|t}}</a></li>
{% endif %}
```

