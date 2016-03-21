# Http Messages

## Overview
Http Messages is a plugin for Craft CMS that creates PSR-7 request and response objects, passing them through a stack of middleware and returning a decorated response.

## Documentation
Documentation can be found in this repo's [wiki] (https://github.com/airtype/httpmessages/wiki).

## Status
This plugin is in active development and is working towards a stable release.

Currently, all commits are being applied to the master branch. Once a stable release is ready, it will be tagged in master as 0.0.0, and (semantic versioning) [http://semver.org] will be utilized thereon.

## To Do
* [x] Change Request and Response Services to factories
* [x] Request factory should be wrappers around `CHttpRequest`
* [ ] Use native Yii routing instead of FastRoute
* [ ] Execute after all other plugins have initialized
* [ ] Explore utilizing [PSR-7 Middlewares](https://github.com/oscarotero/psr7-middlewares)
