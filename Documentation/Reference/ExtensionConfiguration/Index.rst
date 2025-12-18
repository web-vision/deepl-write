..  include:: /Includes.rst.txt

..  _extensionConfiguration:

=======================
Extension Configuration
=======================

Some general settings must be configured in the Extension Configuration.

#.  Go to :guilabel:`Admin Tools > Settings > Extension Configuration`
#.  Choose :guilabel:`deepl_write`

..  attention::

    Before using the DeepL API (PRO), you need to get an API key from your `DeepL Profile`_.

..  _deeplApiKey:

DeepL API Key
=============

..  attention::

    Be aware that based on `DeepL Write API` requirements a paid `DeepL PRO`
    api key is required for this extension, which can also be used for the
    `deepltranslate-core` or using there a free key.

..  confval:: apiKey

    :type: string

    Add your DeepL API (PRO) Key here.


..  _DeepL Free API: https://www.deepl.com/pro-checkout/account?productId=1200&yearly=false&trial=false
..  _DeepL Profile: https://www.deepl.com/en/your-account/keys
..  _DeepL Pro: https://www.deepl.com/de/pro
