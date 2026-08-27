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
| main   | 2.x-dev | v13 + v14 | 8.2, 8.3, 8.4, 8.5                           |
| 1      | 1.x-dev | v12 + v13 | 8.1, 8.2, 8.3, 8.4, 8.5 (depending on TYPO3) |

## Installation

Install with your flavor:

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
> Set `RELEASE_VERSION` to release version working on, for example: '5.0.0'.

```bash
echo '>> Create release based on configuration' ; \
  RELEASE_BRANCH='1' ; \
  RELEASE_VERSION='1.0.4' ; \
  DEV_VERSION='1.0.5' ; \
  echo ">> Checkout branches" && \
  git checkout main && \
  git fetch --all && \
  git pull --rebase && \
  git checkout ${RELEASE_BRANCH} && \
  git pull --rebase && \
  echo ">> Create release ${RELEASE_VERSION}" && \
  git checkout -b release-${RELEASE_VERSION} && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${RELEASE_VERSION}\"/" Build/Scripts/runTests.sh && \
  sed -i "s/^  RELEASE_VERSION.*/  RELEASE_VERSION='${RELEASE_VERSION}' ; \\\\/" README.md && \
  sed -i "s/^  DEV_VERSION.*/  DEV_VERSION='${DEV_VERSION}' ; \\\\/" README.md && \
  tailor set-version ${RELEASE_VERSION} && \
  composer config "extra"."typo3/cms"."version" "${RELEASE_VERSION}" && \
  echo "${RELEASE_VERSION}" > VERSION && \
  git add . && \
  git commit -m "[RELEASE] ${RELEASE_VERSION}" && \
  git push --set-upstream origin release-${RELEASE_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[RELEASE] ${RELEASE_VERSION}" && \
  sleep 10 && \
  gh pr checks --watch --interval 2 && \
  sleep 10 && \
  gh pr merge -rd --admin && \
  git remote prune origin && \
  git tag ${RELEASE_VERSION} && \
  git push origin ${RELEASE_VERSION} && \
  echo ">> Post-release - set dev version: ${DEV_VRESION}-dev" && \
  git checkout -b set-dev-version-${DEV_VERSION} && \
  sed -i "s/^COMPOSER_ROOT_VERSION.*/COMPOSER_ROOT_VERSION=\"${DEV_VERSION}-dev\"/" Build/Scripts/runTests.sh && \
  tailor set-version ${DEV_VERSION} && \
  composer config "extra"."typo3/cms"."version" "${DEV_VERSION}-dev" && \
  echo "${DEV_VERSION}-dev" > VERSION && \
  git add . && \
  git commit -m "[TASK] Set dev version ${DEV_VERSION}" && \
  git push --set-upstream origin set-dev-version-${DEV_VERSION} && \
  gh pr create --fill --base ${RELEASE_BRANCH} --title "[TASK] Set dev version \"${DEV_VERSION}-dev\"" && \
  sleep 10 && \
  gh pr checks --watch --interval 2 && \
  sleep 10 && \
  gh pr merge -rd --admin && \
  git remote prune origin
```

## Supported Versions

| Version | Supported          | End of Support |
|---------|--------------------|----------------|
| 2.x     | :white_check_mark: | 2029-06-30     |
| 1.x     | :white_check_mark: | 2027-12-31     |

## Security

Found a vulnerability? Please report it privately via our
[security report form](https://security.web-vision.de) — **do not** open a public issue.
See [SECURITY.md](SECURITY.md) for the full vulnerability disclosure policy,
including what to expect and our safe harbor statement.

## Simplified EU Declaration of Conformity (Annex VI)

> Hereby, web-vision GmbH declares that the product with digital elements
> type DeepL Write is in compliance with Regulation (EU) 2024/2847.
>
> The full text of the EU declaration of conformity is available at the
> following internet address:
> https://security.web-vision.de/conformity/web-vision/deepl-write/1.1.0/en/

The full declarations are also included in this repository:
[English](EU-Declaration-of-Conformity.md) ·
[Deutsch](EU-Konformitaetserklaerung.md).

## License

This extension is released under the [GPL-2.0-or-later](LICENSE) license.
