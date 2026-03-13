<?php

class ApiManxCitation extends ApiBase {
    
    public function execute() {
        $url = $this->getParameter('url');
        
        // Validate URL - details.php must be at root, allow http or https
        if (!preg_match('#^https?://manx-docs\.org/details\.php/([0-9]+,[0-9]+)$#', $url, $matches)) {
            $this->dieWithError('manxcitation-invalid-url', 'invalidurl');
        }
        
        $publication = $matches[1];
        
        // Fetch HTML from Manx Documentation Database
        $request = MWHttpRequest::factory($url, [
            'method' => 'GET',
            'timeout' => 10,
            'userAgent' => 'MediaWiki ManxCitation Extension/1.0'
        ]);
        
        $status = $request->execute();
        
        if (!$status->isOK()) {
            $this->dieWithError('manxcitation-fetch-error', 'fetcherror');
        }
        
        $html = $request->getContent();
        
        // Parse HTML and build citation
        $citation = $this->parseCitation($html, $publication);
        
        $result = [
            'citation' => $citation,
            'url' => $url
        ];
        
        $this->getResult()->addValue(null, $this->getModuleName(), $result);
    }
    
    private function parseCitation($html, $publication) {
        libxml_use_internal_errors(true);
        
        $dom = new DOMDocument();
        $dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        
        libxml_clear_errors();
        
        $xpath = new DOMXPath($dom);
        
        // Extract title from div.det h1
        $titleNodes = $xpath->query("//div[contains(@class, 'det')]//h1");
        $title = '';
        if ($titleNodes->length > 0) {
            $title = trim($titleNodes->item(0)->textContent);
        }
        
        // Extract date from table cells
        $date = '';
        $cells = $xpath->query("//div[contains(@class, 'det')]//table//td");
        $foundDate = false;
        
        foreach ($cells as $cell) {
            $cellText = trim($cell->textContent);
            if ($foundDate) {
                $date = $cellText;
                break;
            }
            if ($cellText === 'Date:') {
                $foundDate = true;
            }
        }
        
        // Build citation text
        $citationText = "{{manx details|{$publication}|{$title}}}";
        
        if ($date) {
            $formattedDate = $this->formatDate($date);
            if ($formattedDate) {
                $citationText .= ", {$formattedDate}";
            }
        }
        
        return $citationText;
    }
    
    private function formatDate($dateString) {
        // Handle YYYY-MM format
        if (preg_match('/^(\d{4})-(\d{2})$/', $dateString, $matches)) {
            $year = $matches[1];
            $month = $matches[2];
            $timestamp = strtotime("{$year}-{$month}-01");
            return date('F, Y', $timestamp);
        }
        
        // Try other date formats
        $timestamp = strtotime($dateString);
        if ($timestamp !== false) {
            return date('F, Y', $timestamp);
        }
        
        return $dateString;
    }
    
    public function getAllowedParams() {
        return [
            'url' => [
                ApiBase::PARAM_TYPE => 'string',
                ApiBase::PARAM_REQUIRED => true
            ]
        ];
    }
    
    public function isWriteMode() {
        return false;
    }
    
    public function needsToken() {
        return false;
    }
    
    protected function getExamplesMessages() {
        return [
            'action=manxcitation&url=https://manx-docs.org/details.php/12345,67890'
                => 'apihelp-manxcitation-example-1'
        ];
    }
}
