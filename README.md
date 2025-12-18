# TYPO3 extension `web-vision/deepl-write`

TYPO3 extension for DeepL Write integration. Write better texts

> [!IMPORTANT]
> This extension is still in a early development phase and still
> considered unstable and releases as beta version.

|                  | URL                                                         |
|------------------|-------------------------------------------------------------|
| **Repository:**  | https://github.com/web-vision/deepl-write                   |
| **Read online:** | https://docs.typo3.org/p/web-vision/deepl-write/main/en-us/ |
| **TER:**         | https://extensions.typo3.org/extension/deepl_write/         |

## Compatibility

| Branch | Version | TYPO3     | PHP                                          |
|--------|---------|-----------|----------------------------------------------|
| main   | 1.x-dev | v12 + v13 | 8.1, 8.2, 8.3, 8.4, 8.5 (depending on TYPO3) |

## Installation

Install with your flavour:

* Extension Manager
* composer

We prefer composer installation:

```bash
composer require -W 'web-vision/deepl-write':'^1.0'
```

In case you had a repository configured from early EAP phase, the repository
can be removed:

```bash
composer config --unset repositories."deepl-write" && \
  composer update --lock
```

## Configuration

`EXT:deepl_write` can be used standalone or in cooperation with established
`EXT:deepltranslate_core` and requires that a `DeepL API KEY` needs to be
configured for both extension in their respective extension configuration.

> [!IMPORTANT]
> Be aware that based on `DeepL Write API` requirements a paid `DeepL PRO`
> api key is required for this extension, which can also be used for the
> `deepltranslate-core` or using there a free key.

## Sponsors

We appreciate very much the sponsorships of the developments and features in
the DeepL Translate Extension for TYPO3.

## Create a release (maintainers only)

Prerequisites:

* git binary
* ssh key allowed to push new branches to the repository
* GitHub command line tool `gh` installed and configured with user having permission to create pull requests.

**Prepare release locally**

> Set `RELEASE_BRANCH` to branch release should happen, for example: 'main'.
> Set `RELEASE_VERSION` to release version working on, for example: '1.0.0'.

```shell
echo '>> Prepare release pull-request' ; \
  RELEASE_BRANCH='main' ; \
  RELEASE_VERSION='1.0.0' ; \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  git checkout ${RELEASE_BRANCH} && \
  git pull --rebase && \
  git checkout -b prepare-release-${RELEASE_VERSION} && \
  composer require --dev "typo3/tailor" && \
  ./.Build/bin/tailor set-version ${RELEASE_VERSION} && \
  composer remove --dev "typo3/tailor" && \
  git add . && \
  git commit -m "[TASK] Prepare release ${RELEASE_VERSION}" && \
  git push --set-upstream origin prepare-release-${RELEASE_VERSION} && \
  gh pr create --fill-verbose --base ${RELEASE_BRANCH} --title "[TASK] Prepare release for ${RELEASE_VERSION} on ${RELEASE_BRANCH}" && \
  git checkout main && \
  git branch -D prepare-release-${RELEASE_VERSION}
```

Check pull-request and the pipeline run.

**Merge approved pull-request and push version tag**

> Set `RELEASE_PR_NUMBER` with the pull-request number of the preparation pull-request.
> Set `RELEASE_BRANCH` to branch release should happen, for example: 'main' (same as in previous step).
> Set `RELEASE_VERSION` to release version working on, for example: `1.0.0` (same as in previous step).

```shell
RELEASE_BRANCH='main' ; \
RELEASE_VERSION='1.0.0' ; \
RELEASE_PR_NUMBER='123' ; \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  gh pr checkout ${RELEASE_PR_NUMBER} && \
  gh pr merge -rd ${RELEASE_PR_NUMBER} && \
  git tag ${RELEASE_VERSION} && \
  git push --tags
```

This triggers the `on push tags` workflow (`publish.yml`) which creates the upload package,
creates the GitHub release and also uploads the release to the TYPO3 Extension Repository.
