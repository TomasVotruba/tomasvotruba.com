<?php

use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TypeParser;
use Symplify\SimplePhpDocParser\SimplePhpDocParser;

$value = <<<'DOCBLOCK'
/**
 * @param int $age
 * @return int
 */
DOCBLOCK;


require __DIR__ . '/../vendor/autoload.php';

$phpDocParser = new PhpDocParser(new TypeParser(), new ConstExprParser());
$simplePhpDocParser = new SimplePhpDocParser($phpDocParser, new Lexer());

$phpDocNode = $simplePhpDocParser->parseDocBlock($value);
dump($phpDocNode);
