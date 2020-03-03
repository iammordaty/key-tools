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
     * @param array $keyToolsParams
     * @param array $newKeyParams
     * @param string $expectedKey
     */
    public function testCalculateKeySuccess(array $keyToolsParams, array $newKeyParams, string $expectedKey): void
    {
        [ $key, $step, $toggleScale ] = $newKeyParams;

        $keyTools = new KeyTools($keyToolsParams);
        $calculatedKey = $keyTools->calculateKey($key, $step, $toggleScale);

        static::assertSame($expectedKey, $calculatedKey);
    }

    public function dataCalculateKeySuccess(): array
    {
        $withoutLeadingZero = [ 'leading_zero' => false, 'notation' => KeyTools::NOTATION_CAMELOT_KEY ];
        $withLeadingZero = [ 'leading_zero' => true, 'notation' => KeyTools::NOTATION_CAMELOT_KEY ];

        return [
            [ $withoutLeadingZero, [ '1A', 0, true ], '1B' ],
            [ $withoutLeadingZero, [ '1B', 0, true ], '1A' ],
            [ $withoutLeadingZero, [ '1A', 1, true ], '2B' ],
            [ $withoutLeadingZero, [ '1B', 1, true ], '2A' ],

            [ $withoutLeadingZero, [ '1A', 0,  false ], '1A'  ],
            [ $withoutLeadingZero, [ '1A', 1,  false ], '2A'  ],
            [ $withoutLeadingZero, [ '1A', 2,  false ], '3A'  ],
            [ $withoutLeadingZero, [ '1A', 3,  false ], '4A'  ],
            [ $withoutLeadingZero, [ '1A', 4,  false ], '5A'  ],
            [ $withoutLeadingZero, [ '1A', 5,  false ], '6A'  ],
            [ $withoutLeadingZero, [ '1A', 6,  false ], '7A'  ],
            [ $withoutLeadingZero, [ '1A', 7,  false ], '8A'  ],
            [ $withoutLeadingZero, [ '1A', 8,  false ], '9A'  ],
            [ $withoutLeadingZero, [ '1A', 9,  false ], '10A' ],
            [ $withoutLeadingZero, [ '1A', 10, false ], '11A' ],
            [ $withoutLeadingZero, [ '1A', 11, false ], '12A' ],
            [ $withoutLeadingZero, [ '1A', 12, false ], '1A'  ],

            [ $withoutLeadingZero, [ '1A', -1,  false ], '12A' ],
            [ $withoutLeadingZero, [ '1A', -2,  false ], '11A' ],
            [ $withoutLeadingZero, [ '1A', -3,  false ], '10A' ],
            [ $withoutLeadingZero, [ '1A', -4,  false ], '9A'  ],
            [ $withoutLeadingZero, [ '1A', -5,  false ], '8A'  ],
            [ $withoutLeadingZero, [ '1A', -6,  false ], '7A'  ],
            [ $withoutLeadingZero, [ '1A', -7,  false ], '6A'  ],
            [ $withoutLeadingZero, [ '1A', -8,  false ], '5A'  ],
            [ $withoutLeadingZero, [ '1A', -9,  false ], '4A'  ],
            [ $withoutLeadingZero, [ '1A', -10, false ], '3A'  ],
            [ $withoutLeadingZero, [ '1A', -11, false ], '2A'  ],
            [ $withoutLeadingZero, [ '1A', -12, false ], '1A'  ],

            [ $withLeadingZero, [ '1A', 0, true ], '01B' ],
            [ $withLeadingZero, [ '1B', 0, true ], '01A' ],
            [ $withLeadingZero, [ '1A', 1, true ], '02B' ],
            [ $withLeadingZero, [ '1B', 1, true ], '02A' ],

            [ $withLeadingZero, [ '1A', 0,  false ],  '01A' ],
            [ $withLeadingZero, [ '1A', 1,  false ],  '02A' ],
            [ $withLeadingZero, [ '1A', 2,  false ],  '03A' ],
            [ $withLeadingZero, [ '1A', 3,  false ],  '04A' ],
            [ $withLeadingZero, [ '1A', 4,  false ],  '05A' ],
            [ $withLeadingZero, [ '1A', 5,  false ],  '06A' ],
            [ $withLeadingZero, [ '1A', 6,  false ],  '07A' ],
            [ $withLeadingZero, [ '1A', 7,  false ],  '08A' ],
            [ $withLeadingZero, [ '1A', 8,  false ],  '09A' ],
            [ $withLeadingZero, [ '1A', 9,  false ],  '10A' ],
            [ $withLeadingZero, [ '1A', 10, false ],  '11A' ],
            [ $withLeadingZero, [ '1A', 11, false ],  '12A' ],
            [ $withLeadingZero, [ '01A', 12, false ], '01A' ],

            [ $withLeadingZero, [ '1A', -1,  false ], '12A' ],
            [ $withLeadingZero, [ '1A', -2,  false ], '11A' ],
            [ $withLeadingZero, [ '1A', -3,  false ], '10A' ],
            [ $withLeadingZero, [ '1A', -4,  false ], '09A' ],
            [ $withLeadingZero, [ '1A', -5,  false ], '08A' ],
            [ $withLeadingZero, [ '1A', -6,  false ], '07A' ],
            [ $withLeadingZero, [ '1A', -7,  false ], '06A' ],
            [ $withLeadingZero, [ '1A', -8,  false ], '05A' ],
            [ $withLeadingZero, [ '1A', -9,  false ], '04A' ],
            [ $withLeadingZero, [ '1A', -10, false ], '03A' ],
            [ $withLeadingZero, [ '1A', -11, false ], '02A' ],
            [ $withLeadingZero, [ '1A', -12, false ], '01A' ],
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
     * @param array $keyToolsParams
     * @param array $newKeyParams
     * @param string $expectedKey
     */
    public function testConvertKeyToNotationSuccess(array $keyToolsParams, array $newKeyParams, string $expectedKey): void
    {
        [ $key, $notation ] = $newKeyParams;

        $keyTools = new KeyTools($keyToolsParams);
        $convertedKey = $keyTools->convertKeyToNotation($key, $notation);

        static::assertSame($expectedKey, $convertedKey);
    }

    public function dataConvertKeyToNotationSuccess(): array
    {
        $withoutLeadingZero = [ 'leading_zero' => false, 'notation' => KeyTools::NOTATION_CAMELOT_KEY ];
        $withLeadingZero = [ 'leading_zero' => true, 'notation' => KeyTools::NOTATION_CAMELOT_KEY ];

        return [
            [ $withoutLeadingZero, [ '1A',  KeyTools::NOTATION_OPEN_KEY ], '6M'  ],
            [ $withoutLeadingZero, [ '1B',  KeyTools::NOTATION_OPEN_KEY ], '6D'  ],
            [ $withoutLeadingZero, [ '2A',  KeyTools::NOTATION_OPEN_KEY ], '7M'  ],
            [ $withoutLeadingZero, [ '2B',  KeyTools::NOTATION_OPEN_KEY ], '7D'  ],
            [ $withoutLeadingZero, [ '3A',  KeyTools::NOTATION_OPEN_KEY ], '8M'  ],
            [ $withoutLeadingZero, [ '3B',  KeyTools::NOTATION_OPEN_KEY ], '8D'  ],
            [ $withoutLeadingZero, [ '4A',  KeyTools::NOTATION_OPEN_KEY ], '9M'  ],
            [ $withoutLeadingZero, [ '4B',  KeyTools::NOTATION_OPEN_KEY ], '9D'  ],
            [ $withoutLeadingZero, [ '5A',  KeyTools::NOTATION_OPEN_KEY ], '10M' ],
            [ $withoutLeadingZero, [ '5B',  KeyTools::NOTATION_OPEN_KEY ], '10D' ],
            [ $withoutLeadingZero, [ '6A',  KeyTools::NOTATION_OPEN_KEY ], '11M' ],
            [ $withoutLeadingZero, [ '6B',  KeyTools::NOTATION_OPEN_KEY ], '11D' ],
            [ $withoutLeadingZero, [ '7A',  KeyTools::NOTATION_OPEN_KEY ], '12M' ],
            [ $withoutLeadingZero, [ '7B',  KeyTools::NOTATION_OPEN_KEY ], '12D' ],
            [ $withoutLeadingZero, [ '8A',  KeyTools::NOTATION_OPEN_KEY ], '1M'  ],
            [ $withoutLeadingZero, [ '8B',  KeyTools::NOTATION_OPEN_KEY ], '1D'  ],
            [ $withoutLeadingZero, [ '9A',  KeyTools::NOTATION_OPEN_KEY ], '2M'  ],
            [ $withoutLeadingZero, [ '9B',  KeyTools::NOTATION_OPEN_KEY ], '2D'  ],
            [ $withoutLeadingZero, [ '10A', KeyTools::NOTATION_OPEN_KEY ], '3M'  ],
            [ $withoutLeadingZero, [ '10B', KeyTools::NOTATION_OPEN_KEY ], '3D'  ],
            [ $withoutLeadingZero, [ '11A', KeyTools::NOTATION_OPEN_KEY ], '4M'  ],
            [ $withoutLeadingZero, [ '11B', KeyTools::NOTATION_OPEN_KEY ], '4D'  ],
            [ $withoutLeadingZero, [ '12A', KeyTools::NOTATION_OPEN_KEY ], '5M'  ],
            [ $withoutLeadingZero, [ '12B', KeyTools::NOTATION_OPEN_KEY ], '5D'  ],

            [ $withLeadingZero, [ '1A',  KeyTools::NOTATION_OPEN_KEY ], '06M' ],
            [ $withLeadingZero, [ '1B',  KeyTools::NOTATION_OPEN_KEY ], '06D' ],
            [ $withLeadingZero, [ '2A',  KeyTools::NOTATION_OPEN_KEY ], '07M' ],
            [ $withLeadingZero, [ '2B',  KeyTools::NOTATION_OPEN_KEY ], '07D' ],
            [ $withLeadingZero, [ '3A',  KeyTools::NOTATION_OPEN_KEY ], '08M' ],
            [ $withLeadingZero, [ '3B',  KeyTools::NOTATION_OPEN_KEY ], '08D' ],
            [ $withLeadingZero, [ '4A',  KeyTools::NOTATION_OPEN_KEY ], '09M' ],
            [ $withLeadingZero, [ '4B',  KeyTools::NOTATION_OPEN_KEY ], '09D' ],
            [ $withLeadingZero, [ '5A',  KeyTools::NOTATION_OPEN_KEY ], '10M' ],
            [ $withLeadingZero, [ '5B',  KeyTools::NOTATION_OPEN_KEY ], '10D' ],
            [ $withLeadingZero, [ '6A',  KeyTools::NOTATION_OPEN_KEY ], '11M' ],
            [ $withLeadingZero, [ '6B',  KeyTools::NOTATION_OPEN_KEY ], '11D' ],
            [ $withLeadingZero, [ '7A',  KeyTools::NOTATION_OPEN_KEY ], '12M' ],
            [ $withLeadingZero, [ '7B',  KeyTools::NOTATION_OPEN_KEY ], '12D' ],
            [ $withLeadingZero, [ '8A',  KeyTools::NOTATION_OPEN_KEY ], '01M' ],
            [ $withLeadingZero, [ '8B',  KeyTools::NOTATION_OPEN_KEY ], '01D' ],
            [ $withLeadingZero, [ '9A',  KeyTools::NOTATION_OPEN_KEY ], '02M' ],
            [ $withLeadingZero, [ '9B',  KeyTools::NOTATION_OPEN_KEY ], '02D' ],
            [ $withLeadingZero, [ '10A', KeyTools::NOTATION_OPEN_KEY ], '03M' ],
            [ $withLeadingZero, [ '10B', KeyTools::NOTATION_OPEN_KEY ], '03D' ],
            [ $withLeadingZero, [ '11A', KeyTools::NOTATION_OPEN_KEY ], '04M' ],
            [ $withLeadingZero, [ '11B', KeyTools::NOTATION_OPEN_KEY ], '04D' ],
            [ $withLeadingZero, [ '12A', KeyTools::NOTATION_OPEN_KEY ], '05M' ],
            [ $withLeadingZero, [ '12B', KeyTools::NOTATION_OPEN_KEY ], '05D' ],

            // The keys below should not be affected by leading_zero param

            [ $withLeadingZero, [ '1A',  KeyTools::NOTATION_MUSICAL ], 'Abm' ],
            [ $withLeadingZero, [ '1B',  KeyTools::NOTATION_MUSICAL ], 'B'   ],
            [ $withLeadingZero, [ '2A',  KeyTools::NOTATION_MUSICAL ], 'Ebm' ],
            [ $withLeadingZero, [ '2B',  KeyTools::NOTATION_MUSICAL ], 'Gb'  ],
            [ $withLeadingZero, [ '3A',  KeyTools::NOTATION_MUSICAL ], 'Bbm' ],
            [ $withLeadingZero, [ '3B',  KeyTools::NOTATION_MUSICAL ], 'Db'  ],
            [ $withLeadingZero, [ '4A',  KeyTools::NOTATION_MUSICAL ], 'Fm'  ],
            [ $withLeadingZero, [ '4B',  KeyTools::NOTATION_MUSICAL ], 'Ab'  ],
            [ $withLeadingZero, [ '5A',  KeyTools::NOTATION_MUSICAL ], 'Cm'  ],
            [ $withLeadingZero, [ '5B',  KeyTools::NOTATION_MUSICAL ], 'Eb'  ],
            [ $withLeadingZero, [ '6A',  KeyTools::NOTATION_MUSICAL ], 'Gm'  ],
            [ $withLeadingZero, [ '6B',  KeyTools::NOTATION_MUSICAL ], 'Bb'  ],
            [ $withLeadingZero, [ '7A',  KeyTools::NOTATION_MUSICAL ], 'Dm'  ],
            [ $withLeadingZero, [ '7B',  KeyTools::NOTATION_MUSICAL ], 'F'   ],
            [ $withLeadingZero, [ '8A',  KeyTools::NOTATION_MUSICAL ], 'Am'  ],
            [ $withLeadingZero, [ '8B',  KeyTools::NOTATION_MUSICAL ], 'C'   ],
            [ $withLeadingZero, [ '9A',  KeyTools::NOTATION_MUSICAL ], 'Em'  ],
            [ $withLeadingZero, [ '9B',  KeyTools::NOTATION_MUSICAL ], 'G'   ],
            [ $withLeadingZero, [ '10A', KeyTools::NOTATION_MUSICAL ], 'Bm'  ],
            [ $withLeadingZero, [ '10B', KeyTools::NOTATION_MUSICAL ], 'D'   ],
            [ $withLeadingZero, [ '11A', KeyTools::NOTATION_MUSICAL ], 'Gbm' ],
            [ $withLeadingZero, [ '11B', KeyTools::NOTATION_MUSICAL ], 'A'   ],
            [ $withLeadingZero, [ '12A', KeyTools::NOTATION_MUSICAL ], 'Dbm' ],
            [ $withLeadingZero, [ '12B', KeyTools::NOTATION_MUSICAL ], 'E'   ],

            [ $withLeadingZero, [ '1A',  KeyTools::NOTATION_MUSICAL_ALT ], 'G#m' ],
            [ $withLeadingZero, [ '1B',  KeyTools::NOTATION_MUSICAL_ALT ], 'B'   ],
            [ $withLeadingZero, [ '2A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Ebm' ],
            [ $withLeadingZero, [ '2B',  KeyTools::NOTATION_MUSICAL_ALT ], 'F#'  ],
            [ $withLeadingZero, [ '3A',  KeyTools::NOTATION_MUSICAL_ALT ], 'A#m' ],
            [ $withLeadingZero, [ '3B',  KeyTools::NOTATION_MUSICAL_ALT ], 'Db'  ],
            [ $withLeadingZero, [ '4A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Fm'  ],
            [ $withLeadingZero, [ '4B',  KeyTools::NOTATION_MUSICAL_ALT ], 'G#'  ],
            [ $withLeadingZero, [ '5A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Cm'  ],
            [ $withLeadingZero, [ '5B',  KeyTools::NOTATION_MUSICAL_ALT ], 'D#'  ],
            [ $withLeadingZero, [ '6A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Gm'  ],
            [ $withLeadingZero, [ '6B',  KeyTools::NOTATION_MUSICAL_ALT ], 'Bb'  ],
            [ $withLeadingZero, [ '7A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Dm'  ],
            [ $withLeadingZero, [ '7B',  KeyTools::NOTATION_MUSICAL_ALT ], 'F'   ],
            [ $withLeadingZero, [ '8A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Am'  ],
            [ $withLeadingZero, [ '8B',  KeyTools::NOTATION_MUSICAL_ALT ], 'C'   ],
            [ $withLeadingZero, [ '9A',  KeyTools::NOTATION_MUSICAL_ALT ], 'Em'  ],
            [ $withLeadingZero, [ '9B',  KeyTools::NOTATION_MUSICAL_ALT ], 'G'   ],
            [ $withLeadingZero, [ '10A', KeyTools::NOTATION_MUSICAL_ALT ], 'Bm'  ],
            [ $withLeadingZero, [ '10B', KeyTools::NOTATION_MUSICAL_ALT ], 'D'   ],
            [ $withLeadingZero, [ '11A', KeyTools::NOTATION_MUSICAL_ALT ], 'F#m' ],
            [ $withLeadingZero, [ '11B', KeyTools::NOTATION_MUSICAL_ALT ], 'A'   ],
            [ $withLeadingZero, [ '12A', KeyTools::NOTATION_MUSICAL_ALT ], 'C#m' ],
            [ $withLeadingZero, [ '12B', KeyTools::NOTATION_MUSICAL_ALT ], 'E'   ],

            [ $withLeadingZero, [ '1A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'G#m'  ],
            [ $withLeadingZero, [ '1B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bmaj' ],
            [ $withLeadingZero, [ '2A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Ebm'  ],
            [ $withLeadingZero, [ '2B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Gb'   ],
            [ $withLeadingZero, [ '3A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bbm'  ],
            [ $withLeadingZero, [ '3B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Db'   ],
            [ $withLeadingZero, [ '4A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Fmin' ],
            [ $withLeadingZero, [ '4B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Ab'   ],
            [ $withLeadingZero, [ '5A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Cmin' ],
            [ $withLeadingZero, [ '5B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Eb'   ],
            [ $withLeadingZero, [ '6A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Gmin' ],
            [ $withLeadingZero, [ '6B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bb'   ],
            [ $withLeadingZero, [ '7A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Dmin' ],
            [ $withLeadingZero, [ '7B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Fmaj' ],
            [ $withLeadingZero, [ '8A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Amin' ],
            [ $withLeadingZero, [ '8B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Cmaj' ],
            [ $withLeadingZero, [ '9A',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Emin' ],
            [ $withLeadingZero, [ '9B',  KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Gmaj' ],
            [ $withLeadingZero, [ '10A', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Bmin' ],
            [ $withLeadingZero, [ '10B', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Dmaj' ],
            [ $withLeadingZero, [ '11A', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'F#m'  ],
            [ $withLeadingZero, [ '11B', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Amaj' ],
            [ $withLeadingZero, [ '12A', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'C#m'  ],
            [ $withLeadingZero, [ '12B', KeyTools::NOTATION_MUSICAL_BEATPORT ], 'Emaj' ],

            [ $withLeadingZero, [ '1A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Ab minor' ],
            [ $withLeadingZero, [ '1B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'B major'  ],
            [ $withLeadingZero, [ '2A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Eb minor' ],
            [ $withLeadingZero, [ '2B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F# major' ],
            [ $withLeadingZero, [ '3A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Bb minor' ],
            [ $withLeadingZero, [ '3B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C# major' ],
            [ $withLeadingZero, [ '4A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F minor'  ],
            [ $withLeadingZero, [ '4B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Ab major' ],
            [ $withLeadingZero, [ '5A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C minor'  ],
            [ $withLeadingZero, [ '5B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Eb major' ],
            [ $withLeadingZero, [ '6A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'G minor'  ],
            [ $withLeadingZero, [ '6B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'Bb major' ],
            [ $withLeadingZero, [ '7A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'D minor'  ],
            [ $withLeadingZero, [ '7B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F major'  ],
            [ $withLeadingZero, [ '8A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'A minor'  ],
            [ $withLeadingZero, [ '8B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C major'  ],
            [ $withLeadingZero, [ '9A',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'E minor'  ],
            [ $withLeadingZero, [ '9B',  KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'G major'  ],
            [ $withLeadingZero, [ '10A', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'B minor'  ],
            [ $withLeadingZero, [ '10B', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'D major'  ],
            [ $withLeadingZero, [ '11A', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'F# minor' ],
            [ $withLeadingZero, [ '11B', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'A major'  ],
            [ $withLeadingZero, [ '12A', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'C# minor' ],
            [ $withLeadingZero, [ '12B', KeyTools::NOTATION_MUSICAL_ESSENTIA ], 'E major'  ],
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
            ->setMethods([ 'calculateKey' ])
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
