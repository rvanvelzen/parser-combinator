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

$concat = function (array $params) {
    return implode('', $params);
};

$atom = (new Repeat(new OneOf([
    new CharSet(range('A', 'Z')),
    new CharSet(range('a', 'z')),
    new CharSet(range('0', '9')),
    new CharSet('!#$%&\'*+/=?^_`{|}~-'),
]), 1));
$atom->setAction($concat);

// Should actually have "quoted-string" too, but nobody uses that (nor should they)
$word = new OneOf([$atom]);

$tldSpec = new OneOf();
foreach (['com', 'org', 'net', 'nl', 'be'] as $tld) {
    $tldSpec->addParser(new String($tld));
}
$tldSpec = new Concat([new String('.'), $tldSpec, new EOS()]);
$tldSpec->setAction($concat);

$addrSpec = (new Concat([
    (new Concat([
        $word,
        (new Repeat(
            (new Concat([new String('.'), $word]))
                ->setAction($concat)
        ))->setAction($concat)
    ]))->setAction($concat),
    new String('@'),
    (new Concat([
        $atom,
        (new Repeat(
            (new Concat([
                new Lookahead(Lookahead::NEGATIVE, new Nothing(), $tldSpec),
                (new Concat([new String('.'), $atom]))
                    ->setAction($concat)
            ]))->setAction($concat),
            1,
            Repeat::INFINITE
        ))->setAction($concat),
        $tldSpec
    ]))->setAction($concat)
]))->setAction(function ($params) {
    if (count($params) !== 3) {
        throw new Exception(sprintf('Expected exactly 3 parts, got %d', count($params)));
    }

    return array(
        'local' => $params[0],
        'domain' => $params[2],
    );
});

$S = new FullParser($addrSpec);

try {
    $match = $S->parse('this.is@an.email.address.com');
    var_dump($match->getSemanticValue());
} catch (FailureException $ex) {
    echo $ex->getDisplayMessage() . "\n";
    exit(1);
}
