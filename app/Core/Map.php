<?php declare(strict_types=1);

class Map
{
    /**
     * A reference to the associative array this Map is based on.
     *
     * @var array
     */
    private $map = [];

    /**
     * Is the Map writable (true) or or 'read only'?
     *
     * @var bool
     */
    private $writable;

    /**
     * Create a new Map instance.
     * 
     * @param array &$baseMap A reference to the base array
     * @param bool $writable The default is 'false', meaning 'read only'
     */
    public function __construct(array &$baseMap, bool $writable = false)
    {
        $this->map =& $baseMap;
        $this->writable = $writable;
    }

    /**
     * Checks if the Map contains a given key.
     *
     * @param string $key
     */
    public function has(string $key) : bool
    {
        return isset($this->map[$key]);
    }

    /**
     * Get the value of a given key.
     *
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key)
    {
        return $this->map[$key] ?? null;
    }

    /**
     * Get the value of a given key as an integer.
     * 
     *
     * @param string $key
     * @return int|null
     */
    public function getInt(string $key)
    {
        if (!isset($this->map[$key])) {
            return null;
        }

        return (int) $this->map[$key];
    }

    /**
     * Get the value of a given key as a float.
     *
     * @param string $key
     * @return float|null
     */
    public function getFloat(string $key)
    {
        if (!isset($this->map[$key])) {
            return null;
        }
        
        return (float) $this->map[$key];
    }

    /**
     * Get the value of a given key as a string.
     * Setting $filter to true adds slashes to special characters.
     *
     * @param string $key
     * @param bool $filter
     * @return string|null
     */
    public function getString(string $key, bool $filter = true)
    {
        if (!isset($this->map[$key])) {
            return null;
        }

        $value = (string) $this->map[$key];

        return $filter ? addslashes($value) : $value;
    }

    /**
     * Check if this Map is writable
     *
     * @return bool
     */
    private function writable() : bool {
        if ($this->writable) {
            return true;
        }
        
        return false;
    }

    /**
     * Set or add a given key-value pair into the Map
     *
     * @param string $key
     * @param mixed $value
     * @return Map
     * @throws Exception Map not writable
     */
    public function set(string $key, $value) : Map
    {
        if (!$this->writable()) {
            throw new Exception("Map not writable.");
        }
        $this->map[$key] = $value;

        return $this;
    }

    /**
     * Remove a key-value pair by key name from the Map
     *
     * @param string $key
     * @return Map
     * @throws Exception Map not writable
     */
    public function delete(string $key) : Map
    {
        if (!$this->writable()) {
            throw new Exception("Map not writable.");
        }
        
        if (isset($this->map[$key])) {
            unset($this->map[$key]);
        }

        return $this;
    }
}
