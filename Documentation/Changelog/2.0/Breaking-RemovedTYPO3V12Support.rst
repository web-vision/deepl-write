..  _breaking-1783314229:

===================================
Breaking: Removed TYPO3 v12 support
===================================

Description
===========

Support for TYPO3 v12 has been removed for `2.x` based on our dual
TYPO3 core version support per major version as casual support matrix.

This includes removing code paths and configurations only required for
TYPO3 v12.

Impact
======

TYPO3 v12 or older instances cannot update to the `2.x` version and are
required to upgrade TYPO3 to be able to install the next version of the
`EXT:deepl_write` together with `EXT:deepl-base (2.x)` and related packages
when released in a compatible version.

Extension cannot be installed in that version but does not break otherwise.

Affected installations
======================

TYPO3 v12 or older instances with `EXT:deepl_write` version `1.x`.

Migration
=========

Upgrade TYPO3 to supported version for `2.x` beforehand or in the same step
with upgrading/installing `EXT:deepl_write`.
