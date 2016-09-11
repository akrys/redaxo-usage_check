# Usage Check

This addon searches for pictures, modules and templates that are not used anymore.

## Installation

All files are directly included and can be moved to
`redaxo/inlcude/addons/usage_check` (using Redaxo 4.x) or
`redaxo/src/addons/usage_check` (using Redaxo 5.x)

The directory itself (`usage_check`) is not included.
Why? If you like, you can check out this repository to the right directory. This
would be impossible, if I included the `usage_check` folder.

You can also download the ZIP archive from here. The unzipped files can be moved
to the right destination depending on your Redaxo copy version.

After copying the files you'll need to install and activate this addon using
the Redaxo backend.

## Compatibility
- PHP version: __5.3.2__ or later
- tested on Redaxo versions __4.3.2__, __4.4.1__, __4.5__, __4.6.1__, __4.7__, __5.0.1__, __5.1__, __5.2__

This addon works with Redaxo 4 and Redaxo 5

##Language file notice

The folder `usage_check/lang` needs writing rights for your PHP / Apache user.

Reason: I'm using `de_de_utf8.lang` and `en_gb_utf8.lang` only.

Redaxo usually needs the language files `de_de.lang` and `en_gb.lang`.
Redaxo 4 needs these files in __ISO-8859-1__, Redaxo 5 in __UTF-8__. So I
decided to use the old `xx_yy_utf8.lang` files as these are always UTF-8.
These have to be converted according to the used Redaxo version. That's why it
needs writing rights.

##Notice on code-analyzing tools
As of version 1.0-Beta7, I'm using some code analyzing tools such as `PHPUnit`.
It seems to be the easiest way to write a `composer.json` and install these tools into the project. I didn't notice
the redaxo Autoloader. It analyzes all PHP-Files, including `vendor`-directories. So, it's possible for the page to
run into a timeout. In this case, simply you can simply delete the `vendor`-directory in this addon. If it's not
present, it wasn't me, who killed your page ;-)

The best way of testing: just do it seperatly from your redaxo installation. Basicly, these tests are ment for me. Just
to find unused code or (SQL-)Erros.
