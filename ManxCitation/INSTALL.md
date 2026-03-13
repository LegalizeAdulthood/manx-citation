# ManxCitation MediaWiki Extension - Installation Guide

## Quick Start

1. Copy the `extensions` directory contents to your MediaWiki's `extensions/ManxCitation/` directory
2. Add one line to `LocalSettings.php`:
   ```php
   wfLoadExtension('ManxCitation');
   ```
3. Run: `php maintenance/update.php`
4. Done! The toolbar button will appear when editing pages.

## Detailed Installation Steps

### Step 1: Copy Files

Copy the contents of the `extensions` directory to your MediaWiki installation:

**Windows PowerShell:**
```powershell
# Example: Copy to local MediaWiki installation
Copy-Item -Path "D:\src\legalize\manx-citation\extensions\*" -Destination "D:\xampp\htdocs\mediawiki\extensions\ManxCitation\" -Recurse
```

**Linux/Mac:**
```bash
cp -r /path/to/manx-citation/extensions/* /path/to/mediawiki/extensions/ManxCitation/
```

### Step 2: Enable Extension

Edit your MediaWiki's `LocalSettings.php` file and add:

```php
wfLoadExtension('ManxCitation');
```

Place this line near the end of the file, after other extension loads.

### Step 3: Update Database

From your MediaWiki root directory:

```powershell
php maintenance/update.php
```

### Step 4: Verify Installation

1. Navigate to `Special:Version` on your wiki
2. Look for "ManxCitation" in the "Installed extensions" section
3. You should see:
   - Name: ManxCitation
   - Version: 1.0.0
   - Author: Richard Thomson

### Step 5: Test the Extension

1. Go to any wiki page and click "Edit"
2. Look for a new button in the toolbar (reference icon)
3. Click the button
4. Enter a test URL: `https://manx-docs.org/details.php/12345,67890`
5. The citation should be inserted into the edit box

## Troubleshooting

### Button Doesn't Appear

**Check WikiEditor is enabled:**

Add to `LocalSettings.php` if not present:
```php
wfLoadExtension('WikiEditor');
```

**Clear cache:**
```powershell
# Add to URL
?action=purge
```

### API Doesn't Work

**Test the API directly:**
```
https://your-wiki.com/api.php?action=manxcitation&url=https://manx-docs.org/details.php/12345,67890&format=json
```

**Check PHP version:**
```powershell
php -v
# Should be 7.0 or later
```

### Permission Issues

**Windows:** Ensure IIS/Apache user has read access to extension files

**Linux:** Set proper permissions:
```bash
chmod -R 755 /path/to/mediawiki/extensions/ManxCitation/
```

## Testing the Installation

### Test PHP API Module

```powershell
cd D:\path\to\mediawiki
$env:MW_INSTALL_PATH = "D:\path\to\mediawiki"
php tests/phpunit/phpunit.php extensions/ManxCitation/tests/phpunit/
```

Expected output: All tests pass

### Test JavaScript

```powershell
cd D:\path\to\mediawiki\extensions\ManxCitation
npm install
npm test
```

Expected output: All tests pass

## Uninstallation

1. Remove from `LocalSettings.php`:
   ```php
   wfLoadExtension('ManxCitation');
   ```

2. Delete the extension directory:
   ```powershell
   Remove-Item -Path "D:\path\to\mediawiki\extensions\ManxCitation" -Recurse
   ```

3. Clear cache:
   ```powershell
   php maintenance/rebuildLocalisationCache.php
   ```

## Directory Structure After Installation

```
mediawiki/
|-- extensions/
|   `-- ManxCitation/
|       |-- extension.json
|       |-- composer.json
|       |-- package.json
|       |-- includes/
|       |   |-- ApiManxCitation.php
|       |   `-- ManxCitationHooks.php
|       |-- modules/
|       |   |-- ext.manxCitation.toolbar.js
|       |   `-- ext.manxCitation.toolbar.css
|       |-- i18n/
|       |   |-- en.json
|       |   `-- qqq.json
|       `-- tests/
|           |-- phpunit/
|           `-- jest/
`-- LocalSettings.php (modified)
```

## Support

- GitHub Issues: https://github.com/LegalizeAdulthood/manx-citation/issues
- Repository: https://github.com/LegalizeAdulthood/manx-citation
