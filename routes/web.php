<?php

Route::get('/{any}', 'Syngenta\FrontendMasterPageController@route')->where('any', '^(?!admin|nova-api|nova-components|nova-vendor).*$');
