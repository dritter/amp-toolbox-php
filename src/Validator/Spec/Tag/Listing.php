<?php

/**
 * DO NOT EDIT!
 * This file was automatically generated via bin/generate-validator-spec.php.
 */

namespace AmpProject\Validator\Spec\Tag;

use AmpProject\Format;
use AmpProject\Tag as Element;
use AmpProject\Validator\Spec\SpecRule;
use AmpProject\Validator\Spec\Tag;

final class Listing extends Tag
{
    /**
     * ID of the tag.
     *
     * @var string
     */
    const ID = 'LISTING';

    /**
     * Array of spec rules.
     *
     * @var array
     */
    const SPEC = [
        SpecRule::TAG_NAME => Element::LISTING,
        SpecRule::HTML_FORMAT => [
            Format::AMP,
        ],
    ];
}
