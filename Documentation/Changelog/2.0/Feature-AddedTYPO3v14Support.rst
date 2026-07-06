..  _feature-1783314230:

================================
Feature: Added TYPO3 v14 Support
================================

Description
===========

:guilabel:`TYPO3 v14.3.*` has been added coming with following changes:

Handling and visual difference between TYPO3 v13 and v14
--------------------------------------------------------

For `TYPO3 v14` the streamlined and revamped overall localization handling is
adopted, implementing the `LocalizationHandlerInterface` and making use of the
new `Localization Handler` feature. That means, that the look and feel is
different based on the used TYPO3 version even with the same extension version.
Some examples:

*   TYPO3 v14 localization handler selection:

    ..  figure:: /Images/Editor/deeplwrite-localization-handler-v14.png
        :alt: Select `DeepL Write` localization handler in TYPO3 v14

*   TYPO3 v13 localization mode selection in `PageLayout module`:

    ..  figure:: /Images/Editor/deeplwrite-localization-mode-v13.png
        :alt: Select `DeepL Write` localization mode in TYPO3 v13

Impact
======

:guilabel:`web-vision/deepl-write` can now be installed and used in
:guilabel:`TYPO3 v14.3` instances.

Supported features are completely available for TYPO3 v13 and v14, except
that generic TYPO3 handling is used provided by these versions.
