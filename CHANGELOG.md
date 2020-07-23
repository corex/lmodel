# Changelog

## 2.0.0

### Added
- Added changelog.
- Added support for package development.
- Added support for ignored tables.
- Added support for showing destination of generated models (-d|--destination).
- Added support for showing content of generated models (-c|--console).
- Added support for replacing builders.

### Removed
- Removed option to specify guarded fields on commands, since it is possible to specify in config file.

### Changed
- Configuration refactored and config file is renamed to "lmodel.php".
- Package rewritten to support overriding code at almost every level.
