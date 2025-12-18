..  include:: /Includes.rst.txt

..  _installation:

Installation
============

The extension has to be installed like any other TYPO3 CMS extension.
You can download the extension using one of the following methods:

#.  **Use composer**:
    Run

    ..  code-block:: bash

        composer require -W 'web-vision/deepl-write':'^1.0'

    in your TYPO3 installation.

#.  **Get it from the Extension Manager**:
    Switch to the module :guilabel:`Admin Tools > Extensions`.
    Switch to :guilabel:`Get Extensions` and search for the extension key
    *deepltranslate_core* and import the extension from the repository.

#.  **Get it from typo3.org**:
    You can always get current version from `TER`_ by downloading the zip
    version. Upload the file afterwards in the Extension Manager.

The extension then needs to be :ref:`configured <configuration>`
in order to display translation buttons in the desired languages.

..  _TER: https://extensions.typo3.org/extension/deepl_write

Compatibility
-------------

DeepL Write supports:

..  csv-table:: Changes
    :header: "DeepL Write version","TYPO3 Version","PHP version(s)","Supported"
    :file: Files/versionSupport.csv
