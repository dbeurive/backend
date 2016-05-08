{
  "version": "__VERSION__",
  "name": "dbeurive/backend",
  "description": "This package contains the database interface",
  "license": "MIT",
  "keywords": [],
  "authors": [
    {
      "name": "Denis BEURIVE",
      "email": "denis.beurive@gmail.com"
    }
  ],
  "require": {
    "symfony/console": "*",
    "dbeurive/input": "*",
    "dbeurive/util": "*",
    "phpunit/phpunit": "5.3.*"
  },
  "repositories": [
    {
      "type": "artifact",
      "url": "__ARTIFACTS_REPO__"
    }
  ],
  "autoload": {
    "psr-4": {
      "dbeurive\\Backend\\": "src"
    }
  },
  "bin": ["src/Cli/Bin/backend"]
}
