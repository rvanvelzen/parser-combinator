<?php
use ES\Parser\Assertion\EndAssertion as EOS;
use ES\Parser\Combinator\ConcatenationCombinator as Concat;
use ES\Parser\Combinator\LookaheadCombinator as Lookahead;
use ES\Parser\Combinator\OrCombinator as OneOf;
use ES\Parser\Combinator\RepeatCombinator as Repeat;
use ES\Parser\FailureException;
use ES\Parser\Parser\CharacterSetParser as CharSet;
use ES\Parser\Parser\EmptyParser as Nothing;
use ES\Parser\Parser\FullParser;
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

    require $path;
});

$digit = (new RegexParser('/^\d+(?:\.\d+)?/'))
    ->setAction(function ($value) {
        return (float)$value;
    });

$addOp = new OneOf([new String('+'), new String('-')]);
$mulOp = new OneOf([new String('*'), new String('/')]);
$expr = new Concat();
$term = new Concat();
$factor = new OneOf();

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

$expr->addParser($term);
$expr->addParser(
    (new Repeat(new Concat([$addOp, $term])))
        ->setAction($repeatAction)
);
$expr->setAction($opAction);

$term->addParser($factor);
$term->addParser(
    (new Repeat(new Concat([$mulOp, $factor])))
        ->setAction($repeatAction)
);
$term->setAction($opAction);

$parenthesized = new Concat([new String('('), $expr, new String(')')]);
$parenthesized->setAction(function (array $params) {
    return $params[1];
});

$factor->addParser($parenthesized);
$factor->addParser($digit);

$S = new FullParser($expr);

try {
    $match = $S->parse(preg_replace('/\s+/', '', '(1*2+3)*(4-2)'));
    print_r($match->getSemanticValue());
} catch (FailureException $ex) {
    echo $ex->getDisplayMessage() . "\n";
    exit(1);
}
