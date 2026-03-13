# ManxCitation Extension

Adds a toolbar button to MediaWiki's edit interface for inserting citations from the Manx Documentation Database website.

**Author:** Richard Thomson

## Installation

### From Git Repository

1. Clone or copy extension to MediaWiki extensions directory:
```powershell
cd /path/to/mediawiki/extensions
git clone https://github.com/LegalizeAdulthood/manx-citation.git ManxCitation
```

2. Add to `LocalSettings.php`:
```php
wfLoadExtension('ManxCitation');
```

3. Run database update:
```powershell
php maintenance/update.php
```

4. Navigate to Special:Version to verify installation

### Manual Installation

1. Extract files to `extensions/ManxCitation/` directory
2. Follow steps 2-4 above

## Usage

1. Edit any wiki page
2. Click the "Manx citation" button in the toolbar (reference icon)
3. Enter a Manx Documentation Database URL (e.g., https://manx-docs.org/details.php/12345,67890)
4. The formatted citation will be inserted at your cursor position

## Example

Input URL:
```
https://manx-docs.org/details.php/12345,67890
```

Generated citation:
```
{{manx details|12345,67890|Intel 8080 Manual}}, June, 1975
```

## Requirements

- MediaWiki 1.31.0 or later
- WikiEditor extension (usually enabled by default)
- PHP 7.0 or later

## Development

### Running Tests

**PHP Tests:**
```powershell
# From MediaWiki root
$env:MW_INSTALL_PATH = "D:\path\to\mediawiki"
php tests/phpunit/phpunit.php extensions/ManxCitation/tests/phpunit/
```

**JavaScript Tests:**
```powershell
# From extension directory
npm install
npm test

# Watch mode
npm run test:watch

# Coverage report
npm run test:coverage
```

### Code Style

```powershell
# Install dependencies
composer install

# Check code style
composer test

# Fix code style
composer fix
```

## Directory Structure

```
ManxCitation/
|-- extension.json          # Extension manifest
|-- composer.json           # PHP dependencies
|-- package.json            # JavaScript dependencies
|-- jest.config.js          # Jest test configuration
|-- phpunit.xml.dist        # PHPUnit configuration
|-- README.md               # This file
|-- LICENSE                 # MIT License
|-- includes/               # PHP code
|   |-- ApiManxCitation.php
|   `-- ManxCitationHooks.php
|-- modules/                # JavaScript/CSS
|   |-- ext.manxCitation.toolbar.js
|   `-- ext.manxCitation.toolbar.css
|-- i18n/                   # Internationalization
|   |-- en.json
|   `-- qqq.json
`-- tests/                  # Tests
    |-- phpunit/
    |   |-- bootstrap.php
    |   `-- ApiManxCitationTest.php
    `-- jest/
        |-- setup.js
        |-- toolbar.test.js
        `-- api-response.test.js
```

## API

The extension adds a new MediaWiki API module:

**Endpoint:** `api.php?action=manxcitation`

**Parameters:**
- `url` (required): Manx Documentation Database URL

**Example:**
```
api.php?action=manxcitation&url=https://manx-docs.org/details.php/12345,67890&format=json
```

**Response:**
```json
{
  "manxcitation": {
    "citation": "{{manx details|12345,67890|Intel 8080 Manual}}, June, 1975",
    "url": "https://manx-docs.org/details.php/12345,67890"
  }
}
```

## License

MIT License - See LICENSE file

## Links

- Repository: https://github.com/LegalizeAdulthood/manx-citation
- Issues: https://github.com/LegalizeAdulthood/manx-citation/issues
- Manx Documentation Database: https://manx-docs.org/
