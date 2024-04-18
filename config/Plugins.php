<?php
namespace App\config;

class Plugins {
    const DATATABLES_CSS = <<<PRE

    <link href="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.4/datatables.min.css" rel="stylesheet">

PRE;
    const DATATABLES_JS = <<<PRE

    <script src="https://cdn.datatables.net/v/dt/jq-3.7.0/dt-2.0.4/datatables.min.js" defer></script>

PRE;

}
