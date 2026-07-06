..  _developer-readability-calculators:

=======================
Readability Calculators
=======================

The :guilabel:`DeepL Write` overlay shows a Flesch reading-ease score for the
edited text (see :ref:`the feature description <feature-1783343929>`). The score
is computed by small, language specific *readability calculators* that can be
extended by third-party extensions.

How it works
============

Each calculator implements
``WebVision\DeeplWrite\Readability\ReadabilityCalculatorInterface`` – usually by
extending ``WebVision\DeeplWrite\Readability\Calculator\AbstractReadabilityCalculator``
– and is registered through the ``deepl.readability`` service tag. At runtime
``ReadabilityCalculatorRegistry`` selects the calculator whose language matches
the primary subtag of the editor content language, so ``en-US`` and ``en-GB``
both resolve to the English calculator. If no calculator matches, or the text
contains no countable words, no score is reported and the overlay keeps working.

The base class provides the shared text metrics – counting sentences, words
(unicode aware) and syllables (via ``org_heigl/hyphenator``) – guards against
empty input and caps the score at the maximum of 100. A concrete calculator
therefore only has to provide the language specific formula.

Supported languages
===================

The following languages ship with a documented Flesch reading-ease adaptation,
where ``ASL`` is the average sentence length (words / sentences) and ``ASW`` the
average number of syllables per word (syllables / words):

..  list-table::
    :header-rows: 1

    *   - Language
        - Formula
        - Adaptation
    *   - English (``en``)
        - ``206.835 - 1.015 * ASL - 84.6 * ASW``
        - Flesch
    *   - German (``de``)
        - ``180 - ASL - 58.5 * ASW``
        - Amstad
    *   - French (``fr``)
        - ``207 - 1.015 * ASL - 73.6 * ASW``
        - Kandel & Moles
    *   - Italian (``it``)
        - ``206 - ASL - 65 * ASW``
        - Franchina & Vacca
    *   - Portuguese (``pt``)
        - ``248.835 - 1.015 * ASL - 84.6 * ASW``
        - Martins et al.

Adding a language
=================

To support an additional language, add a class that extends
``AbstractReadabilityCalculator``, declare the language it serves (``LANGUAGE``)
and the hyphenation locale used for syllable counting (``HYPHENATION_LOCALE``,
matching a dictionary shipped by ``org_heigl/hyphenator`` such as ``de``,
``en``, ``es``, ``fr``, ``it`` or ``pt``), and implement the language specific
formula in ``calculateScore()``. Tag it with
``#[AutoconfigureTag('deepl.readability')]`` so it is picked up automatically:

..  code-block:: php

    <?php

    declare(strict_types=1);

    namespace Vendor\MyExtension\Readability\Calculator;

    use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;
    use WebVision\DeeplWrite\Readability\Calculator\AbstractReadabilityCalculator;

    #[AutoconfigureTag('deepl.readability')]
    final class FleschReadingEaseSpanish extends AbstractReadabilityCalculator
    {
        // Primary language subtag matched against the editor content language.
        protected const LANGUAGE = 'es';

        // Locale of the org_heigl/hyphenator dictionary used for syllables.
        protected const HYPHENATION_LOCALE = 'es';

        protected function calculateScore(
            float $averageSentenceLength,
            float $averageSyllablesPerWord,
        ): float {
            // Return the language specific Flesch reading-ease score; the base
            // class guards empty input and caps the result at the maximum of 100.
            return 206.84 - 1.02 * $averageSentenceLength - 60.0 * $averageSyllablesPerWord;
        }
    }

..  note::

    Spanish (``es``) is supported by DeepL Write but is intentionally not shipped
    with a calculator yet: the commonly cited Fernández-Huerta formula is based
    on "sentences per 100 words" and carries a historically mis-transcribed
    coefficient, so a reliable, well-sourced variant should be selected before
    adding it. The example above illustrates the API only and is not an endorsed
    formula.
