<?php

/**
 * Tests condensing URLs.
 */

use putyourlightson\blitzcloudfront\CloudFrontPurger;
use putyourlightson\blitzhints\records\HintRecord;

beforeEach(function() {
    HintRecord::deleteAll();
});

test('URLs are condensed into a single URL with a wildcard character after the longest common prefix', function() {
    $purger = new CloudFrontPurger();
    $urls = [
        'https://example.com/foo',
        'https://example.com/foo/bar',
        'https://example.com/foo/bar/baz',
        'https://example.com/fun',
    ];

    expect($purger->getCondensedUrls($urls))
        ->toBe(['https://example.com/f*']);
});
