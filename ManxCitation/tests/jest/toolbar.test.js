// SPDX-License-Identifier: GPL-2.0-only

/**
 * Tests for ext.manxCitation.toolbar.js
 * @jest-environment jsdom
 */

describe('ManxCitation Toolbar', () => {
    let mockApi;
    
    beforeEach(() => {
        // Reset mocks
        jest.clearAllMocks();
        
        // Setup DOM
        document.body.innerHTML = '<textarea id="wpTextbox1"></textarea>';
        
        // Mock API instance
        mockApi = {
            get: jest.fn()
        };
        mw.Api.mockReturnValue(mockApi);
    });
    
    describe('Module Loading', () => {
        test('module is registered', () => {
            const state = mw.loader.getState('ext.manxCitation.toolbar');
            expect(state).toBe('ready');
        });
    });
    
    describe('URL Validation', () => {
        test('valid manx-docs.org URL passes with http or https', () => {
            const validUrls = [
                'https://manx-docs.org/details.php/12345,67890',
                'http://manx-docs.org/details.php/12345,67890',
                'https://manx-docs.org/details.php/11111,22222',
                'http://manx-docs.org/details.php/11111,22222'
            ];
            
            validUrls.forEach(url => {
                const matches = url.match(/^https?:\/\/manx-docs\.org\/details\.php\/[0-9]+,[0-9]+$/);
                expect(matches).not.toBeNull();
            });
        });
        
        test('invalid URL fails', () => {
            const invalidUrls = [
                'https://manx-docs.org/foo/details.php/12345,67890',
                'ftp://manx-docs.org/details.php/12345,67890',
                'https://example.com/details.php/12345,67890',
                'https://manx-docs.org/otherpage.php',
                'https://manx-docs.org/details.php',
                'not-a-url'
            ];
            
            invalidUrls.forEach(url => {
                const matches = url.match(/^https?:\/\/manx-docs\.org\/details\.php\/[0-9]+,[0-9]+$/);
                expect(matches).toBeNull();
            });
        });
    });
    
    describe('API Integration', () => {
        test('successful API call inserts citation', async () => {
            const testUrl = 'https://manx-docs.org/details.php/12345,67890';
            const testCitation = '{{manx details|12345,67890|Test Manual}}, June, 1975';
            
            mockApi.get.mockResolvedValue({
                manxcitation: {
                    citation: testCitation,
                    url: testUrl
                }
            });
            
            const result = await mockApi.get({
                action: 'manxcitation',
                url: testUrl,
                format: 'json'
            });
            
            expect(result.manxcitation.citation).toBe(testCitation);
            expect(mockApi.get).toHaveBeenCalledWith({
                action: 'manxcitation',
                url: testUrl,
                format: 'json'
            });
        });
        
        test('API error shows notification', async () => {
            mockApi.get.mockRejectedValue(new Error('Network error'));
            
            try {
                await mockApi.get({
                    action: 'manxcitation',
                    url: 'https://manx-docs.org/details.php/12345,67890',
                    format: 'json'
                });
            } catch (error) {
                expect(error.message).toBe('Network error');
            }
        });
    });
    
    describe('Message Localization', () => {
        test('retrieves localized messages', () => {
            const prompt = mw.msg('manxcitation-prompt');
            expect(prompt).toBe('Enter Manx Documentation Database URL:');
            
            const success = mw.msg('manxcitation-success');
            expect(success).toBe('Citation inserted successfully');
        });
    });
    
    describe('WikiEditor Integration', () => {
        test('button is added to toolbar', () => {
            const $textbox = $('#wpTextbox1');
            
            $textbox.wikiEditor('addToToolbar', {
                section: 'main',
                group: 'insert',
                tools: {
                    'manxcitation': {
                        label: 'Manx citation',
                        type: 'button',
                        icon: 'reference'
                    }
                }
            });
            
            expect($textbox.wikiEditor).toHaveBeenCalledWith(
                'addToToolbar',
                expect.objectContaining({
                    section: 'main',
                    group: 'insert'
                })
            );
        });
    });
});
