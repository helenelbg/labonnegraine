<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->exclude('tools')
    ->in(__DIR__)
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder($finder)
;