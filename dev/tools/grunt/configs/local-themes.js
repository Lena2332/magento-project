/**
 * Magento/luma - en_US
 * grunt exec:luma && grunt less:luma
 * grunt exec:luma && grunt less:luma && grunt watch
 *
 * Olenak/luma - uk_UA
 * grunt exec:olenak_luma_uk_ua && grunt less:olenak_luma_uk_ua
 * grunt exec:olenak_luma_uk_ua && grunt less:olenak_luma_uk_ua && grunt watch:olenak_luma_uk_ua
 */
module.exports = {
    luma: {
        area: 'frontend',
        name: 'Magento/luma',
        locale: 'en_US',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    },
    olenak_luma_uk_ua: {
        area: 'frontend',
        name: 'OlenaK/Luma',
        locale: 'uk_UA',
        files: [
            'css/styles-m',
            'css/styles-l'
        ],
        dsl: 'less'
    }
};
