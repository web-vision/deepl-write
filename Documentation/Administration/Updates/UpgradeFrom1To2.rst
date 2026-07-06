.. _upgrade1to2:

==================
Upgrade 1.x to 2.x
==================

The `2.x` version drops support for TYPO3 v12 and adds support for TYPO3 v14,
see :ref:`Breaking: Removed TYPO3 v12 support <breaking-1783314229>` and
:ref:`Feature: Added TYPO3 v14 support <feature-1783314230>`.

Apart from the changed supported TYPO3 versions the functionality stayed the
same and no greater actions are needed.

composer-mode
=============

..  code-block:: bash

    composer require -W \
       "web-vision/deepl-write":"2.0.*@dev"

classic-mode
============

#.  **Get it from the Extension Manager**:
    Switch to the module :guilabel:`System > Extensions`.
    Switch to :guilabel:`Get Extensions` and search for the extension key
    *deepl_write* and import the extension from the repository.

#.  **Get it from typo3.org**:
    You can always get current version from `TER`_ by downloading the zip
    version. Upload the file afterwards in the Extension Manager.

#.  **Get it from GitHub release**:
    TER upload archives are added to the corresponding GitHub release page,
    in case you need to download or update the extension and `GITHUB_RELEASES`_
    is down or not reachable.


..  _TER: https://extensions.typo3.org/extension/deepl_write
..  _GITHUB_RELEASES: https://github.com/web-vision/deepl-write/releases/
