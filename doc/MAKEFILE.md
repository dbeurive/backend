# How to use the Makefile

## Generate the composer's configuration file

The command below will use the file `composer.tpl` as a template.

    make composer version=1.0.0

## Create an "artifact"

Artifacts are Composer's packages stored on your local filesystem.
Artifacts are ZIP files which names follow the following convention: `<vendor>-<name>-<version>.zip`
(for example: `dbeurive-backend-0.0.5.zip`).

To create an artifact with the version `1.0.0` 

    make artifact version=1.0.0

Note:

> Artifacts will be stored in the following directory: `../artifacts/` (relatively to the current directory).
> So make sure that this directory exists and is writable.

If you want to declare a dependency to your newly created artifact, just add the following JSON in your `composer.json`: 
 
```json
  "repositories": [
    {
      "type": "artifact",
      "url": "/path/to/your/artifacts"
    }
  ],
```

This configuration tells Composer to look in the directory `/path/to/your/artifacts` for packages (instead of asking [Packagist](https://packagist.org)).

