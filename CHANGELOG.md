# Changelog

## 4.0.1 (Shuttle - maintenance release 1)

- [FIX] output composition before response events
- [FIX] invalid base-uri setting
- [ADD] base-location setting

## 4.0.0 (Shuttle)

Stable 4.0-branch release, almost a complete rewrite. Some new features:

- REGEX router
- autoinstaller
- multiple cache manager (comodojo/cache library)
- add comodojo/foundation as base package

## 3.0.1 (Cosmonaut - maintenance release 1)

- fixed error in whole cache purging
- missing exception namespace declaration
- serialization methods fix

## 3.0.0 (Cosmonaut)

- Monolog as default log library
- (almost) compatible with fig standards
- Project name changed from "simpleDataRestDispatcher" to "dispatcher"
- Semantic versioning (2.0.0) and codenames
- Complete rewrite
- Plugin/servicebundle architecture
- Installation/update via composer/Packagist
- Event engine & routing
- Lots more...

## 2.1 (never released)
- Support for MySQL via mysqli
- DB query fetch method (ASSOC,NUM,BOTH)
- YAML output/transport
- Bugfixing

## 2.0

- Bugfixing & optimization
- GET,PUT,POST,DELETE Methods support
- HTTP status codes support
- HTTP Active Access-Control-Allow-Origin support
- HTTP Allowed/Implemented method active check
- HTTP method overriding/transformation in router
- new CURL based tests
- CORS now supports multiple domains list (comma separated)
- Support for PostgreSQL

## 1.1

- Bugfixing & optimization
- Server and client cache support in router
- Correct encoding in XML output (via constant DEFAULT_ENCODING)
- Client cache support in services

## 1.0

- Initial release
