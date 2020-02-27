<?php

namespace KeyTools\Tests;

use KeyTools\Exception\InvalidArgumentException;
use KeyTools\Exception\InvalidKeyException;
use KeyTools\Exception\UnsupportedNotationException;
use KeyTools\KeyTools;
use PHPUnit\Framework\TestCase;

class KeyToolsTest extends TestCase
{
    /**
     * @dataProvider dataCreateSuccess
     *
     * @param array $params
     */
    public function testCreateSuccess(array $params): void
    {
        [ $keyToolsParams, $key ] = $params;

        $keyTools = new KeyTools($keyToolsParams);
        $calculatedResult = $keyTools->isValidKey($key);

        static::assertTrue($calculatedResult);
    }

    public function dataCreateSuccess(): array
    {
        return [
            [ [ [                                                   ], '1A'   ] ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY      ], '1A'   ] ],
            [ [ [ 'notation' => KeyTools::NOTATION_OPEN_KEY         ], '1D'   ] ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL          ], 'Abm'  ] ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL_ALT      ], 'G#m'  ] ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Cmin' ] ],
        ];
    }

    /**
     * @dataProvider dataCreateError
     *
     * @param array $params
     * @param string $expectedException
     */
    public function testCreateError(array $params, string $expectedException): void
    {
        $this->expectException($expectedException);

        new KeyTools($params);
    }

    public function dataCreateError(): array
    {
        return [
            [ [ 'notation' => uniqid('', true)                     ], UnsupportedNotationException::class ],
            [ [ 'notation' => KeyTools::NOTATION_DETERMINED_BY_KEY ], UnsupportedNotationException::class ],
        ];
    }

    /**
     * @dataProvider dataCalculateKeySuccess
     *
     * @param array $params
     * @param string $expectedKey
     */
    public function testCalculateKeySuccess(array $params, string $expectedKey): void
    {
        [ $key, $step, $toggleScale ] = $params;

        $keyTools = new KeyTools();
        $calculatedKey = $keyTools->calculateKey($key, $step, $toggleScale);

        static::assertSame($expectedKey, $calculatedKey);
    }

    public function dataCalculateKeySuccess(): array
    {
        return [
            [ [ '1A', 0, true ], '1B' ],
            [ [ '1B', 0, true ], '1A' ],
            [ [ '1A', 1, true ], '2B' ],
            [ [ '1B', 1, true ], '2A' ],

            [ [ '1A', 0,  false ], '1A'  ],
            [ [ '1A', 1,  false ], '2A'  ],
            [ [ '1A', 2,  false ], '3A'  ],
            [ [ '1A', 3,  false ], '4A'  ],
            [ [ '1A', 4,  false ], '5A'  ],
            [ [ '1A', 5,  false ], '6A'  ],
            [ [ '1A', 6,  false ], '7A'  ],
            [ [ '1A', 7,  false ], '8A'  ],
            [ [ '1A', 8,  false ], '9A'  ],
            [ [ '1A', 9,  false ], '10A' ],
            [ [ '1A', 10, false ], '11A' ],
            [ [ '1A', 11, false ], '12A' ],
            [ [ '1A', 12, false ], '1A'  ],

            [ [ '1A', -1,  false ], '12A' ],
            [ [ '1A', -2,  false ], '11A' ],
            [ [ '1A', -3,  false ], '10A' ],
            [ [ '1A', -4,  false ], '9A'  ],
            [ [ '1A', -5,  false ], '8A'  ],
            [ [ '1A', -6,  false ], '7A'  ],
            [ [ '1A', -7,  false ], '6A'  ],
            [ [ '1A', -8,  false ], '5A'  ],
            [ [ '1A', -9,  false ], '4A'  ],
            [ [ '1A', -10, false ], '3A'  ],
            [ [ '1A', -11, false ], '2A'  ],
            [ [ '1A', -12, false ], '1A'  ],
        ];
    }

    /**
     * @dataProvider dataCalculateKeyError
     *
     * @param array $params
     * @param string $expectedException
     */
    public function testCalculateKeyError(array $params, string $expectedException): void
    {
        $this->expectException($expectedException);

        [ $keyToolsParams, $key, $step, $toggleScale ] = $params;

        $keyTools = new KeyTools($keyToolsParams);
        $keyTools->calculateKey($key, $step, $toggleScale);
    }

    public function dataCalculateKeyError(): array
    {
        return [
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], 'XA', 1,   false ], InvalidKeyException::class      ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '0A', 1,   false ], InvalidKeyException::class      ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '1A', 20,  false ], InvalidArgumentException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '1A', -20, false ], InvalidArgumentException::class ],
        ];
    }

    /**
     * @dataProvider dataConvertKeyToNotationSuccess
     *
     * @param array $params
     * @param string $expectedKey
     */
    public function testConvertKeyToNotationSuccess(array $params, string $expectedKey): void
    {
        [ $key, $notation ] = $params;

        $keyTools = new KeyTools();
        $convertedKey = $keyTools->convertKeyToNotation($key, $notation);

        static::assertSame($expectedKey, $convertedKey);
    }

    public function dataConvertKeyToNotationSuccess(): array
    {
        return [
            [ [ '1A',  KeyTools::NOTATION_OPEN_KEY ], '6M'  ],
            [ [ '1B',  KeyTools::NOTATION_OPEN_KEY ], '6D'  ],
            [ [ '2A',  KeyTools::NOTATION_OPEN_KEY ], '7M'  ],
            [ [ '2B',  KeyTools::NOTATION_OPEN_KEY ], '7D'  ],
            [ [ '3A',  KeyTools::NOTATION_OPEN_KEY ], '8M'  ],
            [ [ '3B',  KeyTools::NOTATION_OPEN_KEY ], '8D'  ],
            [ [ '4A',  KeyTools::NOTATION_OPEN_KEY ], '9M'  ],
            [ [ '4B',  KeyTools::NOTATION_OPEN_KEY ], '9D'  ],
            [ [ '5A',  KeyTools::NOTATION_OPEN_KEY ], '10M' ],
            [ [ '5B',  KeyTools::NOTATION_OPEN_KEY ], '10D' ],
            [ [ '6A',  KeyTools::NOTATION_OPEN_KEY ], '11M' ],
            [ [ '6B',  KeyTools::NOTATION_OPEN_KEY ], '11D' ],
            [ [ '7A',  KeyTools::NOTATION_OPEN_KEY ], '12M' ],
            [ [ '7B',  KeyTools::NOTATION_OPEN_KEY ], '12D' ],
            [ [ '8A',  KeyTools::NOTATION_OPEN_KEY ], '1M'  ],
            [ [ '8B',  KeyTools::NOTATION_OPEN_KEY ], '1D'  ],
            [ [ '9A',  KeyTools::NOTATION_OPEN_KEY ], '2M'  ],
            [ [ '9B',  KeyTools::NOTATION_OPEN_KEY ], '2D'  ],
            [ [ '10A', KeyTools::NOTATION_OPEN_KEY ], '3M'  ],
            [ [ '10B', KeyTools::NOTATION_OPEN_KEY ], '3D'  ],
            [ [ '11A', KeyTools::NOTATION_OPEN_KEY ], '4M'  ],
            [ [ '11B', KeyTools::NOTATION_OPEN_KEY ], '4D'  ],
            [ [ '12A', KeyTools::NOTATION_OPEN_KEY ], '5M'  ],
            [ [ '12B', KeyTools::NOTATION_OPEN_KEY ], '5D'  ],

            [ [ '1A',  KeyTools::NOTATION_MUSICAL ], 'Abm' ],
            [ [ '1B',  KeyTools::NOTATION_MUSICAL ], 'B'   ],
            [ [ '2A',  KeyTools::NOTATION_MUSICAL ], 'Ebm' ],
            [ [ '2B',  KeyTools::NOTATION_MUSICAL ], 'Gb'  ],
            [ [ '3A',  KeyTools::NOTATION_MUSICAL ], 'Bbm' ],
            [ [ '3B',  KeyTools::NOTATION_MUSICAL ], 'Db'  ],
            [ [ '4A',  KeyTools::NOTATION_MUSICAL ], 'Fm'  ],
            [ [ '4B',  KeyTools::NOTATION_MUSICAL ], 'Ab'  ],
            [ [ '5A',  KeyTools::NOTATION_MUSICAL ], 'Cm'  ],
            [ [ '5B',  KeyTools::NOTATION_MUSICAL ], 'Eb'  ],
            [ [ '6A',  KeyTools::NOTATION_MUSICAL ], 'Gm'  ],
            [ [ '6B',  KeyTools::NOTATION_MUSICAL ], 'Bb'  ],
            [ [ '7A',  KeyTools::NOTATION_MUSICAL ], 'Dm'  ],
            [ [ '7B',  KeyTools::NOTATION_MUSICAL ], 'F'   ],
            [ [ '8A',  KeyTools::NOTATION_MUSICAL ], 'Am'  ],
            [ [ '8B',  KeyTools::NOTATION_MUSICAL ], 'C'   ],
            [ [ '9A',  KeyTools::NOTATION_MUSICAL ], 'Em'  ],
            [ [ '9B',  KeyTools::NOTATION_MUSICAL ], 'G'   ],
            [ [ '10A', KeyTools::NOTATION_MUSICAL ], 'Bm'  ],
            [ [ '10B', KeyTools::NOTATION_MUSICAL ], 'D'   ],
            [ [ '11A', KeyTools::NOTATION_MUSICAL ], 'Gbm' ],
            [ [ '11B', KeyTools::NOTATION_MUSICAL ], 'A'   ],
            [ [ '12A', KeyTools::NOTATION_MUSICAL ], 'Dbm' ],
            [ [ '12B', KeyTools::NOTATION_MUSICAL ], 'E'   ],

            [ [ '1A',  KeyTools::NOTATION_MUSICAL_ALT ], 'G#m' ],
            [ [ '1B',  KeyTools::NOTATION_MUSICAL_ALT ], 'B'   ],
            [ [ '2A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Ebm' ],
            [ [ '2B',  KeyTools::NOTATION_MUSICAL_ALT ], 'F#'  ],
            [ [ '3A',  KeyTools::NOTATION_MUSICAL_ALT ], 'A#m' ],
            [ [ '3B',  KeyTools::NOTATION_MUSICAL_ALT ], 'Db'  ],
            [ [ '4A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Fm'  ],
            [ [ '4B',  KeyTools::NOTATION_MUSICAL_ALT ], 'G#'  ],
            [ [ '5A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Cm'  ],
            [ [ '5B',  KeyTools::NOTATION_MUSICAL_ALT ], 'D#'  ],
            [ [ '6A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Gm'  ],
            [ [ '6B',  KeyTools::NOTATION_MUSICAL_ALT ], 'Bb'  ],
            [ [ '7A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Dm'  ],
            [ [ '7B',  KeyTools::NOTATION_MUSICAL_ALT ], 'F'   ],
            [ [ '8A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Am'  ],
            [ [ '8B',  KeyTools::NOTATION_MUSICAL_ALT ], 'C'   ],
            [ [ '9A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Em'  ],
            [ [ '9B',  KeyTools::NOTATION_MUSICAL_ALT ], 'G'   ],
            [ [ '10A', KeyTools::NOTATION_MUSICAL_ALT ], 'Bm'  ],
            [ [ '10B', KeyTools::NOTATION_MUSICAL_ALT ], 'D'   ],
            [ [ '11A', KeyTools::NOTATION_MUSICAL_ALT ], 'F#m' ],
            [ [ '11B', KeyTools::NOTATION_MUSICAL_ALT ], 'A'   ],
            [ [ '12A', KeyTools::NOTATION_MUSICAL_ALT ], 'C#m' ],
            [ [ '12B', KeyTools::NOTATION_MUSICAL_ALT ], 'E'   ],

            [ [ '1A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'G#m'  ],
            [ [ '1B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bmaj' ],
            [ [ '2A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Ebm'  ],
            [ [ '2B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Gb'   ],
            [ [ '3A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bbm'  ],
            [ [ '3B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Db'   ],
            [ [ '4A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Fmin' ],
            [ [ '4B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Ab'   ],
            [ [ '5A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Cmin' ],
            [ [ '5B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Eb'   ],
            [ [ '6A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Gmin' ],
            [ [ '6B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bb'   ],
            [ [ '7A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Dmin' ],
            [ [ '7B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Fmaj' ],
            [ [ '8A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Amin' ],
            [ [ '8B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Cmaj' ],
            [ [ '9A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Emin' ],
            [ [ '9B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Gmaj' ],
            [ [ '10A', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bmin' ],
            [ [ '10B', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Dmaj' ],
            [ [ '11A', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'F#m'  ],
            [ [ '11B', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Amaj' ],
            [ [ '12A', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'C#m'  ],
            [ [ '12B', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Emaj' ],

            [ [ '1A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Ab minor' ],
            [ [ '1B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'B major'  ],
            [ [ '2A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Eb minor' ],
            [ [ '2B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F# major' ],
            [ [ '3A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Bb minor' ],
            [ [ '3B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C# major' ],
            [ [ '4A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F minor'  ],
            [ [ '4B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Ab major' ],
            [ [ '5A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C minor'  ],
            [ [ '5B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Eb major' ],
            [ [ '6A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'G minor'  ],
            [ [ '6B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Bb major' ],
            [ [ '7A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'D minor'  ],
            [ [ '7B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F major'  ],
            [ [ '8A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'A minor'  ],
            [ [ '8B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C major'  ],
            [ [ '9A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'E minor'  ],
            [ [ '9B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'G major'  ],
            [ [ '10A', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'B minor'  ],
            [ [ '10B', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'D major'  ],
            [ [ '11A', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F# minor' ],
            [ [ '11B', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'A major'  ],
            [ [ '12A', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C# minor' ],
            [ [ '12B', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'E major'  ],
        ];
    }

    /**
     * @dataProvider dataConvertKeyToNotationError
     *
     * @param array $params
     * @param string $expectedException
     */
    public function testConvertKeyToNotationError(array $params, string $expectedException): void
    {
        $this->expectException($expectedException);

        [ $keyToolsParams, $key, $notation ] = $params;

        $keyTools = new KeyTools($keyToolsParams);
        $keyTools->convertKeyToNotation($key, $notation);
    }

    public function dataConvertKeyToNotationError(): array
    {
        $keyToolsParams = [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ];

        return [
            [ [ $keyToolsParams, 'AA', KeyTools::NOTATION_OPEN_KEY         ], InvalidKeyException::class          ],
            [ [ $keyToolsParams, '1D', KeyTools::NOTATION_CAMELOT_KEY      ], InvalidKeyException::class          ],
            [ [ $keyToolsParams, '1C', KeyTools::NOTATION_MUSICAL_ALT      ], InvalidKeyException::class          ],
            [ [ $keyToolsParams, '1C', KeyTools::NOTATION_MUSICAL_BEATPORT ], InvalidKeyException::class          ],
            [ [ $keyToolsParams, '1A', uniqid('', true)                    ], UnsupportedNotationException::class ],
        ];
    }

    /**
     * @dataProvider dataKeyScaleSuccess
     *
     * @param array $params
     * @param bool $expectedResult
     */
    public function testKeyScaleSuccess(array $params, bool $expectedResult): void
    {
        [ $method, $key ] = $params;

        $keyTools = new KeyTools();
        $calculatedResult = $keyTools->{$method}($key);

        static::assertSame($expectedResult, $calculatedResult);
    }

    public function dataKeyScaleSuccess(): array
    {
        return [
            [ [ 'isMajorKey', '1B' ], true ],
            [ [ 'isMajorKey', '6B' ], true ],
            [ [ 'isMinorKey', '1A' ], true ],
            [ [ 'isMinorKey', '6A' ], true ],

            [ [ 'isMajorKey', '1A' ], false ],
            [ [ 'isMajorKey', '6A' ], false ],
            [ [ 'isMinorKey', '1B' ], false ],
            [ [ 'isMinorKey', '6B' ], false ],
        ];
    }

    /**
     * @dataProvider dataKeyScaleError
     *
     * @param array $params
     * @param string $expectedException
     */
    public function testKeyScaleError(array $params, string $expectedException): void
    {
        $this->expectException($expectedException);

        [ $keyToolsParams, $method, $key ] = $params;

        $keyTools = new KeyTools($keyToolsParams);
        $keyTools->{$method}($key);
    }

    public function dataKeyScaleError(): array
    {
        return [
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY      ], 'isMajorKey', '1D'   ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_OPEN_KEY         ], 'isMajorKey', '1A'   ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL          ], 'isMajorKey', 'Cmin' ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL_ALT      ], 'isMajorKey', 'Abm'  ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL_BEATPORT ], 'isMajorKey', 'B'    ], InvalidKeyException::class ],

            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY      ], 'isMinorKey', '1D'   ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_OPEN_KEY         ], 'isMinorKey', '1A'   ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL          ], 'isMinorKey', 'Cmin' ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL_ALT      ], 'isMinorKey', 'Abm'  ], InvalidKeyException::class ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL_BEATPORT ], 'isMinorKey', 'B'    ], InvalidKeyException::class ],
        ];
    }

    /**
     * @dataProvider dataIsValidKey
     *
     * @param array $params
     * @param bool $expectedResult
     */
    public function testIsValidKey(array $params, bool $expectedResult): void
    {
        [ $keyToolsParams, $key ] = $params;

        $keyTools = new KeyTools($keyToolsParams);
        $calculatedResult = $keyTools->isValidKey($key);

        static::assertSame($expectedResult, $calculatedResult);
    }

    public function dataIsValidKey(): array
    {
        return [
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '1A'  ], true ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '1B'  ], true ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '01B' ], true ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '01B' ], true ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '12A' ], true ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '12B' ], true ],

            [ [ [                                              ], ''     ], false ],
            [ [ [                                              ], 1      ], false ],
            [ [ [                                              ], 0      ], false ],
            [ [ [                                              ], false, ], false ],
            [ [ [                                              ], true,  ], false ],
            [ [ [                                              ], 'ZZ'   ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], 'C#'   ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], true   ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], ''     ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_OPEN_KEY    ], '1A'   ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL     ], '12B'  ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_MUSICAL_ALT ], '12B'  ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], 'AA'   ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '1D'   ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_CAMELOT_KEY ], '1C',  ], false ],
            [ [ [ 'notation' => KeyTools::NOTATION_OPEN_KEY    ], 'Cm',  ], false ],
        ];
    }

    /**
     * @dataProvider dataShorthandMethod
     *
     * @param array $params
     * @param array $expected
     */
    public function testShorthandMethod(array $params, array $expected): void
    {
        [ $method, $key ] = $params;
        [ $expectedKey, $expectedStep, $expectedModeToggle ] = $expected;

        $keyTools = $this->getMockBuilder(KeyTools::class)
            ->onlyMethods([ 'calculateKey' ])
            ->getMock();

        $keyTools
            ->expects(static::once())
            ->method('calculateKey')
            ->with($expectedKey, $expectedStep, $expectedModeToggle)
            ->willReturn('');

        $keyTools->{$method}($key);
    }

    public function dataShorthandMethod(): array
    {
        $key = KeyTools::NOTATION_KEYS_CAMELOT_KEY[array_rand(KeyTools::NOTATION_KEYS_CAMELOT_KEY)];

        return [
            [ [ 'noChange',             $key ], [ $key, 0,   false ] ],
            [ [ 'perfectFourth',        $key ], [ $key, -1,  false ] ],
            [ [ 'perfectFifth',         $key ], [ $key, 1,   false ] ],
            [ [ 'relativeMinorToMajor', $key ], [ $key, 0,   true  ] ],
            [ [ 'minorToMajor',         $key ], [ $key, 3,   true  ] ],
            [ [ 'minorThird',           $key ], [ $key, -3,  false ] ],
            [ [ 'halfStep',             $key ], [ $key, 7,   false ] ],
            [ [ 'wholeStep',            $key ], [ $key, 2,   false ] ],
            [ [ 'dominantRelative',     '1A' ], [ '1A', 1,   true  ] ],
            [ [ 'dominantRelative',     '1B' ], [ '1B', -1,  true  ] ],
        ];
    }
}
