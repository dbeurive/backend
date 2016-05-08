<?php

/**
 * This file defines the name of the configuration parameters used to configure the "documentation writer".
 */

namespace dbeurive\Backend\Database\Doc;

/**
 * Class Option
 *
 * This class contains the name of the configuration parameters used to configure the "documentation writer".
 *
 * @package dbeurive\Backend\Database\Doc
 */

class Option {

    /**
     * This constant defines the name of the parameter used to specify the path to the directory used to store the generated documentation.
     */
    const DOC_DB_REPO_PATH = 'doc-db-repository-path';
    /**
     * This constant defines the name of the parameter used to specify the base name of the file used to store the generated documentation.
     * In practice, there are two files: a ".sqlite" file and a ".json" file.
     */
    const DOC_DB_FILE_BASENAME = 'doc-db-basename';
    /**
     * This constant defines the name of the parameter used to specify the name of the file used to store the PHP generated documentation.
     */
    const PHP_DB_DESC_PATH = 'php-db-desc-path';
}