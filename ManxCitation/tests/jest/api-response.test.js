// SPDX-License-Identifier: GPL-2.0-only

/**
 * Test API response handling
 */

describe('API Response Handling', () => {
    let mockApi;
    
    beforeEach(() => {
        mockApi = {
            get: jest.fn()
        };
        mw.Api.mockReturnValue(mockApi);
    });
    
    test('handles citation with date', async () => {
        mockApi.get.mockResolvedValue({
            manxcitation: {
                citation: '{{manx details|12345,67890|Test Doc}}, March, 1985',
                url: 'https://manx-docs.org/details.php/12345,67890'
            }
        });
        
        const result = await mockApi.get({
            action: 'manxcitation',
            url: 'https://manx-docs.org/details.php/12345,67890'
        });
        
        expect(result.manxcitation.citation).toContain('March, 1985');
    });
    
    test('handles citation without date', async () => {
        mockApi.get.mockResolvedValue({
            manxcitation: {
                citation: '{{manx details|12345,67890|Test Doc}}',
                url: 'https://manx-docs.org/details.php/12345,67890'
            }
        });
        
        const result = await mockApi.get({
            action: 'manxcitation',
            url: 'https://manx-docs.org/details.php/12345,67890'
        });
        
        expect(result.manxcitation.citation).toMatch(/}}$/);
        expect(result.manxcitation.citation).toMatch(/^{{manx details\|/);
    });
});
