..  include:: /Includes.rst.txt

..  _configuration:

Configuration
=============

Set up DeepL API (PRO) key
--------------------------

..  attention::

    Before using the DeepL API, you need to get an API key from your `DeepL Profile`_.

Go to the :ref:`extension configuration <extensionConfiguration>`
in :guilabel:`Admin Tools > Settings > Extension Configuration`.

Open the settings for :guilabel:`deepl_write` and add your API key.

..  attention::

    Be aware that based on `DeepL Write API` requirements a paid `DeepL PRO`
    api key is required for this extension, which can also be used for the
    `deepltranslate-core` or using there a free key.

.. _sitesetup:

Set up translation language
---------------------------

..  attention::

    Be aware that the DeepL Write API only supports a subset of languages for
    now and is hardcoded in the extension due to a missing API endpoint.

#. Go to :guilabel:`Site Management > Sites` and edit your site configuration

#. Switch to tab `Languages` and open your target

#. Go to :guilabel:`DeepL Settings` and set up your `DeepL Write language`

..  _DeepL Profile: https://www.deepl.com/en/your-account/keys
