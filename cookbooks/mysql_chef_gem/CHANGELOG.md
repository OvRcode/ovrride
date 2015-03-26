mysql_chef_gem CHANGELOG
========================

v2.0.2 (2014-12-26)
-------------------
- Updating source in Berksfile

v2.0.1 (2014-12-25)
-------------------
- Switching to include_recipe from recipe_eval

v2.0.0 (2014-12-23)
-------------------
- Reverting to using vendor packages instead of the connector tarball
- Adding support for linking against MariaDB libraries

v1.0.0 (2014-12-12)
-------------------
- Removing recipe that contained a single resource
- Removed dependency on mysql cookbook
- Switched to using the MySQL connector libraries tarball from a
  webserver rather than system development package
- Added serverspec tests
- Updated the README

v0.0.5 (2014-09-26)
-------------------
- Reverting installation of ruby dev packages

v0.0.4 (2014-09-22)
-------------------
- Fixing some bugs in the README
- Adding more development packages

v0.0.2 (2014-03-31)
-------------------
Initial Release


v0.0.1 (2014-03-28)
-------------------
- Initial release
