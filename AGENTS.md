# Agent instructions

Instructions for coding agents working in this repository. `CLAUDE.md`
imports this file. Read [CONTRIBUTING.md](CONTRIBUTING.md) first: its rules
apply to you in full. This file adds what an agent needs on top of it.

## Know which line you are on

This file belongs to the branch `main`: deepl-write 2.x, TYPO3 13.4 and
14.3, PHP 8.2 to 8.5. **The branch you work on decides the rules.** For work
on `1` (1.x, TYPO3 12.4 and 13.4), switch to that branch and follow its own
`AGENTS.md` and `CONTRIBUTING.md`, never this one. A backport is written for
the target branch, it is not a copy of the change on `main`.

Check before you start:

```bash
git branch --show-current
git status -sb
```

## Rules

- **Never write to a remote** (push, pull request, issue, comment, review,
  merge) unless the maintainer asks for exactly that.
- **Never credit a tool or a model** in commits, pull requests, issues, code
  comments or documentation. No `Co-authored-by` for it, no "Generated
  with" line. The human who submits the change is its author.
- Scratch files, plans, reports and downloads go into `.agent/` (git
  ignored), never into the tracked tree and never into `/tmp`.
- Verify by running, not by recalling: the suites below, `git`, `composer`.
  Say what you ran and what you did not run.
- An issue reference (`DPL-123`, `#12`) is written only when it is known to
  exist. Do not invent one.

## Running the test harness as an agent

- **Set `CI=true`.** Without a terminal, `runTests.sh` fails with "the input
  device is not a TTY" unless `CI` is `true`:

  ```bash
  export CI=true
  Build/Scripts/runTests.sh -b docker -t 13 -s composerUpdate
  Build/Scripts/runTests.sh -b docker -t 13 -s cgl -n
  Build/Scripts/runTests.sh -b docker -t 13 -s phpstan
  Build/Scripts/runTests.sh -b docker -t 13 -s unit
  Build/Scripts/runTests.sh -b docker -t 13 -s functional
  ```

- **One TYPO3 version at a time.** `.Build/` holds the installation of the
  last `composerUpdate`. Run all suites of v13, then `composerUpdate -t 14`
  and the suites of v14. Never run two `runTests.sh` calls of this checkout
  in parallel.
- `composerUpdate` rewrites `composer.json` while it runs and restores it
  afterwards (`composer.json.orig`). Do not interrupt it, and never commit a
  `composer.json` changed by it.
- `-s cgl` changes files, `-s cgl -n` only checks. Run the check before you
  commit, the fix only on purpose.
- Pass test filters behind `--`: `-s unit -- --filter VersionTest`.
- The functional tests use a DeepL mock server. Never put a real DeepL key
  into a file, a command line or a log.
- Done means: the suites of the changed area green on **both** v13 and v14,
  `cgl -n` and `phpstan` green on both, `checkContribComposer` when
  `composer.json` or `contrib/` changed, `renderDocumentation` when
  `Documentation/` changed.

## Code you are likely to touch

- `Classes/` is shared by both TYPO3 versions. A difference between v13 and
  v14 goes into `Core13/` and `Core14/`. Never add a new TYPO3 version check
  to a class.
- The localization runs through the DataHandler command `deeplwrite`
  (`Classes/Hooks/WriteHook.php`): localize the record, then rephrase its
  translatable fields with DeepL Write. On v13 the mode comes from the
  localization wizard of deepl-base (listeners in `Core13/Event/Listener/`),
  on v14 from the TYPO3 localization handler
  `Core14/Backend/Localization/DeeplWriteLocalizationHandler.php`.
- deepl-write has its own client and configuration on the DeepL SDK. Do not
  add a dependency on `web-vision/deepltranslate-core`. The places that work
  with it when it is installed are `Classes/Configuration/Configuration.php`
  (API key fallback) and `Configuration/SiteConfiguration/Overrides/sites.php`
  (display conditions on core's `deeplTargetLanguage` and `deeplFormality`).
  The listener identifiers of deepl-base and deepltranslate-core named in
  `after:` must keep matching theirs.
- `org_heigl/hyphenator` has to stay in sync with `contrib/`, see
  CONTRIBUTING.md.

## Commits and pull requests

- One commit per pull request, following the commit rules in CONTRIBUTING.md.
  Amend and force push (`--force-with-lease`) when asked to update a pull
  request, do not add fix-up commits.
- Rebase onto `origin/main`, never merge it in.
- When a change also needs a pull request in another DeepL extension, say so,
  and name the order in which they have to be merged.
