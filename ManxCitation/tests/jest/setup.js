// SPDX-License-Identifier: GPL-2.0-only

/**
 * Setup mocks for MediaWiki environment
 */

// Mock MediaWiki API
global.mw = {
    msg: jest.fn((key) => {
        const messages = {
            'manxcitation-prompt': 'Enter Manx Documentation Database URL:',
            'manxcitation-success': 'Citation inserted successfully',
            'manxcitation-error-invalid-url': 'Invalid URL',
            'manxcitation-error-fetch': 'Failed to fetch citation'
        };
        return messages[key] || key;
    }),

    notify: jest.fn(),

    Api: jest.fn().mockImplementation(() => ({
        get: jest.fn()
    })),

    loader: {
        using: jest.fn((modules, callback) => {
            if (typeof callback === 'function') {
                callback();
            }
            return Promise.resolve();
        }),
        getState: jest.fn(() => 'ready')
    },

    config: {
        get: jest.fn((key) => {
            const config = {
                'wgAction': 'edit',
                'wgNamespaceNumber': 0
            };
            return config[key];
        })
    },

    util: {
        addPortletLink: jest.fn()
    }
};

// Mock jQuery
global.$ = jest.fn((selector) => {
    const element = {
        on: jest.fn().mockReturnThis(),
        wikiEditor: jest.fn().mockReturnThis(),
        textSelection: jest.fn().mockReturnThis(),
        text: jest.fn().mockReturnThis()
    };
    return element;
});

global.$.inArray = jest.fn((value, array) => array.indexOf(value));
