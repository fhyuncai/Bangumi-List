<?php

require_once('plugin-update-checker/plugin-update-checker.php');
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$myUpdateChecker = PucFactory::buildUpdateChecker(
    'https://api.yuncaioo.com/plugin-api/bangumi-list/details.json',
    __FILE__,
    'bangumi-list'
);
