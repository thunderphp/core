<?php

/** $Id$
 * photo.class.php
 * @version 1.0.0, $Revision$
 * @package foto.ulwanski.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\Media\Image {

    class ResizeTools {

        private $size;
        private $mime;
        private $file;
        private $sha1;

        public function __construct($filename) {

            $this->size = getimagesize($filename);
            $this->mime = image_type_to_mime_type($this->size[2]);

            switch ($this->getImageMimeType()) {

                case 'image/jpeg':
                    $this->file = imagecreatefromjpeg($filename);
                    break;

                case 'image/png':
                    $this->file = imagecreatefrompng($filename);
                    break;

                case 'image/gif':
                    $this->file = imagecreatefromgif($filename);
                    break;

                default:
                    throw new exception('File type "' . $this->getImageMimeType() . '" is not supported by this class.');
            }

            if (!$this->is_resources()) {
                throw new exception('It was not possible to load resources.');
            }

            $this->sha1 = sha1_file($filename);
        }

        public function __destruct() {
            if ($this->is_resources())
                imagedestroy($this->file);
        }

        public function getWidth() {
            return (int) $this->size[0];
        }

        public function getHeight() {
            return (int) $this->size[1];
        }

        public function getImageMimeType() {
            return $this->mime;
        }

        public function getImageSizeHtmlTag() {
            return $this->size[3];
        }

        public function getFileSha1() {
            return $this->sha1;
        }

        public function getRandomName() {
            mt_srand(time());
            $rand = time() . mt_rand(1000, 9999);
            return md5($this->getFileSha1() . $this->getWidth() . $this->getHeight() . $rand);
        }

        public function getImageExtension($include_dot = true) {
            return image_type_to_extension($this->size[2], $include_dot);
        }

        public function Scale($width, $height) {
            
            $image = imagecreatetruecolor($width, $height);
            $this->fastimagecopyresampled($image, $this->file, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight(), 4);
            imagedestroy($this->file);
            $this->file = $image;
            $this->size[0] = imagesx($this->file);
            $this->size[1] = imagesy($this->file);
            return $this;
        }

        public function Trim($width, $height){
            $crop_x		= max( 0, imagesx($this->file) - $width );
            $crop_y		= max( 0, imagesy($this->file) - $height);
            if( imagesx($this->file) < $width )  $crop_x = 0;
            if( imagesy($this->file) < $height ) $crop_y = 0;
            $new_width 	= min( $width,  imagesx($this->file) );
            $new_height	= min( $height, imagesy($this->file) );
            $new_image	= imagecreatetruecolor($new_width, $new_height);

            imagecopy($new_image, $this->file, 0, 0, round($crop_x/2), round($crop_y/2), $new_width, $new_height);
            $this->file = $new_image;
            $this->size[0] = imagesx($this->file);
            $this->size[1] = imagesy($this->file);
            return $this;
        }
        
        public function ScaleTrim($width, $height) {
            
            return $this;
        }
        
        public function ScaleMax($max_width, $max_height) {
            $width = $this->getWidth();
            $height = $this->getHeight();

            if ($width >= $height) {
                if ($max_width != false && $width > $max_width) {
                    $ratio = $max_width / $width;
                    $width = $max_width;
                    $height = $height * $ratio;
                }
                if ($max_height != false && $height > $max_height) {
                    $ratio = $max_height / $height;
                    $height = $max_height;
                    $width = $width * $ratio;
                }
            } else {
                if ($max_height != false && $height > $max_height) {
                    $ratio = $max_height / $height;
                    $height = $max_height;
                    $width = $width * $ratio;
                }
                if ($max_width != false && $width > $max_width) {
                    $ratio = $max_width / $width;
                    $width = $max_width;
                    $height = $height * $ratio;
                }
            }
            $this->Scale($width, $height);
            return $this;
        }
        
        public function ScaleMin($max_width, $max_height) {
            $width = $this->getWidth();
            $height = $this->getHeight();

            if ($width >= $height) {
                if ($max_width != false && $width > $max_width) {
                    $ratio = $max_height / $height;
                    $height = $max_height;
                    $width = $width * $ratio;
                }
                if ($max_height != false && $height > $max_height) {
                    $ratio = $max_width / $width;
                    $width = $max_width;
                    $height = $height * $ratio;
                }
            } else {
                if ($max_height != false && $height > $max_height) {
                    $ratio = $max_width / $width;
                    $width = $max_width;
                    $height = $height * $ratio;
                }
                if ($max_width != false && $width > $max_width) {
                    $ratio = $max_height / $height;
                    $height = $max_height;
                    $width = $width * $ratio;
                }
            }
            $this->Scale($width, $height);
            return $this;
        }

        public function ScaleMaxRotate($max_width, $max_height) {
            $width = $this->getWidth();
            $height = $this->getHeight();

            if ($width >= $height) {
                if ($max_width != false && $width > $max_width) {
                    $ratio = $max_width / $width;
                    $width = $max_width;
                    $height = $height * $ratio;
                }
                if ($max_height != false && $height > $max_height) {
                    $ratio = $max_height / $height;
                    $height = $max_height;
                    $width = $width * $ratio;
                }
            } else {
                $tmp = $max_width;
                $max_width = $max_height;
                $max_height = $tmp;
                if ($max_height != false && $height > $max_height) {
                    $ratio = $max_height / $height;
                    $height = $max_height;
                    $width = $width * $ratio;
                }
                if ($max_width != false && $width > $max_width) {
                    $ratio = $max_width / $width;
                    $width = $max_width;
                    $height = $height * $ratio;
                }
            }
            $this->Scale($width, $height);
            return $this;
        }

        public function AdjustImageOrientation() {
            if (!$this->exif->getOrientation()) return false;
            $orientation = $this->exif->getOrientation();

            $mirror = false;
            $rotate = 0;

            switch ($orientation) {
                case 1:
                    // Normal orientation
                    break;
                case 2:
                    $mirror = IMG_FLIP_HORIZONTAL;
                    break;
                case 3:
                    $rotate = 180;
                    break;
                case 4:
                    $rotate = 180;
                    $mirror = IMG_FLIP_HORIZONTAL;
                    break;
                case 5:
                    $rotate = 270;
                    $mirror = IMG_FLIP_HORIZONTAL;
                    break;
                case 6:
                    $rotate = 270;
                    break;
                case 7:
                    $rotate = 90;
                    $mirror = IMG_FLIP_HORIZONTAL;
                    break;
                case 8:
                    $rotate = 90;
                    break;
            }

            if ($rotate || $mirror){
                $this->file = imagerotate($this->file, $rotate, 0);
                //imageflip($this->file, $mirror); // (PHP 5 >= 5.5.0)
                $this->size[0] = imagesx($this->file);
                $this->size[1] = imagesy($this->file);
            }
            return $this;
        }

        public function SaveImage($filename = null, $quality = 100) {
            if ($quality > 100)
                $quality = 100;
            if ($quality < 1)
                $quality = 1;

            switch ($this->getImageMimeType()) {

                case 'image/jpeg':
                    return imagejpeg($this->file, $filename, $quality);

                case 'image/png':
                    if ($quality < 10)
                        $quality = 10;
                    $quality = 9 - (floor($quality / 10) - 1);
                    return imagepng($this->file, $filename, $quality);

                case 'image/gif':
                    return imagegif($this->file, $filename);
            }
        }

        protected function fastimagecopyresampled(&$dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h, $quality = 3) {
            // Plug-and-Play fastimagecopyresampled function replaces much slower imagecopyresampled.
            // Just include this function and change all "imagecopyresampled" references to "fastimagecopyresampled".
            // Typically from 30 to 60 times faster when reducing high resolution images down to thumbnail size using the default quality setting.
            //
            // Optional "quality" parameter (defaults is 3).  Fractional values are allowed, for example 1.5.
            // 1 = Up to 600 times faster.  Poor results, just uses imagecopyresized but removes black edges.
            // 2 = Up to 95 times faster.  Images may appear too sharp, some people may prefer it.
            // 3 = Up to 60 times faster.  Will give high quality smooth results very close to imagecopyresampled.
            // 4 = Up to 25 times faster.  Almost identical to imagecopyresampled for most images.
            // 5 = No speedup.  Just uses imagecopyresampled, highest quality but no advantage over imagecopyresampled.

            if (empty($src_image) || empty($dst_image)) {
                return false;
            }
            if ($quality <= 1) {
                $temp = imagecreatetruecolor($dst_w + 1, $dst_h + 1);
                imagecopyresized($temp, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w + 1, $dst_h + 1, $src_w, $src_h);
                imagecopyresized($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $dst_w, $dst_h);
                imagedestroy($temp);
            } elseif ($quality < 5 && (($dst_w * $quality) < $src_w || ($dst_h * $quality) < $src_h)) {
                $tmp_w = $dst_w * $quality;
                $tmp_h = $dst_h * $quality;
                $temp = imagecreatetruecolor($tmp_w + 1, $tmp_h + 1);
                imagecopyresized($temp, $src_image, $dst_x * $quality, $dst_y * $quality, $src_x, $src_y, $tmp_w + 1, $tmp_h + 1, $src_w, $src_h);
                imagecopyresampled($dst_image, $temp, 0, 0, 0, 0, $dst_w, $dst_h, $tmp_w, $tmp_h);
                imagedestroy($temp);
            } else {
                imagecopyresampled($dst_image, $src_image, $dst_x, $dst_y, $src_x, $src_y, $dst_w, $dst_h, $src_w, $src_h);
            }
            return true;
        }

        protected function is_resources() {
            if (get_resource_type($this->file) == "gd") {
                return true;
            } else {
                return false;
            }
        }

    }

}