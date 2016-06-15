<?php

/** $Id$
 * Elements.php
 * @version 1.0.0, $Revision$
 * @package eroticam.pl
 * @author Marek Ulwański <marek@ulwanski.pl>
 * @copyright Copyright (c) 2015, Marek Ulwański
 * @link $HeadURL$ Subversion
 */

namespace Core\View\Html {
    
    class Elements {
        
        public static function div($content, $class = null, $id = null) {
            $part = array('<div');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</div>';
        }

        public static function strong($content, $class = null, $id = null) {
            $part = array('<strong');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</strong>';
        }

        public static function button($content, $class = null, $id = null, $type = 'submit', $onClick = null) {
            $part = array('<button');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            if ($onClick !== null)
                $part[] = 'onclick="javascript:' . $onClick . '";';
            $part[] = 'type="' . $type . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</button>';
        }

        public static function b($content, $class = null, $id = null) {
            $part = array('<b');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</b>';
        }

        public static function p($content, $class = null, $id = null) {
            $part = array('<p');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</p>';
        }

        public static function u($content, $class = null, $id = null) {
            $part = array('<u');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</u>';
        }

        public static function i($content, $class = null, $id = null) {
            $part = array('<i');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</i>';
        }

        public static function li($content, $class = null, $id = null) {
            $part = array('<li');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</li>';
        }

        public static function a($content, $href = null, $class = null, $id = null) {
            $part = array('<a');
            if ($href !== null)
                $part[] = 'href="' . $href . '"';
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</a>';
        }

        public static function img($src = null, $alt = null, $class = null, $id = null) {
            $part = array('<img');
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            if ($src !== null)
                $part[] = 'src="' . $src . '"';
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            $part[] = '>';
            return implode(' ', $part);
        }

        public static function label($content, $class = null, $id = null) {
            $part = array('<label');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</label>';
        }

        public static function span($content, $class = null, $id = null) {
            $part = array('<span');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</span>';
        }

        public static function option($label, $value = null, $selected = false) {
            $part = array('<option');
            if ($value !== null)    $part[] = 'value="'.trim($value).'"';
            if ($selected === true) $part[] = 'selected="selected"';
            $part[] = '>';
            return implode(' ', $part).$label.'</option>';
        }

        public static function tr($content, $class = null, $id = null) {
            $part = array('<tr');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</tr>';
        }

        public static function td($content, $class = null, $id = null) {
            $part = array('<td');
            if ($class !== null)
                $part[] = 'class="' . $class . '"';
            if ($id !== null)
                $part[] = 'id="' . $id . '"';
            $part[] = '>';
            return implode(' ', $part) . $content . '</td>';
        }
        
    }
    
}