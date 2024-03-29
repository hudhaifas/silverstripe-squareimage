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
class SquareImagelExtension
        extends Extension {

    public function Square($width) {
        $variant = $this->owner->variantName(__FUNCTION__, $width);
        $self = $this->owner;

        return $this->owner->manipulateImage($variant,
                        function (Image_Backend $backend) use($self, $width) {
                    $clone = clone $backend;
                    $resource = clone $backend->getImageResource();
                    if (!$resource) {
                        return null;
                    }

                    $background = $self->getColor($resource);

                    $resource
                            ->resize($width, $width,
                                    function (Constraint $constraint) {
                                $constraint->aspectRatio();
                            })
                            ->resizeCanvas($width, $width, 'center', false, $background);

                    $clone->setImageResource($resource);
                    return $clone;
                });
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
