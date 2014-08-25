<?php
namespace Hyperion\ApiBundle\Service;

/**
 * Convert ANSI escape codes into HTML colourised strings
 */
class AnsiColouriser
{
    protected $colours_normal = [
        0                         => [25, 25, 25],
        1                         => [205, 0, 0],
        self::MAX_SEQUENCE_LENGTH => [0, 205, 0],
        3                         => [205, 205, 0],
        4                         => [0, 0, 238],
        5                         => [205, 0, 205],
        6                         => [0, 205, 205],
        7                         => [169, 183, 198],
    ];

    protected $colours_bright = [
        0                         => [127, 127, 127],
        1                         => [255, 0, 0],
        self::MAX_SEQUENCE_LENGTH => [0, 255, 0],
        3                         => [255, 255, 0],
        4                         => [92, 92, 255],
        5                         => [255, 0, 255],
        6                         => [0, 255, 255],
        7                         => [255, 255, 255],
    ];

    const ESC_CODE            = 27;
    const MAX_SEQUENCE_LENGTH = 2;

    /**
     * Get a colour as an int[] of rgb colours
     *
     * @param int  $index
     * @param bool $bright
     * @return int[]
     * @throws \RangeException
     */
    public function getColour($index, $bright = false)
    {
        if ($index < 0 || $index > 7) {
            throw new \RangeException("Invalid colour index");
        }

        if ($bright) {
            return $this->colours_bright[$index];
        } else {
            return $this->colours_normal[$index];
        }
    }

    /**
     * Set a colour index
     *
     * @param int  $index
     * @param int  $red
     * @param int  $green
     * @param int  $blue
     * @param bool $bright
     * @throws \RangeException
     * @return $this
     */
    public function setColour($index, $red, $green, $blue, $bright = false)
    {
        if ($index < 0 || $index > 7) {
            throw new \RangeException("Invalid colour index");
        }

        if ($red < 0 || $red > 255) {
            throw new \RangeException("Red value invalid");
        }

        if ($green < 0 || $green > 255) {
            throw new \RangeException("Green value invalid");
        }

        if ($blue < 0 || $blue > 255) {
            throw new \RangeException("Blue value invalid");
        }

        if ($bright) {
            $this->colours_bright[$index] = [$red, $green, $blue];
        } else {
            $this->colours_normal[$index] = [$red, $green, $blue];
        }

        return $this;
    }

    /**
     * Colourise an ANSI escaped string
     *
     * @param string $str
     * @return string
     */
    public function parse($str)
    {
        $out     = '';
        $depth   = 0;
        $len     = strlen($str);
        $intense = false;

        for ($i = 0; $i < $len; $i++) {
            $c1 = $str{$i};
            $c2 = ($i + 1) < $len ? $str{$i + 1} : null;

            // Colour sequence detected
            if ($c1 == chr(self::ESC_CODE) && $c2 == '[') {
                // Get all codes found in the sequence
                $codes    = $this->getCodes(substr($str, $i + 2));
                $consumed = array_shift($codes);

                // Consumed chars (non-zero) indicates a valid sequence
                if ($consumed) {
                    foreach ($codes as $code) {
                        $code = (int)$code; // Will actually be a string from the parser

                        // Clear all
                        if ($code == 0) {
                            // Clear all
                            $out .= $this->close($depth);
                            $intense = false;
                            $depth   = 0;
                        } elseif ($code == 1) {
                            // + intensity
                            $intense = true;
                        } elseif ($code == 2) {
                            // - intensity
                            $intense = false;
                        } elseif ($code >= 30 && $code <= 37) {
                            // Set foreground
                            $col = $this->getColour($code - 30, $intense);
                            $out .= '<span style="color: rgb('.implode(',', $col).')">';
                            $depth++;
                        } elseif ($code == 39) {
                            // Set default foreground
                            $col = $this->getColour(7, $intense);
                            $out .= '<span style="color: rgb('.implode(',', $col).')">';
                            $depth++;
                        } elseif ($code >= 40 && $code <= 47) {
                            // Set background
                            $col = $this->getColour($code - 40, $intense);
                            $out .= '<span style="background-color: rgb('.implode(',', $col).')">';
                            $depth++;
                        } elseif ($code == 49) {
                            // Set default background
                            $col = $this->getColour(0, $intense);
                            $out .= '<span style="background-color: rgb('.implode(',', $col).')">';
                            $depth++;
                        }
                    }

                    $i += $consumed + 1;
                    continue;
                }
            }

            $out .= $c1;
        }

        $out .= $this->close($depth);
        return $out;
    }

    /**
     * Return closing tags
     *
     * @param int $depth
     * @return string
     */
    protected function close($depth)
    {
        $depth = (int)$depth;

        if (!$depth) {
            return '';
        }

        return str_repeat('</span>', $depth);
    }


    /**
     * Get colour codes from an escape sequence
     *
     * Should contain a string of codes separated by semi-colons and terminated with a literal 'm'
     *
     * @param $str
     * @return int[] First index is the consumed chars, follow is an array of codes found in the sequence
     */
    protected function getCodes($str)
    {
        $out   = [0 => 0];
        $index = 1;
        $len   = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $c = $str{$i};

            // End code - the only way to properly exit this function
            if ($c == 'm') {
                // Set the consumed bytes
                $out[0] = $i + 1;

                // Integerise the strings
                for ($int = 1; $int <= $index; $int++) {
                    $out[$int] = (int)$out[$int];
                }

                return $out;
            }

            // Code separator
            if ($c == ';') {
                $index++;
                continue;
            }

            $ord = ord($c);
            if ($ord > 47 && $ord < 58) {
                if (isset($out[$index])) {
                    $out[$index] .= $c;
                } else {
                    $out[$index] = $c;
                }
            } else {
                // Garbage character
                return $this->error();
            }
        }

        // No end char found - return no-sequence
        return $this->error();
    }

    /**
     * Returns the error response for an invalid sequence
     *
     * @return int[]
     */
    protected function error()
    {
        return [0];
    }

} 