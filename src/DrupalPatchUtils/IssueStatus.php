<?php

/**
 * @file
 * Contains \Drupal\github_drupalorg\IssueStatus.
 */

namespace DrupalPatchUtils;

/**
 * Defines drupal.org issue statuses.
 */
class IssueStatus {

    /*
    1  Active
    13 Needs work
    8  Needs review
    14 Reviewed & tested by the community
    15 Patch (to be ported)
    2  Fixed
    4  Postponed
    16 Postponed (maintainer needs more info)
    3  Closed (duplicate)
    5  Closed (won't fix)
    6  Closed (works as designed)
    18 Closed (cannot reproduce)
    7  Closed (fixed)
    */

    const ACTIVE = 1;

    const NEEDS_WORK = 13;

    const NEEDS_REVIEW = 8;

    const RTBC = 14;

    const PATCH_TO_BE_PORTED = 15;

    const FIXED = 2;

    const POSTPONED = 4;

    const POSTPONED_MAINTAINER_INFO = 16;

    const CLOSED_DUPLICATE = 3;

    const CLOSED_WONT_FIX = 5;

    const CLOSED_WORKS_AS_DESIGNED = 6;

    const CLOSED_CANNOT_REPRODUCE = 18;

    const CLOSED_FIXED = 7;

    /**
     * Get all "Open Issues" statuses.
     *
     * @return array
     */
    public static function getOpenIssues() {
        return array(
          self::ACTIVE,
          self::NEEDS_WORK,
          self::NEEDS_REVIEW,
          self::RTBC,
          self::PATCH_TO_BE_PORTED,
          self::FIXED,
          self::POSTPONED,
          self::POSTPONED_MAINTAINER_INFO,
        );
    }

    public static function getDefinition() {
        return array(
          self::ACTIVE => array(
            'label' => 'Active',
            'aliases' => array(
              'active',
              'a',
            ),
          ),
          self::NEEDS_WORK => array(
            'label' => 'Needs work',
            'aliases' => array(
              'needs work',
              'nw',
              'work',
            ),
          ),
          self::NEEDS_REVIEW => array(
            'label' => 'Needs review',
            'aliases' => array(
              'needs review',
              'nr',
              'review',
            ),
          ),
          self::RTBC => array(
            'label' => 'Reviewed & tested by the community',
            'aliases' => array(
              'rtbc',
              '+1',
            ),
          ),
          self::PATCH_TO_BE_PORTED => array(
            'label' => 'Patch (to be ported)',
            'aliases' => array(
              'pp',
            ),
          ),
          self::FIXED => array(
            'label' => 'Fixed',
            'aliases' => array(
              'fixed',
              'f',
            ),
          ),
          self::POSTPONED => array(
            'label' => 'Postponed',
            'aliases' => array(
              'p',
            ),
          ),
          self::POSTPONED_MAINTAINER_INFO => array(
            'label' => 'Postponed (maintainer needs more info)',
            'aliases' => array(
              'pmi',
            ),
          ),
          self::CLOSED_DUPLICATE => array(
            'label' => 'Closed (duplicate)',
            'aliases' => array(
              'cd',
              'dup',
            ),
          ),
          self::CLOSED_WONT_FIX => array(
            'label' => "Closed (won't fix)",
            'aliases' => array(
              'cwf',
            ),
          ),
          self::CLOSED_WORKS_AS_DESIGNED => array(
            'label' => 'Closed (works as designed)',
            'aliases' => array(
              'cwad',
            ),
          ),
          self::CLOSED_CANNOT_REPRODUCE => array(
            'label' => 'Closed (cannot reproduce)',
            'aliases' => array(
              'cnr',
            ),
          ),
          self::CLOSED_FIXED => array(
            'label' => 'Closed (fixed)',
            'aliases' => array(
              'cf',
            ),
          ),
        );
    }

    public static function aliasMapReverse() {
        $map = self::getDefinition();
        $aliases = array();
        foreach ($map as $status => $definition) {
            $aliases[$definition['label']] = $status;
            $aliases = array_merge($aliases, array_fill_keys($definition['aliases'], $status));
        }
        return $aliases;
    }

    public static function toInteger($string) {
        $string = trim($string);
        if (is_numeric($string)) {
            return $string;
        }
        $map = self::aliasMapReverse();
        return isset($map[$string]) ? $map[$string] : FALSE;
    }

    public static function toString($integer) {
        return static::getDefinition()[$integer]['label'];
    }

}
