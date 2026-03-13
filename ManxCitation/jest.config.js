module.exports = {
    testEnvironment: 'jsdom',
    testMatch: ['**/tests/jest/**/*.test.js'],
    collectCoverageFrom: [
        'modules/**/*.js'
    ],
    coverageDirectory: 'coverage',
    setupFilesAfterEnv: ['<rootDir>/tests/jest/setup.js']
};
