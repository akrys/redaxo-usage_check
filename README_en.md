# Usage Check

This addon searches for pictures, modules and templates that are not used anymore.

With realase of Version 2.0 the support for Redaxo 4 will be dropped due to (for me, onreproducable) encoding problems
on some Redaxo 5 instances.

## Installation

###Standard installation
Usually this addon can be installed through the addon installer located in the redaxo backend.

###Manual installtion
If you want to test some beta functionality, it can be necessary to install this addon manually.

Installation instructions:

All files are directly included and can be moved to `redaxo/src/addons/usage_check`

The directory itself (`usage_check`) is not included.
Why? If you like, you can check out this repository to the right directory. This
would be impossible, if I included the `usage_check` folder.

You can also download the ZIP archive from here. The unzipped files can be moved to the right destination depending on
your Redaxo copy version.

After copying the files you'll need to install and activate this addon using the Redaxo backend.

## Compatibility
- PHP version: __5.6__ or later
- tested on Redaxo versions __5.0.1__, __5.1__, __5.2__,  __5.3__,  __5.4__,  __5.5__

##Notice on code-analyzing tools
As of version 1.0-Beta7, I'm using some code analyzing tools such as `PHPUnit`.
It seems to be the easiest way to write a `composer.json` and install these tools into the project. I didn't notice the
redaxo Autoloader. It analyzes all PHP-Files, including `vendor`-directories. So, it's possible for the page to run into
a timeout. In this case, simply you can simply delete the `vendor`-directory in this addon. If it's not present, it
wasn't me, who killed your page ;-)

The best way of testing: just do it seperatly from your redaxo installation. Basicly, these tests are ment for me. Just
to find unused code or (SQL-)Erros.
