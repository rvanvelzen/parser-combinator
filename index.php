<?php
use ES\Parser\Combinator\ConcatenationCombinator as Concat;
use ES\Parser\Combinator\OrCombinator as OneOf;
use ES\Parser\Combinator\RepeatCombinator as Repeat;
use ES\Parser\FailureException;
use ES\Parser\Parser\CharacterSetParser as CharSet;
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

$atom = new Repeat(new OneOf([
    new CharSet(range('A', 'Z')),
    new CharSet(range('a', 'z')),
    new CharSet(range('0', '9')),
    new CharSet('!#$%&\'*+/=?^_`{|}~-'),
]), 1);

// Should actually have "quoted-string" too, but nobody uses that (nor should they)
$word = new OneOf([$atom]);

// If you want to really impress your peers, write your code like this and pretend you can read it!
$dotAtom = new Concat([$word, new Repeat(new Concat([new String('.'), $word]))]);
$domain = new Concat([$atom, new Repeat(new Concat([new String('.'), $atom]), 1)]);
$addrSpec = new Concat([$dotAtom, new String('@'), $domain]);

$S = new FullParser($addrSpec);

try {
    $match = $S->match('this.is@an.email.address.com');
    $match->getClean();
    echo $match->exportTree() . "\n";
} catch (FailureException $ex) {
    echo $ex->getDisplayMessage() . "\n";
    exit(1);
}
