<?php

namespace AmpProject\Optimizer\Transformer;

use AmpProject\Amp;
use AmpProject\Attribute;
use AmpProject\Dom\CssByteCountCalculator;
use AmpProject\Dom\Document;
use AmpProject\CssLength;
use AmpProject\Extension;
use AmpProject\Layout;
use AmpProject\Optimizer\Error;
use AmpProject\Optimizer\ErrorCollection;
use AmpProject\Optimizer\Exception\InvalidHtmlAttribute;
use AmpProject\Optimizer\Transformer;
use AmpProject\Role;
use AmpProject\Tag;
use DOMAttr;
use DOMElement;
use Exception;
use RuntimeException;

/**
 * Transformer applying the server-side rendering transformations to the HTML input.
 *
 * This is ported from the NodeJS optimizer while verifying against the Go version.
 *
 * NodeJS:
 *
 * @version c92d6023ea4c9edadff593742a992da2b400a75d
 * @link    https://github.com/ampproject/amp-toolbox/blob/c92d6023ea4c9edadff593742a992da2b400a75d/packages/optimizer/lib/transformers/ServerSideRendering.js
 *
 * Go:
 * @version ea0959046c179953de43077eafaeb720f9b20bdf
 * @link    https://github.com/ampproject/amppackager/blob/ea0959046c179953de43077eafaeb720f9b20bdf/transformer/transformers/transformedidentifier.go
 *
 * @package ampproject/optimizer
 */
final class ServerSideRendering implements Transformer
{

    /**
     * List of layouts that support server-side rendering.
     *
     * @var string[]
     */
    const SUPPORTED_LAYOUTS = [
        '',
        Layout::NODISPLAY,
        Layout::FIXED,
        Layout::FIXED_HEIGHT,
        Layout::RESPONSIVE,
        Layout::CONTAINER,
        Layout::FILL,
        Layout::FLEX_ITEM,
        Layout::INTRINSIC,
    ];

    /**
     * List of elements to exclude from rendering their layout at the server.
     *
     * @var string[]
     */
    const EXCLUDED_ELEMENTS = [
        'amp-audio',
    ];

    /**
     * Map of attribute names to extraction methods.
     *
     * @var string[]
     */
    const EXTRACTION_METHOD_MAP = [
        Attribute::SIZES   => 'extractSizesAttributeCss',
        Attribute::HEIGHTS => 'extractHeightsAttributeCss',
        Attribute::MEDIA   => 'extractMediaAttributeCss',
    ];

    /**
     * XPath query to retrieve the <style amp-custom> tag, relative to the <head> node.
     *
     * @var string
     */
    const STYLE_AMP_CUSTOM_XPATH = './/style[@amp-custom]';

    /**
     * Regex pattern to match a CSS Dimension with an associated media condition.
     *
     * @var string
     */
    const CSS_DIMENSION_WITH_MEDIA_CONDITION_REGEX_PATTERN = '/(?<media_condition>.*)\s(?<dimension>.*)/m';

    /**
     * Characters to use for trimming CSS values.
     *
     * @var string
     */
    const CSS_TRIM_CHARACTERS = " \t\n\r\0\x0B;";

    /**
     * Maximum size of the CSS styles in bytes.
     *
     * @todo Max size is hard-coded for now until we ported over the generated spec into a reusable package.
     *
     * @var int
     */
    const MAX_CSS_BYTE_COUNT = 75000;

    /**
     * The <style amp-custom> element that custom CSS styles need to be added to.
     *
     * @var DOMElement
     */
    private $ampCustomStyleElement;

    /**
     * Count of bytes to calculate against the AMP size limit for the custom CSS styling.
     *
     * AMP only allows for 75000 bytes of CSS across <style amp-custom> and inline style attributes.
     *
     * @var int
     */
    private $ampCustomCssByteCount = 0;

    /**
     * Apply transformations to the provided DOM document.
     *
     * @param Document        $document DOM document to apply the transformations to.
     * @param ErrorCollection $errors   Collection of errors that are collected during transformation.
     * @return void
     */
    public function transform(Document $document, ErrorCollection $errors)
    {
        if ($this->isAlreadyTransformed($document)) {
            return;
        }

        /*
         * Within the loop we apply the layout to the custom tags (amp-foo...) where possible, but while we're at this
         * we also look for reasons not to remove the boilerplate.
         */
        $canRemoveBoilerplate = true;
        foreach ($document->ampElements as $ampElement) {
            // Make sure we only deal with valid elements.
            if (! $ampElement instanceof DOMElement) {
                continue;
            }

            // Skip tags inside a template tag.
            if ($this->hasAncestorWithTag($ampElement, Tag::TEMPLATE)) {
                continue;
            }

            /*
             * amp-experiment is a render delaying extension iff the tag is used in the doc. We check for that here
             * rather than checking for the existence of the amp-experiment script in IsRenderDelayingExtension below.
             */
            if ($ampElement->tagName === Extension::EXPERIMENT && $this->isAmpExperimentUsed($ampElement)) {
                $errors->add(Error\CannotRemoveBoilerplate::fromAmpExperiment($ampElement));
                $canRemoveBoilerplate = false;
            }

            /*
             * amp-audio requires knowing the dimensions of the browser. Do not remove the boilerplate or apply layout
             * if amp-audio is present in the document.
             */
            if ($ampElement->tagName === Extension::AUDIO) {
                $errors->add(Error\CannotRemoveBoilerplate::fromAmpAudio($ampElement));
                $canRemoveBoilerplate = false;
            }

            /*
             * Try to adapt 'sizes', 'heights' and 'media' attribute to turn them from blocking attributes into CSS
             * styles we add to <style amp-custom>.
             */
            if (! $this->adaptBlockingAttributes($document, $ampElement, $errors)) {
                $canRemoveBoilerplate = false;
                continue;
            }

            /*
             * Now apply the layout to the custom elements. If we encounter any unsupported layout, the applyLayout()
             * method returns false and we can't remove the boilerplate.
             */
            if (! $this->applyLayout($document, $ampElement, $errors)) {
                $errors->add(Error\CannotRemoveBoilerplate::fromUnsupportedLayout($ampElement));
                $canRemoveBoilerplate = false;
            }
        }

        // Emit the amp-runtime marker to indicate that we're applying server side rendering in the document.
        $ampRuntimeMarker = $document->createElement(Tag::STYLE);
        $ampRuntimeMarker->setAttribute(Attribute::AMP_RUNTIME, '');
        $document->head->insertBefore($ampRuntimeMarker, $document->head->hasChildNodes() ? $document->head->firstChild : null);

        foreach ($document->xpath->query('.//script[ @custom-element ]', $document->head) as $customElementScript) {
            /** @var DOMElement $customElementScript */
            // amp-experiment is a render delaying extension iff the tag is used in the doc, which we checked for above.
            if (
                $customElementScript->getAttribute(Attribute::CUSTOM_ELEMENT) !== Extension::EXPERIMENT
                && Amp::isRenderDelayingExtension($customElementScript)
            ) {
                $errors->add(Error\CannotRemoveBoilerplate::fromRenderDelayingScript($customElementScript));
                $canRemoveBoilerplate = false;
            }
        }

        /*
         * Below, we're only concerned about removing the boilerplate.
         * If we've already determined that we can't, we're done here.
         */
        if (! $canRemoveBoilerplate) {
            return;
        }

        // The boilerplate can be removed, note it on the <html> tag.
        $document->html->setAttribute(Amp::NO_BOILERPLATE_ATTRIBUTE, '');

        /*
         * Find the boilerplate and remove it.
         * The following code assumes that the <noscript> tag in the head is only ever used for boilerplate.
         */
        foreach ($document->xpath->query('.//noscript', $document->head) as $noscriptTagInHead) {
            /** @var DOMElement $noscriptTagInHead */
            $noscriptTagInHead->parentNode->removeChild($noscriptTagInHead);
        }

        foreach ($document->xpath->query('.//style[ @amp-boilerplate or @amp4ads-boilerplate or @amp4email-boilerplate ]', $document->head) as $boilerplateStyleTag) {
            /** @var DOMElement $boilerplateStyleTag */
            $boilerplateStyleTag->parentNode->removeChild($boilerplateStyleTag);
        }
    }

    /**
     * Check whether the document was already transformed.
     *
     * We want to ensure we don't apply server-side rendering modifications more than once.
     *
     * @param Document $document DOM document to apply the transformations to.
     * @return bool Whether the document was already transformed.
     */
    private function isAlreadyTransformed(Document $document)
    {
        if ($document->html->hasAttribute(Amp::LAYOUT_ATTRIBUTE)) {
            return true;
        }

        // Mark the document as "already transformed".
        $document->html->setAttribute(Amp::LAYOUT_ATTRIBUTE, '');

        return false;
    }

    /**
     * Apply the adequate layout to a custom element.
     *
     * @param DOMElement      $element  Element to apply the layout to.
     * @param Document        $document DOM document to apply the transformations to.
     * @param ErrorCollection $errors   Collection of errors that are collected during transformation.
     * @return boolean Whether applying the layout was successful or not.
     */
    private function applyLayout(Document $document, DOMElement $element, ErrorCollection $errors)
    {
        $ampLayout = $this->parseLayout($element->getAttribute(Attribute::LAYOUT));

        $inputWidth = new CssLength($element->getAttribute(Attribute::WIDTH));
        $inputWidth->validate(/* $allowAuto */ true, /* $allowFluid */ false);
        if (! $inputWidth->isValid()) {
            $errors->add(Error\CannotPerformServerSideRendering::fromInvalidInputWidth($element));
            return false;
        }

        $inputHeight = new CssLength($element->getAttribute(Attribute::HEIGHT));
        $inputHeight->validate(/* $allowAuto */ true, /* $allowFluid */ $ampLayout === Layout::FLUID);
        if (! $inputHeight->isValid()) {
            $errors->add(Error\CannotPerformServerSideRendering::fromInvalidInputHeight($element));
            return false;
        }

        // Calculate effective width, height and layout.
        $width  = $this->calculateWidth($ampLayout, $inputWidth, $element->tagName);
        $height = $this->calculateHeight($ampLayout, $inputHeight, $element->tagName);
        $layout = $this->calculateLayout($ampLayout, $width, $height, $element->getAttribute(Attribute::SIZES), $element->getAttribute(Attribute::HEIGHTS));

        if (! $this->isSupportedLayout($layout)) {
            $errors->add(Error\CannotPerformServerSideRendering::fromUnsupportedLayout($element, $layout));
            return false;
        }

        $this->applyLayoutAttributes($element, $layout, $width, $height);
        $this->maybeAddSizerInto($document, $element, $layout, $width, $height);

        return true;
    }

    /**
     * Parse the layout attribute value.
     *
     * @param string $layout Layout attribute value.
     * @return string Validated AMP layout, or empty string if none.
     */
    private function parseLayout($layout)
    {
        if (empty($layout)) {
            return '';
        }

        $layout = strtolower($layout);

        if (array_key_exists($layout, Layout::TO_SPEC)) {
            return $layout;
        }

        return '';
    }

    /**
     * Calculate the width of an element for its requested layout.
     *
     * @param string    $inputLayout Requested layout.
     * @param CssLength $inputWidth  Input value for the width.
     * @param string    $tagName     Tag name of the element.
     * @return CssLength Calculated Width.
     */
    private function calculateWidth($inputLayout, CssLength $inputWidth, $tagName)
    {
        if ((empty($inputLayout) || $inputLayout === Layout::FIXED) && ! $inputWidth->isDefined()) {
            // These values come from AMP's runtime and can be found in
            // https://github.com/ampproject/amphtml/blob/master/src/layout.js#L70
            switch ($tagName) {
                case Extension::ANALYTICS:
                case Extension::PIXEL:
                    $width = new CssLength('1px');
                    $width->validate(/* $allowAuto */ false, /* $allowFluid */ false);
                    return $width;
                case Extension::AUDIO:
                    $width = new CssLength(CssLength::AUTO);
                    $width->validate(/* $allowAuto */ true, /* $allowFluid */ false);
                    return $width;
                case Extension::SOCIAL_SHARE:
                    $width = new CssLength('60px');
                    $width->validate(/* $allowAuto */ false, /* $allowFluid */ false);
                    return $width;
            }
        }

        return $inputWidth;
    }

    /**
     * Calculate the height of an element for its requested layout.
     *
     * @param string    $inputLayout Requested layout.
     * @param CssLength $inputHeight Input value for the height.
     * @param string    $tagName     Tag name of the element.
     * @return CssLength Calculated Height.
     */
    private function calculateHeight($inputLayout, CssLength $inputHeight, $tagName)
    {
        if ((empty($inputLayout) || $inputLayout === Layout::FIXED || $inputLayout === Layout::FIXED_HEIGHT) && ! $inputHeight->isDefined()) {
            // These values come from AMP's runtime and can be found in
            // https://github.com/ampproject/amphtml/blob/master/src/layout.js#L70
            switch ($tagName) {
                case Extension::ANALYTICS:
                case Extension::PIXEL:
                    $height = new CssLength('1px');
                    $height->validate(/* $allowAuto */ false, /* $allowFluid */ false);
                    return $height;
                case Extension::AUDIO:
                    $height = new CssLength(CssLength::AUTO);
                    $height->validate(/* $allowAuto */ true, /* $allowFluid */ false);
                    return $height;
                case Extension::SOCIAL_SHARE:
                    $height = new CssLength('44px');
                    $height->validate(/* $allowAuto */ false, /* $allowFluid */ false);
                    return $height;
            }
        }

        return $inputHeight;
    }

    /**
     * Calculate the final AMP layout attribute for an element.
     *
     * @param string    $inputLayout Requested layout.
     * @param CssLength $width       Calculated width.
     * @param CssLength $height      Calculated height.
     * @param string    $sizesAttr   Sizes attribute value.
     * @param string    $heightsAttr Heights attribute value.
     * @return string Calculated layout.
     */
    private function calculateLayout(
        $inputLayout,
        CssLength $width,
        CssLength $height,
        $sizesAttr,
        $heightsAttr
    ) {
        if (! empty($inputLayout)) {
            return $inputLayout;
        }

        if (! $width->isDefined() && ! $height->isDefined()) {
            return Layout::CONTAINER;
        }

        if ($height->isDefined() && (! $width->isDefined() || $width->isAuto())) {
            return Layout::FIXED_HEIGHT;
        }

        if ($height->isDefined() && $width->isDefined() && (! empty($sizesAttr) || ! empty($heightsAttr))) {
            return Layout::RESPONSIVE;
        }

        return Layout::FIXED;
    }

    /**
     * Check whether a layout is support for SSR.
     *
     * @param string $layout Layout to check.
     * @return bool Whether the layout is supported for SSR.
     */
    private function isSupportedLayout($layout)
    {
        return in_array($layout, self::SUPPORTED_LAYOUTS, true);
    }

    /**
     * Apply the calculated layout attributes to an element.
     *
     * @param DOMElement $element Element to apply the layout attributes to.
     * @param string     $layout  Final layout.
     * @param CssLength  $width   Calculated width.
     * @param CssLength  $height  Calculated height.
     */
    private function applyLayoutAttributes(DOMElement $element, $layout, CssLength $width, CssLength $height)
    {
        if ($this->isExcludedElement($element)) {
            return;
        }

        $this->addClass($element, $this->getLayoutClass($layout));

        if ($this->isLayoutSizeDefined($layout)) {
            $this->addClass($element, Amp::LAYOUT_SIZE_DEFINED_CLASS);
        }

        $styles = '';
        switch ($layout) {
            case Layout::NODISPLAY:
                $element->setAttribute(Attribute::HIDDEN, Attribute::HIDDEN);
                break;
            case Layout::FIXED:
                $styles = "width:{$width->getNumeral()}{$width->getUnit()};height:{$height->getNumeral()}{$height->getUnit()};";
                break;
            case Layout::FIXED_HEIGHT:
                $styles = "height:{$height->getNumeral()}{$height->getUnit()};";
                break;
            case Layout::RESPONSIVE:
            case Layout::INTRINSIC:
                // Do nothing here but emit <i-amphtml-sizer> later.
                break;
            case Layout::FILL:
            case Layout::CONTAINER:
                // Do nothing here.
                break;
            case Layout::FLEX_ITEM:
                if ($width->isDefined()) {
                    $styles = "width:{$width->getNumeral()}{$width->getUnit()};";
                }
                if ($height->isDefined()) {
                    $styles .= "height:{$height->getNumeral()}{$height->getUnit()};";
                }
                break;
        }

        // We prepend just in case an existing value (which shouldn't be there for valid docs) doesn't end with ';'.
        if ($element->hasAttribute(Tag::STYLE)) {
            $styles .= $element->getAttribute(Tag::STYLE);
        }
        if (! empty($styles)) {
            $element->setAttribute(Tag::STYLE, $styles);
        }

        $element->setAttribute(Amp::LAYOUT_ATTRIBUTE, $layout);
    }

    /**
     * Get the class to use for a given layout.
     *
     * @param string $layout Layout to get the class for.
     * @return string Class name to use for the layout.
     */
    private function getLayoutClass($layout)
    {
        if (empty($layout)) {
            return '';
        }

        return Amp::LAYOUT_CLASS_PREFIX . $layout;
    }

    /**
     * Add a class to an element.
     *
     * This makes sure we keep existing classes on the element.
     *
     * @param DOMElement $element Element to add a class to.
     * @param string     $class   Class to add.
     */
    private function addClass(DOMElement $element, $class)
    {
        if ($element->hasAttribute(Attribute::CLASS_) && ! empty($element->getAttribute(Attribute::CLASS_))) {
            $class = "{$element->getAttribute(Attribute::CLASS_)} {$class}";
        }

        $element->setAttribute(Attribute::CLASS_, $class);
    }

    /**
     * Check whether the provided layout is a layout with a defined size.
     *
     * @param string $layout Layout to check.
     * @return bool Whether the layout has a defined size.
     */
    private function isLayoutSizeDefined($layout)
    {
        return in_array($layout, Layout::SIZE_DEFINED_LAYOUTS, true);
    }

    /**
     * Insert a sizer element if one is required.
     *
     * @param Document   $document DOM document to add a sizer to.
     * @param DOMElement $element  Element to add a sizer to.
     * @param string     $layout   Calculated layout of the element.
     * @param CssLength  $width    Calculated width of the element.
     * @param CssLength  $height   Calculated height of the element.
     */
    private function maybeAddSizerInto(
        Document $document,
        DOMElement $element,
        $layout,
        CssLength $width,
        CssLength $height
    ) {
        if (
            ! $width->isDefined()
            || $width->getNumeral() === 0
            || ! $height->isDefined()
            || $width->getUnit() !== $height->getUnit()
        ) {
            return;
        }
        $sizer = null;

        if ($layout === Layout::RESPONSIVE) {
            $sizer = $this->createResponsiveSizer($document, $width, $height);
        } elseif ($layout === Layout::INTRINSIC) {
            $sizer = $this->createIntrinsicSizer($document, $width, $height);
        }

        if ($sizer) {
            $element->insertBefore($sizer, $element->firstChild);
        }
    }

    /**
     * Create a sizer element for a responsive layout.
     *
     * @param Document  $document DOM document to create the sizer for.
     * @param CssLength $width    Calculated width of the element.
     * @param CssLength $height   Calculated height of the element.
     * @return DOMElement
     */
    private function createResponsiveSizer(Document $document, CssLength $width, CssLength $height)
    {
        $padding = $height->getNumeral() / $width->getNumeral() * 100;
        $sizer   = $document->createElement(Amp::SIZER_ELEMENT);
        $sizer->setAttribute(Tag::STYLE, sprintf('display:block;padding-top:%1.4F%%;', $padding));

        return $sizer;
    }

    /**
     * Create a sizer element for an intrinsic layout.
     *
     * Intrinsic uses an svg inside the sizer element rather than the padding trick.
     * Note: a naked svg won't work because other things expect the i-amphtml-sizer element.
     *
     * @param Document  $document DOM document to create the sizer for.
     * @param CssLength $width    Calculated width of the element.
     * @param CssLength $height   Calculated height of the element.
     * @return DOMElement
     */
    private function createIntrinsicSizer(Document $document, CssLength $width, CssLength $height)
    {
        $sizer = $document->createElement(Amp::SIZER_ELEMENT);
        $sizer->setAttribute(Attribute::CLASS_, Amp::SIZER_ELEMENT);

        $sizer_img = $document->createElement(Tag::IMG);
        $sizer_img->setAttribute(Attribute::ALT, '');
        $sizer_img->setAttribute(Attribute::ARIA_HIDDEN, 'true');
        $sizer_img->setAttribute(Attribute::CLASS_, Amp::INTRINSIC_SIZER_ELEMENT);
        $sizer_img->setAttribute(Attribute::ROLE, Role::PRESENTATION);

        // Temporarily cast decimal dimensions to integers. Can be reverted when/if the AMP Validator allows decimals.
        // Note that the floor value is used because two elements with width=99.5 in a container 199px-wide will not fit
        // on the same line if rounding is used.
        // @todo Revisit after <https://github.com/ampproject/amphtml/issues/27528>.
        $height_int = (int) $height->getNumeral();
        $width_int  = (int) $width->getNumeral();

        $sizer_img->setAttribute(Attribute::SRC, "data:image/svg+xml;charset=utf-8,<svg height=&quot;{$height_int}&quot; width=&quot;{$width_int}&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot; version=&quot;1.1&quot;/>");

        $sizer->appendChild($sizer_img);

        return $sizer;
    }

    /**
     * Check whether the element has an ancestor of a given tag type.
     *
     * @param DOMElement $element Element to check the ancestor tree of.
     * @param string     $tagName Name of the tag to look for.
     * @return bool Whether the element has an ancestor of the given tag name.
     */
    private function hasAncestorWithTag(DOMElement $element, $tagName)
    {
        $parent = $element->parentNode;
        while ($parent !== null) {
            if ($parent instanceof DOMElement && $parent->tagName === $tagName) {
                return true;
            }
            $parent = $parent->parentNode;
        }
        return false;
    }

    /**
     * Check if the amp-experiment element is actually used.
     *
     * This checks if amp-experiment has one child that is a script/json tag with a textnode that is parsable JSON and
     * not empty. The validator ensures that the script/json is parsable but since transformers may be used outside of
     * validation it is checked here as well.
     *
     * @param DOMElement $element Element to check.
     * @return bool Whether the amp-experiment element is actually used.
     */
    private function isAmpExperimentUsed(DOMElement $element)
    {
        $script = null;
        $child  = $element->firstChild;

        while ($child) {
            if (
                $child instanceof DOMElement
                && $child->tagName === Tag::SCRIPT
                && strtolower($child->getAttribute(Attribute::TYPE)) === Attribute::TYPE_JSON
            ) {
                $script = $child;
                break;
            }
            $child = $child->nextSibling;
        }

        // If not script/json tag, then not used.
        if ($script === null) {
            return false;
        }

        // If not exactly one child is present, then not used.
        if ($script->childNodes->length !== 1) {
            return false;
        }

        $child = $script->firstChild;

        // If child is not a text node or CDATA section, then not used.
        if ($child->nodeType !== XML_TEXT_NODE && $child->nodeType !== XML_CDATA_SECTION_NODE) {
            return false;
        }

        $json = $child->textContent;

        // If textnode is not JSON parsable, then not used.
        $experiment = json_decode($json, /*$assoc*/ true);
        if ($experiment === null) {
            return false;
        }

        // If JSON is empty, then not used.
        if (empty($experiment)) {
            return false;
        }

        // Otherwise, used.
        return true;
    }

    /**
     * Adapt blocking attributes so that they allow for boilerplate removal.
     *
     * Blocking attributes that need special attention are `sizes`, `heights` and `media`.
     *
     * @see https://github.com/ampproject/amp-wp/issues/4439
     *
     * @param Document        $document   DOM document to apply the transformations to.
     * @param DOMElement      $ampElement Element to adapt.
     * @param ErrorCollection $errors     Collection of errors that are collected during transformation.
     * @return bool Whether boilerplate can be removed.
     */
    private function adaptBlockingAttributes(Document $document, DOMElement $ampElement, ErrorCollection $errors)
    {
        $attributes = $ampElement->attributes;

        if ($attributes === null) {
            return true;
        }

        $customCss          = '';
        $attributesToRemove = [];

        foreach ($attributes as $attribute) {
            /**
             * Attribute to check.
             *
             * @var DOMAttr $attribute
             */
            $normalizedAttributeName = strtolower($attribute->name);
            if (array_key_exists($normalizedAttributeName, self::EXTRACTION_METHOD_MAP)) {
                $method = self::EXTRACTION_METHOD_MAP[$normalizedAttributeName];
                try {
                    $customCss            .= $this->$method($document, $ampElement, $attribute->value);
                    $attributesToRemove[] = $attribute->name;
                    $ampElement->removeAttribute($attribute->name);
                } catch (Exception $exception) {
                    $errors->add(Error\CannotRemoveBoilerplate::fromAttributesRequiringBoilerplate($ampElement));
                    return false;
                }
            }
        }

        if (!empty($customCss) && ! $this->addCustomCss($document, $customCss)) {
            $errors->add(Error\CannotRemoveBoilerplate::fromAttributesRequiringBoilerplate($ampElement));
            return false;
        }

        foreach ($attributesToRemove as $attributeToRemove) {
            $ampElement->removeAttribute($attributeToRemove);
        }

        return true;
    }

    /**
     * Add custom CSS styling to the document.
     *
     * @param Document $document  Document to add the custom CSS styling to.
     * @param string   $customCss Custom CSS styling to add.
     * @return bool Whether adding the custom CSS styling to the document succeeded.
     */
    private function addCustomCss(Document $document, $customCss)
    {
        $additionalBytes = strlen($customCss);

        if (($this->ampCustomCssByteCount + $additionalBytes) > self::MAX_CSS_BYTE_COUNT) {
            return false;
        }

        if (empty($this->ampCustomStyleElement)) {
            $ampCustomStyleElement = $document->xpath->query(self::STYLE_AMP_CUSTOM_XPATH, $document->head)->item(0);
            if ($ampCustomStyleElement instanceof DOMElement) {
                $this->ampCustomStyleElement = $ampCustomStyleElement;
                $this->ampCustomCssByteCount = (new CssByteCountCalculator($document))->calculate();
                if (($this->ampCustomCssByteCount + $additionalBytes) > self::MAX_CSS_BYTE_COUNT) {
                    return false;
                }
            } else {
                $ampCustomStyleElement = $document->createElement(Tag::STYLE);
                $ampCustomStyleElement->setAttribute(Attribute::AMP_CUSTOM, null);
                $this->ampCustomStyleElement = $ampCustomStyleElement;
                $document->head->appendChild($this->ampCustomStyleElement);
            }
        }

        $this->ampCustomStyleElement->textContent .= $customCss;
        $this->ampCustomCssByteCount              += strlen($customCss);

        return true;
    }

    /**
     * Extract the custom CSS styling from a 'sizes' attribute.
     *
     * __sizes__
     * "One or more strings separated by commas, indicating a set of source sizes. Each source size consists of:
     * - A media condition. This must be omitted for the last item in the list.
     * - A source size value."
     *
     * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img#attr-sizes
     *
     * @param Document   $document       Document containing the element to adapt.
     * @param DOMElement $element        Element to adapt.
     * @param string     $attributeValue Value of the attribute to extract the CSS styling from.
     * @return string Extract custom CSS styling.
     */
    private function extractSizesAttributeCss(Document $document, DOMElement $element, $attributeValue)
    {
        if (!$element->hasAttribute(Attribute::SRCSET) || empty($element->getAttribute(Attribute::SRCSET))) {
            // According to the Mozilla docs, a sizes attribute without a valid srcset attribute should have no effect.
            // Therefore, it should simply be stripped, without producing media queries.
            // @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/img#attr-sizes
            return '';
        }

        return $this->extractAttributeCss(
            $document,
            $element,
            $attributeValue,
            '#__ID__{width:%s};',
            '@media %s{#__ID__{width:%s;}}'
        );
    }

    /**
     * Extract the custom CSS styling from a 'heights' attribute.
     *
     * __heights__ (AMP-specific)
     * "The value of this attribute is a sizes expression based on media expressions, similar to the sizes attribute on
     * img tags but with two key differences:
     * - The value applies to the height, not the width of the element.
     * - Percent values are allowed. A percent value indicates the percent of the element's width. For example, a value
     *   of 80% indicates that the height of the element will be 80% of the element's width."
     *
     * @see https://amp.dev/documentation/guides-and-tutorials/learn/common_attributes/#heights
     *
     * @param Document   $document       Document containing the element to adapt.
     * @param DOMElement $element        Element to adapt.
     * @param string     $attributeValue Value of the attribute to extract the CSS styling from.
     * @return string Extract custom CSS styling.
     */
    private function extractHeightsAttributeCss(Document $document, DOMElement $element, $attributeValue)
    {
        return $this->extractAttributeCss(
            $document,
            $element,
            $attributeValue,
            '#__ID__>i-amphtml-sizer{height:%s};',
            '@media %s{#__ID__>i-amphtml-sizer{height:%s;}}'
        );
    }

    /**
     * Extract the custom CSS styling from an attribute and turn into a templated CSS style string.
     *
     * @param Document   $document        Document containing the element to adapt.
     * @param DOMElement $element         Element to adapt.
     * @param string     $attributeValue  Value of the attribute to extract the CSS styling from.
     * @param string     $mainStyle       CSS template for the main style.
     * @param string     $mediaQueryStyle CSS template for a media query style.
     * @return string Extract custom CSS styling.
     */
    private function extractAttributeCss(Document $document, DOMElement $element, $attributeValue, $mainStyle, $mediaQueryStyle)
    {
        if (empty($attributeValue)) {
            return '';
        }

        $sourceSizes = explode(',', $attributeValue);
        $lastItem    = trim(array_pop($sourceSizes), self::CSS_TRIM_CHARACTERS);

        if (empty($lastItem)) {
            throw InvalidHtmlAttribute::fromAttribute(Attribute::HEIGHTS, $element);
        }

        $customCss = sprintf($mainStyle, $lastItem);

        foreach ($sourceSizes as $sourceSize) {
            $matches = [];
            if (preg_match(self::CSS_DIMENSION_WITH_MEDIA_CONDITION_REGEX_PATTERN, $sourceSize, $matches)) {
                $mediaCondition = trim($matches['media_condition'], self::CSS_TRIM_CHARACTERS);

                if (empty($mediaCondition)) {
                    throw InvalidHtmlAttribute::fromAttribute(Attribute::HEIGHTS, $element);
                }

                $dimension = trim($matches['dimension'], self::CSS_TRIM_CHARACTERS);

                if (empty($dimension)) {
                    throw InvalidHtmlAttribute::fromAttribute(Attribute::HEIGHTS, $element);
                }

                $customCss .= sprintf($mediaQueryStyle, $mediaCondition, $dimension);
            }
        }

        $elementId = $document->getElementId($element);
        $customCss = str_replace('__ID__', $elementId, $customCss);

        return $customCss;
    }

    /**
     * Extract the custom CSS styling from a 'media' attribute.
     *
     * __media__
     * "The value of media is a media query. If the query does not match, the element is not rendered and its resources
     * and potentially its child resources will not be fetched. If the browser window changes size or orientation, the
     * media queries are re-evaluated and elements are hidden and shown based on the new results."
     *
     * @param Document   $document       Document containing the element to adapt.
     * @param DOMElement $element        Element to adapt.
     * @param string     $attributeValue Value of the attribute to extract the CSS styling from.
     * @return string Extract custom CSS styling.
     */
    private function extractMediaAttributeCss(Document $document, DOMElement $element, $attributeValue)
    {
        $attributeValue = trim($attributeValue, self::CSS_TRIM_CHARACTERS);

        if (empty($attributeValue)) {
            return '';
        }

        if ($attributeValue[0] === '(') {
            $attributeValue = 'all and ' . $attributeValue;
        }

        return sprintf(
            '@media not %s{#%s{display:none;}}',
            $attributeValue,
            $document->getElementId($element)
        );
    }

    /**
     * Check whether a given element should be excluded from applying its layout on the server.
     *
     * @param DOMElement $element Element to check.
     * @return bool Whether to exclude the element or not.
     */
    private function isExcludedElement(DOMElement $element)
    {
        return in_array($element->tagName, self::EXCLUDED_ELEMENTS, true);
    }
}
