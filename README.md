# Package Lister


## Intro

The CLI application is based on `symfony/console` and does not use a full framework.
To fetch the packages from packagist it relies on `spatie/packagist-api`, a very thin layer on top of Guzzle.

A local copy of dataset is stored in `/tmp/package-lister.json`, with the `PL_TEMP_JSON_PATH` env var you can change this default location.
The packagist API suggests to send a `User-Agent` header to indentify the source of the requests. This header is set to "PackageLister Homework @fortrabbit" unless you change it using the `PL_USER_AGENT` env var.

## Commands

* `./bin/pluginlist generate` to query the API
* `./bin/pluginlist show` to output the table or json according to the specs

## Tests

Some pest unit tests cover the implemented behaviour. 
Clone, composer install and run the tests:

```
composer test
```

##  Install (once it is released)

```
composer global require {vendor}/{package}
```


##  Install (package not on packagist, but on github)

```
composer global config repositories.0 vcs https://github.com/{user}/{repo}
composer global config minimum-stability dev

composer global require {vendor}/{package}

composer global config minimum-stability stable
```


# Trouble shooting

If you run into composer dependency issues, try the `--W` flag:.
`composer global require {vendor}/{package} --W`

If the setup happened successfully, the `pluginlist` command should be accessible from the command line.
If not try this:
https://newbedev.com/how-do-i-place-the-composer-vendor-bin-directory-in-your-path-using-zshrc



