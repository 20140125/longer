<?php
/**
 * @class php-cs-fixer.php
 * @author <fl2014@gmail.com>
 * @created_time 2021-12-07 09:57:57
 * @modified_by v_llongfang
 * @modified_time 2021-12-07 09:57:57
 */

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

$rules = [
    '@PHP80Migration' => true,

    'ordered_imports' => [
        'sort_algorithm' => 'alpha',
    ],
    'class_attributes_separation' => [
        'elements' => [
            'const' => 'one',
            'method' => 'one',
            'property' => 'one',
        ],
    ],
];

$finder = Finder::create()
    ->in([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database',
        __DIR__.'/resources',
        __DIR__.'/routes',
        __DIR__.'/tests',
    ])
    ->name('*.php')
    ->notName('*.blade.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setFinder($finder)
    ->setRules($rules)
    ->setRiskyAllowed(true)
    ->setUsingCache(true);
