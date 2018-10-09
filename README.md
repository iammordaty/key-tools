# iammordaty/key-tools

KeyTools is a library that allows you to convert musical keys between notations. In addition, KeyTools allows you to calculate matching keys for harmonic mixing.

Supported notations:
* Camelot Key
* Open Key
* Musical
* Musical used by Beatport

KeyTools is based on the code written by [@mossspence](https://github.com/mossspence), which can be found [here](https://github.com/mossspence/trakofflive/blob/master/appsrc/moss/musicapp/finder/keyTools.php).

## Installation

Use [Composer](https://getcomposer.org/) to install it:

```bash
$ composer require iammordaty/key-tools
```

## Requirements

* PHP 7.1 and higher

## Usage

The following example shows how to calculate a new key.

```php
use KeyTools\KeyTools;

$keyTools = new KeyTools();

echo $keyTools->calculateKey('3A'); // "3A"
echo $keyTools->calculateKey('3A', 1); // "4A"
echo $keyTools->calculateKey('3A', 2); // "5A"
echo $keyTools->calculateKey('3A', -1); // "2A"
echo $keyTools->calculateKey('3A', 0, true); // "3B"
```

To calculate new key, you can also use shorthand methods:

```php
echo $keyTools->noChange('3A'); // "3A"
echo $keyTools->perfectFifth('3A'); // "4A"
echo $keyTools->wholeStep('3A'); // "5A"
echo $keyTools->perfectFourth('3A'); // "2A"
echo $keyTools->relativeMinorToMajor('3A'); // "3B"
```

Also, conversion of keys between notations is easy:

```php
echo $keyTools->convertKeyToNotation('Fmin', KeyTools::NOTATION_CAMELOT_KEY); // "4A"
echo $keyTools->convertKeyToNotation('Fmin', KeyTools::NOTATION_OPEN_KEY); // "9M"
echo $keyTools->convertKeyToNotation('Fmin', KeyTools::NOTATION_MUSICAL); // = "Fm"
```

KeyTools allows key and notation validation by suitable methods...

```php
$key = 'Fmin';
$notation = KeyTools::NOTATION_CAMELOT_KEY;

$keyTools = new KeyTools();

if (!$keyTools->isValidKey($key)) {
    exit('Invalid key');
}

if (!$keyTools->isSupportedNotation($notation)) {
    exit('Unsupported notation');
}

echo $keyTools->convertKeyToNotation($key, $notation); // "4A"
```

... or by throwing appropriate exceptions:


```php
use KeyTools\{
    Exception\InvalidKeyException,
    Exception\UnsupportedNotationException
};

$key = 'Fmin';
$notation = KeyTools::NOTATION_CAMELOT_KEY;

try {
    $keyTools = new KeyTools();

    echo $keyTools->convertKeyToNotation($key, $notation); // "4A"
} catch (InvalidKeyException | UnsupportedNotationException $e) {
    echo $e->getMessage();
}

```

## Tests

Use [PHPunit](https://phpunit.de) to run tests:

```bash
$ phpunit
```

## Further informations

 - [Harmonic mixing overview and how-to](http://www.harmonic-mixing.com/HowTo.aspx)
 - ["What Is Harmonic Mixing?" – tutorial by DJ Endo](http://blog.dubspot.com/harmonic-mixing-w-dj-endo-part-1/)
 - ["Digital DJing: harmonic mixing" – tutorial by Radley Marx](https://radleymarx.com/djs/digital-djing-harmonic-mixing/)
 - [Open Key notation](https://beatunes.com/en/open-key-notation.html)
 - [Camelot wheel (image)](https://forums.pioneerdj.com/hc/user_images/yBXP1v0OnnB8wIrg3_mbpw.jpeg)
 - [More possibilities for harmonic mixing (image)](https://imgur.com/KYw9IBE)

## License

iammordaty/key-tools is licensed under the MIT License.
