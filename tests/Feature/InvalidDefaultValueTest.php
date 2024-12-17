<?php

use PodPoint\ConfigCat\Facades\ConfigCat;

test('null configured as a default value for the package will throw an exception', function () {
    config()->set('configcat.default', null);

    $this->expectException(\InvalidArgumentException::class);

    ConfigCat::get('foo');
});
