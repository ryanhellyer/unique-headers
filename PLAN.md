# Unique Headers — Modernization Plan

## Task 1: Project scaffolding
- `composer.json` (PSR-4 autoload, `inpsyde/modularity` dep, dev deps, scripts)
- `unique-headers.php` (entry point matching disable-emojis pattern)
- `.gitignore`, `.gitattributes`

## Task 2: `src/AdminModule.php`
- Meta box registration, enqueueing, saving, rendering
- Legacy migration fallback
- Implements `Inpsyde\Modularity\Module\ExecutableModule`

## Task 3: `src/DisplayModule.php`
- Front-end header image filter + srcset
- Taxonomy term header fields + archive display
- Implements `Inpsyde\Modularity\Module\ExecutableModule`

## Task 4: Dev tooling configs
- `phpcs.xml.dist` (PSR-12)
- `phpstan.neon` (level 6 + `szepeviktor/phpstan-wordpress`)
- `.php-cs-fixer.dist.php` (PSR-12 + `declare_strict_types`)
- `phpunit.xml.dist`

## Task 5: Tests
- `tests/bootstrap.php` (WordPress function stubs)
- `tests/SmokeTest.php` (module ID + `run()` returns true)

## Task 6: CI + cleanup
- `.github/workflows/ci.yml` (PHP 8.2–8.5, CS/stan/test)
- Delete `index.php` and `inc/` directory
