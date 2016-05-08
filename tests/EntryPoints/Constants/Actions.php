<?php

namespace dbeurive\BackendTest\EntryPoints\Constants;

/**
 * Class Actions
 *
 * This class defines the names of the actions (that are performed overs the entities).
 *
 * @package dbeurive\BackendTest\EntryPoints\Constants
 */

class Actions {
    const SELECT = 'select';
    const CREATE = 'create';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const UPSERT = 'upsert';
    const AUTHENTICATE = 'authenticate';
}