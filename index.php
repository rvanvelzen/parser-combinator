<?php
use ES\Parser\Combinator\ConcatenationCombinator as Concat;
use ES\Parser\Combinator\OrCombinator as OneOf;
use ES\Parser\Combinator\RepeatCombinator as Repeat;
use ES\Parser\FailureException;
use ES\Parser\Parser\FullParser;
use ES\Parser\Parser\ProxyParser as Proxy;
use ES\Parser\Parser\RegexParser;
use ES\Parser\Parser\StringParser as String;

// so sneaky
spl_autoload_register(function ($class) {
    $prefix = 'ES\\Parser\\';
    if (strncasecmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $structured = str_replace('\\', DIRECTORY_SEPARATOR, substr($class, strlen($prefix)));
    $path = __DIR__ . '/src/' . $structured . '.php';

    /** @noinspection PhpIncludeInspection */
    require $path;
});

$digit = (new RegexParser('/^\d+(?:\.\d+)?/'))
    ->setAction(function ($value) {
        return (float)$value;
    });

$addOp = new OneOf([new String('+'), new String('-')]);
$mulOp = new OneOf([new String('*'), new String('/')]);
$expr = new Proxy();
$term = new Proxy();
$factor = new Proxy();

$opAction = function (array $values) {
    list ($result, $extra) = $values;
    foreach ($extra as list($op, $value)) {
        if ($op === '+') {
            $result += $value;
        } elseif ($op === '-') {
            $result -= $value;
        } elseif ($op === '*') {
            $result *= $value;
        } elseif ($op === '/') {
            $result /= $value;
        } else {
            throw new RuntimeException(sprintf('Invalid operator "%s"', $op));
        }
    }

    return $result;
};

$repeatAction = function ($value) {
    if ($value === null) {
        return [];
    } else {
        return $value;
    }
};

$expr->setParser(new Concat([
    $term,
    (new Repeat(new Concat([$addOp, $term])))
        ->setAction($repeatAction)
]));
$expr->setAction($opAction);

$term->setParser(new Concat([
    $factor,
    (new Repeat(new Concat([$mulOp, $factor])))
        ->setAction($repeatAction)
]));
$term->setAction($opAction);

$parenthesized = new Concat([new String('('), $expr, new String(')')]);
$parenthesized->setAction(function (array $params) {
    return $params[1];
});

$factor->setParser(new OneOf([
    $parenthesized,
    $digit
]));

$S = new FullParser($expr);

try {
    $match = $S->parse(preg_replace('/\s+/', '', '(1*2+3)*(4-2)'));
    print_r($match->getSemanticValue());
} catch (FailureException $ex) {
    echo $ex->getDisplayMessage() . "\n";
    exit(1);
}
