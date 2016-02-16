PSLib
=====

PSLib - The SugarCRM PS Library

Copyright (C) SugarCRM Inc. All rights reserved.

## About and usage

`php console list`
```bash
Available commands:
  help      Displays help for a command
  list      Lists commands
 ps
  ps:greet  Greet someone
  ps:qrr    Run SugarCRM full Quick Repair and Rebuild
```

## Install

### Update composer.json

```json
    "require": {
        "sugarcrm/pslib": "dev-master",
    },
    "repositories" : [
        {
            "type" : "git",
            "url" : "https://github.com/svnvcristea/pslib"
        }
    ],
    "scripts": {
        "post-update-cmd": [
            "php -r \"copy('vendor/sugarcrm/pslib/console', 'console');\""
        ],
        "post-install-cmd": [
            "php -r \"copy('vendor/sugarcrm/pslib/console', 'console');\""
        ]
    }
```

and run `composer update sugarcrm/pslib`

### Add to .gitignore

```
sugarcrm/vendor/sugarcrm/pslib
sugarcrm/console
```

## It may also help:

* [sugarBash Helper](https://github.com/svnvcristea/sugarBash)
* [vagrantON](https://github.com/svnvcristea/vagrantON)
* [SugarCRM CodeSniffer](https://github.com/svnvcristea/SugarCRMCodeSniffer)