# Google Apps Signin for websites

Currently supports Silverstripe 3.x

To install:
```
composer require "innis-maggiore/silverstripe-google-auth"
```

## YAML Configuration
```
GSuiteAuthExtension:
  client_id:
    '<YOUR ID>.apps.googleusercontent.com'

GSuiteAuthenticator:
  domain:
    '<yourdomain.com>'
  create_new_users:
    true
  default_new_user_group:
    'content-authors'
```

## Enable the Authenticator

In your site's main _config.php

```
# You can (optionally) disable the stock Authenticator
Authenticator::unregister('MemberAuthenticator');

# Enable Google Authenticator
Authenticator::register_authenticator('GSuiteAuthenticator');
```

### Note
The domain being used must be enabled in Google Developer Console for this to work. In Google
Developer Console create your project, set up new credentials. 
Add your domain to "Authorized JavaScript origins".

## Resources
[Google Sign-in documentation](https://developers.google.com/identity/sign-in/web/sign-in)