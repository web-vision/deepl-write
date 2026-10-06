# Contributing to deepl-write 1.x

This is the branch `1`: deepl-write 1.x for TYPO3 12.4 and 13.4. It receives
bug fixes and security fixes. New features are developed on `main` (2.x),
whose `CONTRIBUTING.md` describes the extension as it is developed today.
This file describes the rules of this branch.

## Table of contents

- [Issues and security](#issues-and-security)
- [Branches](#branches)
- [Getting started](#getting-started)
- [Tests and checks](#tests-and-checks)
- [Code rules on this branch](#code-rules-on-this-branch)
- [Documentation](#documentation)
- [Commit messages](#commit-messages)
- [Pull requests](#pull-requests)

## Issues and security

- Bugs are GitHub issues, with the templates offered when you open one. Say
  that you use 1.x, and which TYPO3 version.
- **Security issues are never reported publicly.** See
  [SECURITY.md](SECURITY.md). 1.x receives security fixes until the date
  named there.
- The maintainers track their work in an internal tracker, project `DPL`.
  That is why commits refer to `DPL-123`. You do not need access to it.

## Branches

| Branch | Version | TYPO3        | PHP        | State                         |
|--------|---------|--------------|------------|-------------------------------|
| `main` | 2.x     | 13.4, 14.3   | 8.2 to 8.5 | development                   |
| `1`    | 1.x     | 12.4, 13.4   | 8.1 to 8.5 | bug fixes and security fixes  |

- A fix is made on `main` first, when `main` has the bug too, and then
  brought to this branch in a second pull request. Name its branch after the
  one of `main` with the suffix `-1` (`bugfix/my-fix-1`). It carries the same
  title as the pull request on `main`.
- A fix that only concerns 1.x is a pull request against `1` directly.
- **A backport is written for this branch.** The code of `main` uses APIs,
  PHP features and a structure that 1.x does not have. Adapt the change to
  the rules below, do not copy it.

## Getting started

You need git, bash and docker or podman. Everything else runs in containers
through `Build/Scripts/runTests.sh`, the same script the CI uses.

```bash
git clone git@github.com:web-vision/deepl-write.git
cd deepl-write
git switch 1
Build/Scripts/runTests.sh -h                        # all options and suites
Build/Scripts/runTests.sh -t 12 -s composerUpdate   # install for TYPO3 v12
Build/Scripts/runTests.sh -t 12 -s unit
```

- `-t` selects the TYPO3 version (`12`, default, or `13`). The installation
  in `.Build/` exists once: run `-s composerUpdate` with the same `-t` before
  the suites of that version, and never two versions at the same time.
- `-p` selects the PHP version (default 8.2). TYPO3 v12 is checked on PHP 8.1
  in CI: run v12 with `-p 8.1` to catch syntax 8.1 does not know.
- `-b docker` or `-b podman` selects the container binary. Without it,
  podman is used when it is installed.

## Tests and checks

The CI of this branch runs the code quality checks only: `composerUpdate`,
`lintTypoScript`, `lintPhp`, `cgl -n`, `checkTestMethodsPrefix`, `checkBom`
and `checkExceptionCodes`, for TYPO3 v12 on PHP 8.1 and v13 on PHP 8.2.
PHPStan and the test suites are disabled in its workflows. Run them locally
anyway for the code you change:

| Check                         | Command                                                  |
|-------------------------------|----------------------------------------------------------|
| Coding style (check only)     | `Build/Scripts/runTests.sh -t 12 -p 8.1 -s cgl -n`       |
| PHP lint                      | `Build/Scripts/runTests.sh -t 12 -p 8.1 -s lintPhp`      |
| TypoScript lint               | `Build/Scripts/runTests.sh -t 12 -p 8.1 -s lintTypoScript` |
| Exception codes unique        | `Build/Scripts/runTests.sh -s checkExceptionCodes`       |
| PHPStan (not in CI)           | `Build/Scripts/runTests.sh -t 12 -p 8.1 -s phpstan`      |
| Unit tests (not in CI)        | `Build/Scripts/runTests.sh -t 12 -p 8.1 -s unit`         |
| Functional tests (not in CI)  | `Build/Scripts/runTests.sh -t 12 -p 8.1 -s functional`   |

The same with `-t 13 -p 8.2` after `-s composerUpdate -t 13 -p 8.2`.

- The functional tests talk to a DeepL mock server container, not to DeepL.
- A bug fix comes with a test that fails without it, where the area has
  tests.

## Code rules on this branch

The rules of `main` apply where TYPO3 v12 and PHP 8.1 allow them. These are
the differences:

- **PHP 8.1.** No `readonly` classes, no disjunctive normal form types, no
  typed class constants, no `#[\Override]`. `readonly` promoted properties
  are fine.
- **Event listeners are registered in `Configuration/Services.yaml`** with
  the tag `event.listener`. TYPO3 v12 does not know `#[AsEventListener]`.
  Services that have to be public are declared there as well.
- **No `Core12/` or `Core13/` directories.** Where v12 and v13 differ, this
  branch checks the version at that place (`Typo3Version`). Keep such places
  few, and do not restructure the branch in a fix.
- No API of TYPO3 v14 and nothing that needs deepl-base 2.x: this branch
  uses deepl-base 1.x. The localization wizard modes come from its events
  on both TYPO3 versions.
- 1.x has no readability score, no `org_heigl/hyphenator` and no `contrib/`
  directory. Changes of `main` to those do not apply here.
- Coding style is PSR-2 with the PHP 7.4 migration rules
  (`Build/php-cs-fixer/php-cs-rules.php`). PHPStan runs on level 8 with a
  baseline per TYPO3 version in `Build/phpstan/Core12` and `Core13`. A change
  does not add to the baselines.
- Every exception gets a unique code, the Unix timestamp of the moment you
  write it. Tests use the `#[Test]` attribute.

## Documentation

The documentation in `Documentation/` describes 1.x. Change it when a fix
changes what it describes. This branch has no `Documentation/Changelog/`: a
fix needs no changelog entry.

## Commit messages

The [TYPO3 Core commit message rules](https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html),
the same as on `main`:

```
[BUGFIX] DPL-237: Correct the branch alias

Explain why the change is needed and what it does. Wrap the body at 72
characters.
```

A backport keeps the subject and the body of the commit on `main` and adapts
the body where the 1.x change differs.

## Pull requests

- One pull request carries one commit, its title the subject of the commit.
  Review changes are amended and force pushed.
- Rebase onto the current `1`. The branch is merged with "rebase and merge".
- Required: one approving review and the checks `code quality with core v12
  (8.1)` and `code quality with core v13 (8.2)`.
