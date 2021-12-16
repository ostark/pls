# Package Lister


## Intro

The CLI application is based on `symfony/console` and does not use a full framework.
To fetch the packages from packagist it relies on `spatie/packagist-api`, a very thin layer on top of Guzzle.

To increase UX when working with table output, a local copy of dataset is stored in `/tmp/package-lister.json`,` `with the `PLS_TEMP_JSON_PATH` env var you can change this default location.

The packagist API suggests to send a `User-Agent` header to indentify the source of the requests. This header is set to `PackageLister Homework @fortrabbit`, with the `PLS_USER_AGENT` env var you can change the default.


## Commands

* `./bin/pls generate` to query the API and generate a local dataset
* `./bin/pls show` to output the table or json according to the specs

## Tests

Some pest unit tests cover the implemented behaviour. 
Clone it, run composer install and run the tests and static analysis:

```
composer install
composer test
composer phpstan
```

# Bonus 

Global install via composer

##  Once it is released

```
composer global require ostark/pls
```


##  Package not on packagist, but on github

```
composer global config repositories.0 vcs https://github.com/ostark/pls
composer global config minimum-stability dev

composer global require ostark/pls

composer global config minimum-stability stable
```


##  Package not on packagist, but locally

```
composer global config repositories.0 path /local/path/to/{repo}
composer global config minimum-stability dev

composer global require ostark/pls

composer global config minimum-stability stable
```


## Trouble shoot global install

If you run into composer dependency issues, try the `--W` flag:.
`composer global require ostark/pls --W`

If the setup happened successfully, the `pls` command should be accessible from the command line, if not try this:
https://newbedev.com/how-do-i-place-the-composer-vendor-bin-directory-in-your-path-using-zshrc



