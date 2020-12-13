<?php

namespace AmpProject\Optimizer;

use AmpProject\Attribute;
use AmpProject\Dom\Element;
use AmpProject\Layout;
use AmpProject\LengthUnit;

final class ImageDimensions
{

    /**
     * Regular expression pattern to match the trailing unit of a dimension.
     *
     * @var string
     */
    const UNIT_REGEX_PATTERN = '/[0-9]+(?<unit>(?:[a-z]+|%))$/i';

    /**
     * Images smaller than 150px are considered tiny.
     *
     * @var int
     */
    const TINY_THRESHOLD = 150;

    /**
     * Image for which this represents the dimensions.
     *
     * @var Element
     */
    private $image;

    /**
     * Width of the image.
     *
     * @var int|float|string|null
     */
    private $width;

    /**
     * Height of the image.
     *
     * @var int|float|string|null
     */
    private $height;

    /**
     * Layout of the image.
     *
     * @var string|null
     */
    private $layout;

    /**
     * ImageDimensions constructor.
     *
     * @param Element $image Image to represent the dimensions of.
     */
    public function __construct(Element $image)
    {
        $this->image = $image;
    }

    /**
     * Get the dimensions to use from an element's parent(s).
     *
     * @return int[] Array containing the width and the height.
     */
    public function getDimensionsFromParent()
    {
        $level   = 0;
        $element = $this->image;
        while ($element->parentNode && ++$level < 3) {
            $element = $element->parentNode;

            if (! $element instanceof Element) {
                continue;
            }

            $width = $element->hasAttribute(Attribute::WIDTH)
                ? $element->getAttribute(Attribute::WIDTH)
                : -1;

            $height = $element->hasAttribute(Attribute::HEIGHT)
                ? $element->getAttribute(Attribute::HEIGHT)
                : -1;

            if (empty($width)) {
                $width = -1;
            }

            if (empty($height)) {
                $height = -1;
            }

            // Skip elements that don't provide any dimensions.
            if ($width === -1 && $height === -1) {
                continue;
            }

            return [(int)$width, (int)$height];
        }

        return [-1, -1];
    }

    /**
     * Check whether the image is to be considered tiny and should be ignored.
     *
     * A tiny image is any image with width or height less than 150 pixels and a non-responsive layout.
     *
     * @param int|null $threshold Optional. Threshold to use. Defaults to 150 pixels.
     * @return bool Whether the image is tiny.
     */
    public function isTiny($threshold = self::TINY_THRESHOLD)
    {
        // Make sure we have a valid threshold to compare against.
        if ($threshold === null) {
            $threshold = self::TINY_THRESHOLD;
        }

        // For the 'fill' layout, we need to look at the parent container's dimensions.
        if (! $this->hasWidth() && ! $this->hasHeight()) {
            if ($this->getLayout() === Layout::FILL) {
                list($this->width, $this->height) = $this->getDimensionsFromParent();
            } else {
                return true;
            }
        }

        // If one or both of the dimensions are missing, we cannot deduce an aspect ratio.
        if ($this->getWidth() === null || $this->getHeight() === null) {
            return true;
        }

        // If one or both of the dimensions are zero, the entire image will be invisible.
        if (
            (is_numeric($this->getWidth()) && $this->getWidth() <= 0)
            || (is_numeric($this->getHeight()) && $this->getHeight() <= 0)
        ) {
            return true;
        }

        // If relative units are in use, we cannot assume much about the final dimensions.
        if (
            (
                in_array($this->getWidthUnit(), LengthUnit::RELATIVE_UNITS, true)
                && (is_numeric($this->getHeight()) && $this->getHeight() < $threshold)
            ) || (
                in_array($this->getHeightUnit(), LengthUnit::RELATIVE_UNITS, true)
                && (is_numeric($this->getWidth()) && $this->getWidth() < $threshold)
            )
        ) {
            return false;
        }

        switch ($this->getLayout()) {
            // For 'intrinsic' layout, the natural dimensions are more important, so assume not tiny for now.
            case Layout::INTRINSIC:
            // For 'responsive' layout, the image adapts to the container and can grow beyond its dimensions.
            case Layout::RESPONSIVE:
                return false;

            // For 'fixed-height' layout, the width can grow and shrink, so we only compare the height.
            case Layout::FIXED_HEIGHT:
                return is_numeric($this->getHeight()) && $this->getHeight() < $threshold;

            // By default, we compare the dimensions against the provided threshold.
            default:
                return (is_numeric($this->getWidth()) && $this->getWidth() < $threshold)
                    || (is_numeric($this->getHeight()) && $this->getHeight() < $threshold);
        }
    }

    /**
     * Check whether the image has a width.
     *
     * @return bool Whether the image has a width.
     */
    public function hasWidth()
    {
        return $this->getWidth() !== null;
    }

    /**
     * Check whether the image has a height.
     *
     * @return bool Whether the image has a height.
     */
    public function hasHeight()
    {
        return $this->getHeight() !== null;
    }

    /**
     * Check whether the image has a layout.
     *
     * @return bool Whether the image has a layout.
     */
    public function hasLayout()
    {
        return $this->getLayout() !== '';
    }

    /**
     * Get the width of the image.
     *
     * @return int|float|string|null Width of the image, or null if the image has no width.
     */
    public function getWidth()
    {
        if ($this->width === null) {
            $this->width = -1;
            $width       = $this->image->getAttribute(Attribute::WIDTH);
            if (trim($width) !== '') {
                if (is_numeric($width)) {
                    $intWidth    = (int)$width;
                    $floatWidth  = (float)$width;
                    $this->width = $intWidth == $floatWidth ? $intWidth : $floatWidth;
                } else {
                    $this->width = $width;
                }
            }
        }

        return $this->width !== -1 ? $this->width : null;
    }

    /**
     * Get the height of the image.
     *
     * @return int|float|string|null Height of the image, or null if the image has no width.
     */
    public function getHeight()
    {
        if ($this->height === null) {
            $this->height = -1;
            $height       = $this->image->getAttribute(Attribute::HEIGHT);
            if (trim($height) !== '') {
                if (is_numeric($height)) {
                    $intHeight    = (int)$height;
                    $floatHeight  = (float)$height;
                    $this->height = $intHeight == $floatHeight ? $intHeight : $floatHeight;
                } else {
                    $this->height = $height;
                }
            }
        }

        return $this->height !== -1 ? $this->height : null;
    }

    /**
     * Get the unit of the width.
     *
     * @return string Unit of the width, or an empty string if none found.
     */
    public function getWidthUnit()
    {
        $width = $this->getWidth();

        if (!is_string($width)) {
            return '';
        }

        $matches = [];

        if (!preg_match(self::UNIT_REGEX_PATTERN, $width, $matches)) {
            return '';
        }

        return $matches['unit'];
    }

    /**
     * Get the unit of the height.
     *
     * @return string Unit of the height, or an empty string if none found.
     */
    public function getHeightUnit()
    {
        $height = $this->getHeight();

        if (!is_string($height)) {
            return '';
        }

        $matches = [];

        if (!preg_match(self::UNIT_REGEX_PATTERN, $height, $matches)) {
            return '';
        }

        return $matches['unit'];
    }

    /**
     * Get the layout of the image.
     *
     * @return string Layout of the image, or an empty string if the image has no layout.
     */
    public function getLayout()
    {
        if ($this->layout === null) {
            $this->layout = $this->image->hasAttribute(Attribute::LAYOUT)
                ? (string)$this->image->getAttribute(Attribute::LAYOUT)
                : '';
        }

        return $this->layout;
    }
}
