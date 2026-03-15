var axios = require('axios');
var cheerio = require('cheerio');
var http = require('http');
var moment = require('moment');
var util = require('util');

function starts_with(text, prefix) {
    return text.substring(0, prefix.length) === prefix;
}

function citation_text(citation) {
    var text = '{{manx details|' + citation.publication + '|' + citation.title + '}}';
    if (citation.date) {
        text += ', ' + moment(citation.date, 'YYYY-MM').format('MMMM, YYYY');
    }
    return text;
}

function citation_from_html(citation, callback) {
    var $ = cheerio.load(citation.html),
        cells = $('div.det table td'),
        found_date = false;
    citation.title = $('div.det h1').text();
    cells.each(function(i, elem) {
        if (found_date) {
            citation.date = $(this).text();
            found_date = false;
        } else if ($(this).text() === 'Date:') {
            found_date = true;
        }
    });
    citation.publication = citation.url.replace(/.*details\.php\/([0-9]+,[0-9]+)/, '$1');
    callback(null, citation_text(citation));
}

function citation_from_url(url, callback) {
    axios.get(url)
        .then(function(response) {
            citation_from_html({ url: url, html: response.data }, callback);
        })
        .catch(function(err) {
            callback(err);
        });
}

function main(args) {
    citation_from_url(args[2], function(err, text) {
        if (err) {
            throw err;
        }
        console.log(text);
    });
}

main(process.argv);
