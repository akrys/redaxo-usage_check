# Usage Check

This addon searches for pictures, modules and templates that are not used anymore.

With realase of Version 2.0 the support for Redaxo 4 will be dropped due to (for me, onreproducable) encoding problems
on some Redaxo 5 instances.

With realase of Version 2.1 edit links moved to a detail page. This leads to the ability to avoid `group_concat`, which
sometimes cut its result, if it gets too long.

## Installation

### Standard installation
Usually this addon can be installed through the addon installer located in the redaxo backend.

### Manual installtion
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
- PHP version: __8.1__  or later
- tested on lagetes Redaxo version at release time (to maintain more instances of redaxo is not possible anymore due
to a lack of time)
