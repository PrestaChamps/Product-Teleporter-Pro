<?php
$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__ . "/build");

return PhpCsFixer\Config::create()
    ->setRules([
        '@PSR2'        => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'long'],
    ])
    ->setFinder($finder);