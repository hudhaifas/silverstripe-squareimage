<?php

namespace HudhaifaS\Image\Extension;

use Intervention\Image\Constraint;
use SilverStripe\Assets\Image_Backend;
use SilverStripe\Core\Extension;

/**
 *
 * @author Hudhaifa Shatnawi <hudhaifa.shatnawi@gmail.com>
 * @version 1.0, May 30, 2018 - 2:15:14 PM
 */
class SquareImageExtension
        extends Extension {

    public function Square($dimension)
    {
        $dimension = $this->owner->castDimension($dimension, 'Dimension');
        $variant = $this->owner->variantName(__FUNCTION__, $dimension);
        $self = $this->owner;

        return $this->owner->manipulateImage(
            $variant,
            function (Image_Backend $backend) use ($dimension, $self) {
                if ($backend->getWidth() === $dimension && $backend->getHeight() === $dimension) {
                    return $self;
                }

                $resource = clone $backend->getImageResource();
                if (!$resource) {
                    return null;
                }

                $backgroundColor = $self->getColor($resource);
                return $backend->paddedResize($dimension, $dimension, $backgroundColor, 0);
            }
        );
    }


    public function Blur($amount = null) {
        $variant = $this->owner->variantName(__FUNCTION__, $amount);
        return $this->owner->manipulateImage($variant,
                        function (Image_Backend $backend) use ($amount) {
                    $clone = clone $backend;
                    $resource = clone $backend->getImageResource();
                    if (!$resource) {
                        return null;
                    }

                    $resource->blur($amount);
                    $clone->setImageResource($resource);
                    return $clone;
                });
    }

    public function getColor($resource) {
        $res = clone $resource;

        // resize the image maintaining the aspect ratio and then pick the main color
        return $res
                        ->resize(3, 3,
                                function (Constraint $constraint) {
                            $constraint->aspectRatio();
                        })
                        ->pickColor(1, 1, 'hex');
    }

}
