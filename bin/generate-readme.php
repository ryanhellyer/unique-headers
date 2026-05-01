<?php

declare(strict_types=1);

use WPReadme2Markdown\Converter;

require_once __DIR__ . '/../vendor/autoload.php';

$readme = file_get_contents(__DIR__ . '/../readme.txt');
if ($readme === false) {
    fwrite(STDERR, "Error: could not read readme.txt\n");
    exit(1);
}

$markdown = Converter::convert($readme);

// Remove metadata label lines left by the converter
$markdown = preg_replace('/^\*\*[^*]+:\*\* .*$/m', '', $markdown);

// Remove Upgrade Notice section
$markdown = preg_replace('/\n*## Upgrade Notice\n.*?(?=\n## |$)/s', '', $markdown);

// Remove duplicate sections from Description subsections
$markdown = preg_replace('/\n*### Architecture\n.*?(?=\n### |\n## )/s', '', $markdown);
$markdown = preg_replace('/\n*### Quality\n.*?(?=\n## )/s', '', $markdown);

// Convert indented code blocks (4 spaces) to fenced code blocks
$lines = explode("\n", $markdown);
$result = [];
$inCode = false;

foreach ($lines as $line) {
    $trimmed = ltrim($line);

    if (
        preg_match('/^(?:    |\t)(.+)$/', $line, $m)
        && !in_array(trim($line), ['', '-'])
        && !preg_match('/^\s*[\*\-\d]\.?\s/', $trimmed)
    ) {
        if (!$inCode) {
            $result[] = '```';
            $inCode = true;
        }
        $result[] = $m[1];
    } else {
        if ($inCode) {
            if ($trimmed !== '') {
                $result[] = '```';
                $inCode = false;
                $result[] = $line;
            } else {
                $result[] = '```';
                $result[] = '';
                $inCode = false;
            }
        } else {
            $result[] = $line;
        }
    }
}

if ($inCode) {
    $result[] = '```';
}

$markdown = implode("\n", $result);

// Convert version format: "1.8 (2026-04-30)" -> "1.8 - 2026-04-30"
$markdown = preg_replace('/^(### \d[\d.]+\w*) \((\d{4}(?:-\d{2}-\d{2})?)\)/m', '$1 - $2', $markdown);

// Collapse multiple blank lines
$markdown = preg_replace("/\n{3,}/", "\n\n", $markdown);

// Build the final README.md with GitHub-specific sections
$header = '# Unique Headers

[![PHP](https://img.shields.io/badge/PHP-%E2%89%A57.4-777BB4?logo=php&logoColor=white)](https://php.net)'
    . ' [![WordPress](https://img.shields.io/badge/WordPress-%E2%89%A54.3-21759B?logo=wordpress&logoColor=white)'
    . '(https://wordpress.org)'
    . ' [![PHPStan](https://img.shields.io/badge/PHPStan-level%206-brightgreen)](https://phpstan.org)'
    . ' [![PSR-12](https://img.shields.io/badge/coding%20standard-PSR--12-ff69b4)](https://www.php-fig.org/psr/psr-12/)'
    . ' [![License](https://img.shields.io/badge/license-GPL--2.0--or--later-blue)](LICENSE)

';

$architecture = '## Architecture

The plugin uses:

- **PSR-4 autoloading** - classes in `src/` are autoloaded via Composer under the `RyanHellyer\UniqueHeaders` namespace.
- **Inpsyde Modularity** - the plugin is structured as two modules implementing `ExecutableModule`,'
    . ' bootstrapped via the library\'s `Package` class.
- **TypeScript** - admin JavaScript is written in TypeScript and compiled to ES5 with esbuild.

```
.
+-- .github/workflows/ci.yml     # GitHub Actions CI
+-- assets/
|   +-- admin.css                # Admin styles
|   +-- admin.js                 # Compiled JS (from src/ts/admin.ts)
+-- bin/generate-readme.php      # README generator
+-- composer.json
+-- index.php                    # Plugin entry point, boots Modularity Package
+-- phpcs.xml.dist               # PHP_CodeSniffer configuration
+-- phpstan.neon                 # PHPStan configuration
+-- readme.txt                   # WordPress.org plugin readme
+-- src/
|   +-- AdminModule.php          # Admin meta box module
|   +-- AttachmentHelper.php     # Attachment helper service
|   +-- DisplayModule.php        # Front-end display module
|   +-- DotorgPluginReview.php   # Review notice class
|   +-- ts/
|       +-- admin.ts             # TypeScript source
+-- tests/
|   +-- SmokeTest.php            # Smoke tests
|   +-- bootstrap.php            # WordPress function stubs
+-- views/
    +-- meta-box.php             # Shared partial template for image picker
```

';

$quality = '## Quality

| Tool | Command | Purpose |
|---|---|---|
| PHP_CodeSniffer | `composer phpcs` | Sniffs for PSR-12 violations |
| PHP_CodeSniffer | `composer phpcbf` | Auto-fixes PSR-12 violations |
| PHP-CS-Fixer | `composer cs` | Dry-run style check |
| PHP-CS-Fixer | `composer cs:fix` | Auto-fixes style issues |
| PHPStan | `composer phpstan` | Static analysis at level 6 |
| PHPUnit | `composer test` | Unit tests |
| TypeScript | `npm run build` | Compile admin.ts to admin.js |
| TypeScript | `npm run typecheck` | Type-check admin.ts |

All PHP code uses `declare(strict_types=1)` and follows PSR-12.

';

$contributing = '## Contributing

1. Clone the repository
2. Run `composer install` and `npm install`
3. Make your changes in `src/` and/or `src/ts/`
4. Run the quality tooling:

   ```bash
   composer phpcs
   composer phpstan
   composer test
   npm run typecheck
   ```

5. Run `npm run build` if you changed the TypeScript
6. Run `composer generate-readme` to regenerate this file
7. Submit a pull request

';

// Insert sections in the desired order
$parts = explode("\n## ", $markdown, 2);

$body = $parts[0] . "\n\n";

$sections = [];

foreach (explode("\n## ", $parts[1] ?? '') as $section) {
    $lines = explode("\n", $section, 2);
    $title = trim($lines[0]);
    $text = $lines[1] ?? '';
    $sections[$title] = $text;
}

$ordered = ['Installation', 'Development', 'Frequently Asked Questions'];

foreach ($ordered as $name) {
    if (isset($sections[$name])) {
        $body .= "## {$name}\n{$sections[$name]}\n\n";
        unset($sections[$name]);
    }
}

$body .= $architecture;
$body .= $quality;
$body .= $contributing;

foreach ($sections as $title => $text) {
    $body .= "## {$title}\n{$text}\n\n";
}

$body = trim($body) . "\n";

file_put_contents(__DIR__ . '/../README.md', $body);
echo "README.md generated from readme.txt.\n";
