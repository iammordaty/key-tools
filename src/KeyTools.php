<?php

declare(strict_types = 1);

namespace KeyTools;

use KeyTools\{
    Exception\InvalidArgumentException,
    Exception\InvalidKeyException,
    Exception\UnsupportedNotationException
};

class KeyTools
{
    /**
     * @var string
     */
    const NOTATION_CAMELOT_KEY = 'camelot_key';

    /**
     * @var string
     */
    const NOTATION_OPEN_KEY = 'open_key';

    /**
     * @var string
     */
    const NOTATION_MUSICAL = 'musical';

    /**
     * @var string
     */
    const NOTATION_MUSICAL_ALT = 'musical_alt';

    /**
     * @var string
     */
    const NOTATION_MUSICAL_BEATPORT = 'musical_beatport';

     /**
      * @var string
      */
    const NOTATION_DETERMINED_BY_KEY = '';

    /**
     * @var string[]
     */
    const NOTATION_KEYS_CAMELOT_KEY = [
        '1A', '1B', '2A', '2B', '3A', '3B', '4A', '4B',
        '5A', '5B', '6A', '6B', '7A', '7B', '8A', '8B',
        '9A', '9B', '10A', '10B', '11A', '11B', '12A', '12B',
    ];

    /**
     * @var string[]
     */
    const NOTATION_KEYS_OPEN_KEY = [
        '6M', '6D', '7M', '7D', '8M', '8D', '9M', '9D',
        '10M', '10D', '11M', '11D', '12M', '12D', '1M', '1D',
        '2M', '2D', '3M', '3D', '4M', '4D', '5M', '5D',
    ];

    /**
     * @var string[]
     */
    const NOTATION_KEYS_MUSICAL = [
        'Abm', 'B', 'Ebm', 'Gb', 'Bbm', 'Db', 'Fm', 'Ab',
        'Cm', 'Eb', 'Gm', 'Bb', 'Dm', 'F', 'Am', 'C',
        'Em', 'G', 'Bm', 'D', 'Gbm', 'A', 'Dbm', 'E',
    ];

    /**
     * @var string[]
     */
    const NOTATION_KEYS_MUSICAL_ALT = [
        'G#m', 'B', 'Ebm', 'F#', 'A#m', 'Db', 'Fm', 'G#',
        'Cm', 'D#', 'Gm', 'Bb', 'Dm', 'F', 'Am', 'C',
        'Em', 'G', 'Bm', 'D', 'F#m', 'A', 'C#m', 'E',
    ];

    /**
     * @var string[]
     */
    const NOTATION_KEYS_MUSICAL_BEATPORT = [
        'G#m', 'Bmaj', 'Ebm', 'Gb', 'Bbm', 'Db', 'Fmin', 'Ab',
        'Cmin', 'Eb', 'Gmin', 'Bb', 'Dmin', 'Fmaj', 'Amin', 'Cmaj',
        'Emin', 'Gmaj', 'Bmin', 'Dmaj', 'F#m', 'Amaj', 'C#m', 'Emaj',
    ];

    /**
     * @var string[]
     */
    private const SUPPORTED_NOTATIONS = [
        self::NOTATION_CAMELOT_KEY,
        self::NOTATION_OPEN_KEY,
        self::NOTATION_MUSICAL,
        self::NOTATION_MUSICAL_ALT,
        self::NOTATION_MUSICAL_BEATPORT,
    ];

    /**
     * @var string[]
     */
    private const NOTATION_TO_KEYS_MAP = [
        self::NOTATION_CAMELOT_KEY => self::NOTATION_KEYS_CAMELOT_KEY,
        self::NOTATION_OPEN_KEY => self::NOTATION_KEYS_OPEN_KEY,
        self::NOTATION_MUSICAL => self::NOTATION_KEYS_MUSICAL,
        self::NOTATION_MUSICAL_ALT => self::NOTATION_KEYS_MUSICAL_ALT,
        self::NOTATION_MUSICAL_BEATPORT => self::NOTATION_KEYS_MUSICAL_BEATPORT,
    ];

    /**
     * @var int
     */
    private const WHEEL_KEYS_NUM = 12;

    /**
     * @var string[]
     */
    private const DEFAULT_PARAMS = [
        'notation' => self::NOTATION_DETERMINED_BY_KEY,
    ];

    /**
     * @var string[]
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
     * @param array $params
     *
     * @throws UnsupportedNotationException
     */
    public function setParams(array $params): void
    {
        if (isset($params['notation']) && !$this->isSupportedNotation($params['notation'])) {
            throw new UnsupportedNotationException(sprintf('Invalid notation specified (%s)', $params['notation']));
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
        if (!$this->isValidKey($key)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        if (abs($step) > self::WHEEL_KEYS_NUM) {
            throw new InvalidArgumentException(sprintf('Invalid step specified (%s)', $step));
        }

        $notation = $this->getNotation($key);
        $keyIndex = $this->getKeyIndex($key);
        $newKeyIndex = $this->calculateNewKeyIndex($keyIndex, $step, $toggleScale);

        return self::NOTATION_TO_KEYS_MAP[$notation][$newKeyIndex];
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
        if (!$this->isValidKey($key)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        if (!$this->isSupportedNotation($newNotation)) {
            throw new UnsupportedNotationException(sprintf('Invalid notation specified (%s)', $newNotation));
        }

        $keyIndex = $this->getKeyIndex($key);

        return self::NOTATION_TO_KEYS_MAP[$newNotation][$keyIndex];
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isValidKey(string $key): bool
    {
        return $this->getKeyIndex($key) !== null;
    }

    /**
     * @param string $notation
     * @return bool
     */
    public function isSupportedNotation(string $notation): bool
    {
        return in_array($notation, self::SUPPORTED_NOTATIONS);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isMajorKey($key): bool
    {
        if (!$this->isValidKey($key)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        $keyIndex = $this->getKeyIndex($key);

        return ($keyIndex % 2) !== 0;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function isMinorKey($key): bool
    {
        if (!$this->isValidKey($key)) {
            throw new InvalidKeyException(sprintf('Invalid key specified (%s)', $key));
        }

        $keyIndex = $this->getKeyIndex($key);

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
     * @return ?int
     */
    private function getKeyIndex(string $key): ?int
    {
        $normalizedKey = $this->normalizeKey($key);

        if (!isset($this->keyToNotationMap[$normalizedKey])) {
            return null;
        }

        $notation = $this->getNotation($key);
        $keyIndex = array_search($normalizedKey, $this->notationToKeysMap[$notation]);

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
            $currentKeyIndex = $currentKeyIndex % 2 ? $currentKeyIndex - 1 : $currentKeyIndex + 1;
        }

        $stepChange = $step > 0 ? $step * 2 : (self::WHEEL_KEYS_NUM * 2) + ($step * 2);
        $newKeyIndex = ($stepChange + $currentKeyIndex) % (self::WHEEL_KEYS_NUM * 2);

        return $newKeyIndex;
    }

    /**
     * @param string $key
     * @return ?string
     */
    private function getNotation(string $key): ?string
    {
        $notation = $this->params['notation'];

        if ($notation === self::NOTATION_DETERMINED_BY_KEY) {
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
        foreach (self::NOTATION_TO_KEYS_MAP as $notation => $keys) {
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
     * @return string
     */
    private function normalizeKey($key): string
    {
        return ltrim(strtolower($key), '0');
    }
}
