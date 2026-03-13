<?php

namespace ManxCitation\Tests;

use ApiManxCitation;
use ApiMain;
use ApiTestCase;
use ApiUsageException;
use ReflectionMethod;

/**
 * @group API
 * @group Database
 * @group ManxCitation
 * @covers ApiManxCitation
 */
class ApiManxCitationTest extends ApiTestCase {
    
    /**
     * Test parseCitation method with complete HTML
     */
    public function testParseCitationComplete() {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<body>
    <div class="det">
        <h1>Intel 8080 Microprocessor Manual</h1>
        <table>
            <tr>
                <td>Date:</td>
                <td>1975-06</td>
            </tr>
            <tr>
                <td>Author:</td>
                <td>Intel Corporation</td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML;
        
        $api = new ApiManxCitation(new ApiMain(), 'manxcitation');
        $method = new ReflectionMethod(ApiManxCitation::class, 'parseCitation');
        $method->setAccessible(true);
        
        $result = $method->invoke($api, $html, '12345,67890');
        
        $this->assertStringContainsString(
            '{{manx details|12345,67890|Intel 8080 Microprocessor Manual}}',
            $result
        );
        $this->assertStringContainsString('June, 1975', $result);
    }
    
    /**
     * Test parseCitation with no date
     */
    public function testParseCitationNoDate() {
        $html = <<<HTML
<!DOCTYPE html>
<html>
<body>
    <div class="det">
        <h1>Test Manual</h1>
        <table>
            <tr>
                <td>Publisher:</td>
                <td>Test Corp</td>
            </tr>
        </table>
    </div>
</body>
</html>
HTML;
        
        $api = new ApiManxCitation(new ApiMain(), 'manxcitation');
        $method = new ReflectionMethod(ApiManxCitation::class, 'parseCitation');
        $method->setAccessible(true);
        
        $result = $method->invoke($api, $html, '99999,11111');
        
        $this->assertEquals(
            '{{manx details|99999,11111|Test Manual}}',
            $result
        );
    }
    
    /**
     * Test parseCitation with malformed HTML
     */
    public function testParseCitationMalformedHtml() {
        $html = '<div class="det"><h1>Broken HTML</div>';
        
        $api = new ApiManxCitation(new ApiMain(), 'manxcitation');
        $method = new ReflectionMethod(ApiManxCitation::class, 'parseCitation');
        $method->setAccessible(true);
        
        $result = $method->invoke($api, $html, '11111,22222');
        
        $this->assertStringContainsString('{{manx details|11111,22222|', $result);
    }
    
    /**
     * Test formatDate with YYYY-MM format
     */
    public function testFormatDateYearMonth() {
        $api = new ApiManxCitation(new ApiMain(), 'manxcitation');
        $method = new ReflectionMethod(ApiManxCitation::class, 'formatDate');
        $method->setAccessible(true);
        
        $testCases = [
            '1985-03' => 'March, 1985',
            '2020-12' => 'December, 2020',
            '1975-01' => 'January, 1975',
        ];
        
        foreach ($testCases as $input => $expected) {
            $result = $method->invoke($api, $input);
            $this->assertEquals($expected, $result, "Failed for input: $input");
        }
    }
    
    /**
     * Test formatDate with full date string
     */
    public function testFormatDateFullDate() {
        $api = new ApiManxCitation(new ApiMain(), 'manxcitation');
        $method = new ReflectionMethod(ApiManxCitation::class, 'formatDate');
        $method->setAccessible(true);
        
        $result = $method->invoke($api, '1985-03-15');
        
        $this->assertEquals('March, 1985', $result);
    }
    
    /**
     * Test formatDate with invalid date returns original
     */
    public function testFormatDateInvalid() {
        $api = new ApiManxCitation(new ApiMain(), 'manxcitation');
        $method = new ReflectionMethod(ApiManxCitation::class, 'formatDate');
        $method->setAccessible(true);
        
        $result = $method->invoke($api, 'not-a-date');
        
        $this->assertEquals('not-a-date', $result);
    }
    
    /**
     * Test API rejects invalid URL
     */
    public function testExecuteInvalidUrl() {
        $this->expectException(ApiUsageException::class);
        
        $this->doApiRequest([
            'action' => 'manxcitation',
            'url' => 'https://example.com/notvalid'
        ]);
    }
    
    /**
     * Test API requires URL parameter
     */
    public function testExecuteMissingUrl() {
        $this->expectException(ApiUsageException::class);
        
        $this->doApiRequest([
            'action' => 'manxcitation'
        ]);
    }
    
    /**
     * Test valid URL formats
     */
    public function testValidUrlFormats() {
        $validUrls = [
            'https://manx-docs.org/details.php/12345,67890',
            'http://manx-docs.org/details.php/12345,67890',
            'https://manx-docs.org/details.php/11111,22222',
            'http://manx-docs.org/details.php/11111,22222',
        ];
        
        foreach ($validUrls as $url) {
            $matches = [];
            $isValid = preg_match(
                '#^https?://manx-docs\.org/details\.php/([0-9]+,[0-9]+)$#',
                $url,
                $matches
            );
            
            $this->assertEquals(1, $isValid, "URL should be valid: $url");
            $this->assertArrayHasKey(1, $matches);
        }
    }
    
    /**
     * Test invalid URL formats
     */
    public function testInvalidUrlFormats() {
        $invalidUrls = [
            'https://manx-docs.org/foo/details.php/12345,67890',
            'https://manx-docs.org/details.php/12345',
            'https://manx-docs.org/details.php',
            'ftp://manx-docs.org/details.php/12345,67890',
            'https://example.com/details.php/12345,67890',
        ];
        
        foreach ($invalidUrls as $url) {
            $matches = [];
            $isValid = preg_match(
                '#^https?://manx-docs\.org/details\.php/([0-9]+,[0-9]+)$#',
                $url,
                $matches
            );
            
            $this->assertEquals(0, $isValid, "URL should be invalid: $url");
        }
    }
    
    /**
     * Test getAllowedParams returns correct structure
     */
    public function testGetAllowedParams() {
        $api = new ApiManxCitation(new ApiMain(), 'manxcitation');
        $params = $api->getAllowedParams();
        
        $this->assertArrayHasKey('url', $params);
        $this->assertTrue($params['url']['required'] ?? false);
    }
}
