## Installation

### 1. Require bundle with composer:

```bash
composer require dh/artis-package-manager-bundle
```
   
### 2. Enable the bundle in bundles.php:

```php
    //config/bundles.php
    
    return [
         // ...
    DH\ArtisPackageManagerBundle\DHArtisPackageManagerBundle::class => ['all' => true],
    ];
```

## CLI commands
- Install package with config

```bash
bin/console artis:package:install package name
```

- Remove package with config

```bash
bin/console artis:package:remove package name
```

## Config file

##### Config consists of 3 main elements:
##### 1. Trait
Trait is an array of classes and traits that we want to add to those classes.

Example trait config:

```yaml
{
    "trait": {
      "App\\Entity\\Product\\Product": {
        "add": {
          "archivableTrait": "DH\\ExamplePlugin\\Entity\\ArchivableTrait"
        }
      }
    }
}
```

The first element in the trait array is the class name with full path, the next thing is **add** array, in this array, we specify what trait we want to add to this class, in the example we have
**DH\\ExamplePlugin\\Entity\\ArchivableTrait** with identifier **archivableTrait**, this identifier can be anything, **important is  trait name with full path.**

##### 2. Config
Config is an array of config files that we want to add to _sylius.yaml, in most of the time it is one config file.

Example config:

```yaml
{
    "config": {
      "config/packages/_sylius.yaml" : {
        "add": {
          "configFile": "@DHExamplePlugin/Resources/config/config.yml"
        }
      }
    }
}
```

The first element in the config array is the path to _sylius.yaml next thing is **add** array, in this array we specify what config file we want to add to _sylius.yaml, in the example we have
**@DHExamplePlugin/Resources/config/config.yml** with identifier **configFile**, this identifier can be anything, important is the full path to the config file.

##### 3. Routing
Routing is an array of routing files that we want to add to routing.yml, most of the time, it is one routing file.

Example routing config:

```yaml
{
    "routing": {
      "config/routes/routing.yml" : {
        "add": {
          "dh_sylius_example_plugin_admin": {
            "resource": "@DHSyliusExamplePlugin/Resources/config/routing.yml"
          }
        }
      }
}
```

The first element in the routing array is the path to routing.yml, if routing.yml don't exist, will be created in this path, next thing is **add** array, in this array we specify 
what routing file we want to add to routing.yml, in the example we have route name: **dh_sylius_example_plugin_admin** with identifier **resource**, this identifier is important, next we have full
path to routing.yml of the plugin.


#### The full config should look something like this:

```yaml
{
  "install": {
    "trait": {
      "App\\Entity\\Product\\Product": {
        "add": {
          "archivableTrait": "DH\\ExamplePlugin\\Entity\\ArchivableTrait"
        }
      }
    },
    "config": {
      "config/packages/_sylius.yaml" : {
        "add": {
          "configFile": "@DHExamplePlugin/Resources/config/config.yml"
        }
      }
    },
    "routing": {
      "config/routes/routing.yml" : {
        "add": {
          "dh_sylius_example_plugin_admin": {
            "resource": "@DHSyliusExamplePlugin/Resources/config/routing.yml"
          }
        }
      }
    }
  }
}
```

## TODO
- add more options for routing like: **path, prefix, method..**
- improve code formatting for php and yaml
- unit tests
