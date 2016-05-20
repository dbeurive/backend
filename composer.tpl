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
    "dbeurive/util": ">=1.0.18",
    "phpunit/phpunit": "5.3.*"
  },
  "autoload": {
    "psr-4": {
      "dbeurive\\Backend\\": "src",
      "dbeurive\\BackendTest\\": "tests"
    }
  },
  "bin": ["src/Cli/Bin/backend"]
}
