<?php

namespace dbeurive\BackendTest\EntryPoints\Constants;

/**
 * Class Tags
 *
 * This class contains the tags that apply to:
 *    - SQL requests.
 *    - Procedures.
 *    - Services.
 *
 * @package dbeurive\BackendTest\EntryPoints\Constants
 */

class Tags {

    const AUTHENTICATION   = 'authentication';
    const ADMIN            = 'admin';
    const TESTTAG          = 'testtag';
    const SERVICE_TEST_TAG = 'service_test_tag';

    /**
     * Return the list of all defined tags.
     * @return array The method returns the list of all defined tags.
     */
    static public function getList() {
        $r = new \ReflectionClass(__CLASS__);
        $list = [];
        foreach ($r->getConstants() as $_constantName => $_constantValue) {
            if (0 === preg_match('/^SERVICE_TYPE_/', $_constantName)) {
                continue;
            }
            $list[$_constantName] = $_constantValue;
        }

        return $list;
    }
}