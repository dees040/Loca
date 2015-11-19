<?php

require_once 'Loca.php';

use dees040\Loca\Loca;

Loca::prepare([
    'locale' => Loca::visitorCountry()
]);

var_dump(Loca::translate('app.welcome', ['name' => 'Dees']));