<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
;

return PhpCsFixer\Config::create()
    ->setRules([
        '@Symfony' => true,
        'increment_style' => ['style' => 'post'],
        'yoda_style' => false,
    ])
    ->setFinder($finder)
    ->setUsingCache(false)
;
