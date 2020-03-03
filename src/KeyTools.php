<?php

declare(strict_types=1);

namespace KeyTools;

use KeyTools\Exception\InvalidArgumentException;
use KeyTools\Exception\InvalidKeyException;
use KeyTools\Exception\UnsupportedNotationException;

class KeyTools
{
    /**
     * @var string
     */
    public const NOTATION_CAMELOT_KEY = 'camelot_key';

    /**
     * @var string
     */
    public const NOTATION_OPEN_KEY = 'open_key';

    /**
     * @var string
     */
    public const NOTATION_MUSICAL = 'musical';

    /**
     * @var string
     */
    public const NOTATION_MUSICAL_ALT = 'musical_alt';

    /**
     * @var string
     */
    public const NOTATION_MUSICAL_BEATPORT = 'musical_beatport';

    /**
     * @var string
     */
    public const NOTATION_MUSICAL_ESSENTIA = 'musical_essentia';

    /**
     * @var string
     */
    public const NOTATION_DETERMINED_BY_KEY = '';

    /**
     * @var string[]
     */
    public const NOTATION_KEYS_CAMELOT_KEY = [
        '1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B',
        '5A', '5B', '6A', '6B', '7A', '7B', '8A', '8B',
        '9A', '9B', '10A', '10B', '11A', '11B', '12A', '12B',
    ];

    /**
     * @var string[]
     */
    public const NOTATION_KEYS_OPEN_KEY = [
        '6M', '6D', '7M', '7D', '8M', '8D', '9M', '9D',
        '10M', '10D', '11M', '11D', '12M', '12D', '1M', '1D',
        '2M', '2D', '3M', '3D', '4M', '4D', '5M', '5D',
    ];

    /**
     * @var string[]
     */
    public const NOTATION_KEYS_MUSICAL = [
        'Abm', 'B', 'Ebm', 'Gb', 'Bbm', 'Db', 'Fm', 'Ab',
        'Cm', 'Eb', 'Gm', 'Bb', 'Dm', 'F', 'Am', 'C',
        'Em', 'G', 'Bm', 'D', 'Gbm', 'A', 'Dbm', 'E',
    ];

    /**
     * @var string[]
     */
    public const NOTATION_KEYS_MUSICAL_ALT = [
        'G#m', 'B', 'Ebm', 'F#', 'A#m', 'Db', 'Fm', 'G#',
        'Cm', 'D#', 'Gm', 'Bb', 'Dm', 'F', 'Am', 'C',
        'Em', 'G', 'Bm', 'D', 'F#m', 'A', 'C#m', 'E',
    ];

    /**
     * @var string[]
     */
    public const NOTATION_KEYS_MUSICAL_BEATPORT = [
        'G#m', 'Bmaj', 'Ebm', 'Gb', 'Bbm', 'Db', 'Fmin', 'Ab',
        'Cmin', 'Eb', 'Gmin', 'Bb', 'Dmin', 'Fmaj', 'Amin', 'Cmaj',
        'Emin', 'Gmaj', 'Bmin', 'Dmaj', 'F#m', 'Amaj', 'C#m', 'Emaj',
    ];

    /**
     * @var string[]
     */
    public const NOTATION_KEYS_MUSICAL_ESSENTIA = [
        'Ab minor', 'B major', 'Eb minor', 'F# major', 'Bb minor', 'C# major', 'F minor', 'Ab major',
        'C minor', 'Eb major', 'G minor', 'Bb major', 'D minor', 'F major', 'A minor', 'C major',
        'E minor', 'G major', 'B minor', 'D major', 'F# minor', 'A major', 'C# minor', 'E major',
    ];

    /**
     * @var string[]
     */
    protected const SUPPORTED_NOTATIONS = [
        self::NOTATION_CAMELOT_KEY,
        self::NOTATION_OPEN_KEY,
        self::NOTATION_MUSICAL,
        self::NOTATION_MUSICAL_ALT,
        self::NOTATION_MUSICAL_BEATPORT,
        self::NOTATION_MUSICAL_ESSENTIA,
    ];

    /**
     * @var array
     */
    protected const NOTATION_TO_KEYS_MAP = [
        self::NOTATION_CAMELOT_KEY => self::NOTATION_KEYS_CAMELOT_KEY,
        self::NOTATION_OPEN_KEY => self::NOTATION_KEYS_OPEN_KEY,
        self::NOTATION_MUSICAL => self::NOTATION_KEYS_MUSICAL,
        self::NOTATION_MUSICAL_ALT => self::NOTATION_KEYS_MUSICAL_ALT,
        self::NOTATION_MUSICAL_BEATPORT => self::NOTATION_KEYS_MUSICAL_BEATPORT,
        self::NOTATION_MUSICAL_ESSENTIA => self::NOTATION_KEYS_MUSICAL_ESSENTIA,
    ];

    /**
     * @var string[]
     */
    protected const NOTATIONS_WITH_LEADING_ZERO_KEYS = [
        self::NOTATION_CAMELOT_KEY,
        self::NOTATION_OPEN_KEY,
    ];

    /**
     * @var array
     */
    protected const DEFAULT_PARAMS = [
        'notation' => self::NOTATION_DETERMINED_BY_KEY,
        'leading_zero' => false,
    ];

    /**
     * @var int
     */
    private const WHEEL_KEYS_NUM = 12;

    /**
     * @var array
     */
    private $keyToNotationMap = [];

    /**
     * @var array
     */
    private $notationToKeysMap = [];

    /**
     * @var array
     */
    private $params = self::DEFAULT_PARAMS;

    /**
     * @param array $params
     *
     * @throws UnsupportedNotationException
     */
    public function __construct(array $params = [])
    {
        if ($params) {
            $this->setParams($params);
        }

        $this->setup();
    }

    /**
     * @param string $notation
     * @param bool|null $useLeadingZero
     * @return static
     */
    public static function fromNotation(string $notation, ?bool $useLeadingZero = null)
    {
        $params = [
            'notation' => $notation,
        ];

        if ($useLeadingZero !== null) {
            $params['leading_zero'] = $useLeadingZero;
        }

        return new static($params);
    }

    /**
     * @param array $userParams
     *
     * @throws UnsupportedNotationException
     */
    public function setParams(array $userParams): void
    {
        if (isset($userParams['notation']) && !$this->isSupportedNotation($userParams['notation'])) {
            throw new UnsupportedNotationException(sprintf('Invalid notation specified (%s)', $userParams['notation']));
        }

        $params = $userParams;

        if (!isset($params['leading_zero'])) {
            $params['leading_zero'] = static::DEFAULT_PARAMS['leading_zero'];
        }

        $this->params = $params;
    }

    /**
     * @param string $key
     * @param int $step
     * @param bool $toggleScale
     * @return string
     *
     * @throws InvalidKeyException
     * @throws InvalidArgumentException
     */
    public function calculateKey(string $key, int $step = 0, bool $toggleScale = false): string
    {
        $keyIndex = $this->getKeyIndex($key);

        if (!$this->isValidKeyIndex($keyIndex)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        if (abs($step) > self::WHEEL_KEYS_NUM) {
            throw new InvalidArgumentException(sprintf('Invalid step specified (%s)', $step));
        }

        $notation = $this->getNotation($key);
        $newKeyIndex = $this->calculateNewKeyIndex($keyIndex, $step, $toggleScale);

        $newKey = static::NOTATION_TO_KEYS_MAP[$notation][$newKeyIndex];

        if ($this->shouldContainsLeadingZero($newKey, $notation)) {
            $newKey = '0' . $newKey;
        }

        return $newKey;
    }

    /**
     * @param string $key
     * @param string $newNotation
     * @return string
     *
     * @throws InvalidKeyException
     * @throws UnsupportedNotationException
     */
    public function convertKeyToNotation(string $key, string $newNotation): string
    {
        $keyIndex = $this->getKeyIndex($key);

        if (!$this->isValidKeyIndex($keyIndex)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        if (!$this->isSupportedNotation($newNotation)) {
            throw new UnsupportedNotationException(sprintf('Invalid notation specified (%s)', $newNotation));
        }

        $newKey = static::NOTATION_TO_KEYS_MAP[$newNotation][$keyIndex];

        if ($this->shouldContainsLeadingZero($newKey, $newNotation)) {
            $newKey = '0' . $newKey;
        }

        return $newKey;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isValidKey(string $key): bool
    {
        $keyIndex = $this->getKeyIndex($key);

        return $this->isValidKeyIndex($keyIndex);
    }

    /**
     * @param string $notation
     * @return bool
     */
    public function isSupportedNotation(string $notation): bool
    {
        return in_array($notation, static::SUPPORTED_NOTATIONS);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isMajorKey($key): bool
    {
        $keyIndex = $this->getKeyIndex($key);

        if (!$this->isValidKeyIndex($keyIndex)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        return ($keyIndex % 2) !== 0;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isMinorKey($key): bool
    {
        $keyIndex = $this->getKeyIndex($key);

        if (!$this->isValidKeyIndex($keyIndex)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        return ($keyIndex % 2) === 0;
    }

    /**
     * @param string $key
     * @return string
     */
    public function noChange(string $key): string
    {
        return $this->calculateKey($key);
    }

    /**
     * @param string $key
     * @return string
     */
    public function perfectFourth(string $key): string
    {
        return $this->calculateKey($key, -1);
    }

    /**
     * @param string $key
     * @return string
     */
    public function perfectFifth(string $key): string
    {
        return $this->calculateKey($key, 1);
    }

    /**
     * @param string $key
     * @return string
     */
    public function relativeMinorToMajor(string $key): string
    {
        return $this->calculateKey($key, 0, true);
    }

    /**
     * @param string $key
     * @return string
     */
    public function minorToMajor(string $key): string
    {
        return $this->calculateKey($key, 3, true);
    }

    /**
     * @param string $key
     * @return string
     */
    public function minorThird(string $key): string
    {
        return $this->calculateKey($key, -3);
    }

    /**
     * @param string $key
     * @return string
     */
    public function halfStep(string $key): string
    {
        return $this->calculateKey($key, 7);
    }

    /**
     * @param string $key
     * @return string
     */
    public function wholeStep(string $key): string
    {
        return $this->calculateKey($key, 2);
    }

    /**
     * @param string $key
     * @return string
     */
    public function dominantRelative(string $key): string
    {
        $stepChange = $this->isMajorKey($key) ? -1 : 1;

        return $this->calculateKey($key, $stepChange, true);
    }

    /**
     * @param string $key
     * @return int|null
     */
    private function getKeyIndex(string $key): ?int
    {
        $normalizedKey = $this->normalizeKey($key);

        if (!isset($this->keyToNotationMap[$normalizedKey])) {
            return null;
        }

        $notation = $this->getNotation($key);
        $keyIndex = array_search($normalizedKey, $this->notationToKeysMap[$notation], true);

        return $keyIndex !== false ? (int) $keyIndex : null;
    }

    /**
     * @param int $keyIndex
     * @param int $step
     * @param bool $toggleScale
     * @return int
     */
    private function calculateNewKeyIndex(int $keyIndex, int $step, bool $toggleScale): int
    {
        $currentKeyIndex = $keyIndex;

        if ($toggleScale) {
            $currentKeyIndex = ($currentKeyIndex % 2) ? $currentKeyIndex - 1 : $currentKeyIndex + 1;
        }

        $stepChange = $step > 0 ? $step * 2 : (self::WHEEL_KEYS_NUM * 2) + ($step * 2);
        $newKeyIndex = ($stepChange + $currentKeyIndex) % (self::WHEEL_KEYS_NUM * 2);

        return $newKeyIndex;
    }

    /**
     * @param string $key
     * @return string|null ?string
     */
    private function getNotation(string $key): ?string
    {
        $notation = $this->params['notation'];

        if ($notation === static::NOTATION_DETERMINED_BY_KEY) {
            $normalizedKey = $this->normalizeKey($key);

            $notation = $this->keyToNotationMap[$normalizedKey];
        }

        return $notation;
    }

    /**
     * @return void
     */
    private function setup(): void
    {
        foreach (static::NOTATION_TO_KEYS_MAP as $notation => $keys) {
            if (!isset($this->notationToKeysMap[$notation])) {
                $this->notationToKeysMap[$notation] = [];
            }

            foreach ($keys as $key) {
                $normalizedKey = $this->normalizeKey($key);

                $this->notationToKeysMap[$notation][] = $normalizedKey;

                if (!isset($this->keyToNotationMap[$normalizedKey])) {
                    $this->keyToNotationMap[$normalizedKey] = $notation;
                }
            }
        }
    }

    /**
     * @param string $key
     * @param string $notation
     * @return bool
     */
    private function shouldContainsLeadingZero(string $key, string $notation): bool
    {
        if (!$this->params['leading_zero'] || !in_array($notation, static::NOTATIONS_WITH_LEADING_ZERO_KEYS, true)) {
            return false;
        }

        $wheelIndex = (int) rtrim($key, 'ABDM');

        return $wheelIndex < 10;
    }

    /**
     * @param int|null $keyIndex
     * @return bool
     */
    private function isValidKeyIndex(?int $keyIndex): bool
    {
        return is_int($keyIndex) && $keyIndex >= 0 && $keyIndex <= self::WHEEL_KEYS_NUM * 2;
    }

    /**
     * @param string $key
     * @return string
     */
    private function normalizeKey($key): string
    {
        return strtolower(ltrim($key, '0'));
    }
}
