<?php

test('[xml:save] is missing required options', function () {
    $this->artisan("xml:save")
    ->assertExitCode(0);
});

test('file [coffee-feed-valid] should be parsed and validated against DTD', function () {
    $this->artisan("xml:save --path='storage/coffee-feed-valid.xml' --validate")
    ->assertExitCode(1);
});

test('the file [coffee-feed-invalid.xml] is invalid', function () {
    $this->artisan("xml:save --path='storage/coffee-feed-invalid.xml' --validate")
    ->assertExitCode(0);
});

test('Saving to sheet, but sheet options are missing, This should fail', function () {
    $this->artisan("xml:save --path='storage/coffee-feed-invalid.xml' --validate --save")
    ->assertExitCode(0);
});
