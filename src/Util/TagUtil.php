<?php

/**
 * Tags Bundle for Contao Open Source CMS
 *
 * @author    Benny Born <benny.born@numero2.de>
 * @author    Michael Bösherz <michael.boesherz@numero2.de>
 * @license   LGPL-3.0-or-later
 * @copyright Copyright (c) 2024, numero2 - Agentur für digitales Marketing GbR
 */


namespace numero2\TagsBundle\Util;

use Contao\Input;
use Contao\PageModel;
use Contao\StringUtil;


class TagUtil {


    /**
     * Get tags from url
     *
     * @return array
     */
    public static function getTagsFromUrl(): array {

        $tag = Input::get('tag');

        if( $tag === null ) {
            return  [];
        }

        $tags = json_decode(base64_decode(StringUtil::revertInputEncoding($tag))) ?? [];

        if( !$tags ) {
            $tags = [$tag];
        }

        return $tags;
    }


    /**
     * Generate parameter string for url
     *
     * @param array $tags
     *
     * @return string
     */
    public static function generateParameterForUrl( array $tags ): string {

        $tags = array_values($tags);
        if( empty($tags) ) {
            return '';
        }

        // for nicer url allow one tag to be used directly if it only got nice charatcers
        if( count($tags) === 1 && preg_match("/^[\w]+$/", $tags[0]) ) {
            return $tags[0];
        }

        return base64_encode(json_encode($tags));
    }


    /**
     * Generate url
     *
     * @param Contao\PageModel $page
     * @param array $tags
     * @param bool $blnGetParameter
     * @param bool $blnAbsolute
     *
     * @return string
     */
    public static function generateUrlWithTags( PageModel $page, array $tags, bool $blnGetParameter=false, bool $blnAbsolute=false ): string {

        $method = $blnAbsolute ? 'getAbsoluteUrl' : 'getFrontendUrl';
        $parameter = self::generateParameterForUrl($tags);

        $href = '';

        if( $blnGetParameter ) {
            $href = $page->{$method}().'?tag='.urlencode($parameter);
        } else {
            $href = $page->{$method}('/tag/'.$parameter);
        }

        return $href;
    }
}
