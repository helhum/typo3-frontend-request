<?php
if (PHP_SAPI !== 'cli') {
    die;
}

class Helhum_Typo3FrontendRequest_RequestBootstrap
{
    /**
     * @return void
     */
    public static function setUpEnvironmentFromRequest(array $requestArguments = null)
    {
        if (empty($requestArguments)) {
            die('No JSON encoded arguments given');
        }

        if (empty($requestArguments['documentRoot'])) {
            die('No documentRoot given');
        }

        if (empty($requestArguments['requestUrl']) || ($requestUrlParts = parse_url($requestArguments['requestUrl'])) === false) {
            die('No valid request URL given');
        }

        // Populating $_GET and $_REQUEST is query part is set:
        if (isset($requestUrlParts['query'])) {
            parse_str($requestUrlParts['query'], $_GET);
            parse_str($requestUrlParts['query'], $_REQUEST);
        }

        // Populating $_POST
        $_POST = [];

        // Populating $_COOKIE
        foreach ($requestArguments['headers'] as $name => $values) {
            if (strtolower($name) === 'cookie') {
                $cookieValues = array_map('trim', explode(';', implode(',', $values)));
            }
        }
        $_COOKIE = [];
        if (isset($cookieValues)) {
            foreach ($cookieValues as $cookieValue) {
                list($cookieName, $cookieValue) = explode('=', $cookieValue);
                $_COOKIE[$cookieName] = $cookieValue;
            }
        }

        // Setting up the server environment
        $_SERVER = [];
        $_SERVER['DOCUMENT_ROOT'] = $requestArguments['documentRoot'];
        $_SERVER['SERVER_ADDR'] = $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['_'] = $_SERVER['PATH_TRANSLATED'] = $requestArguments['documentRoot'] . '/index.php';
        $_SERVER['QUERY_STRING'] = (isset($requestUrlParts['query']) ? $requestUrlParts['query'] : '');
        $_SERVER['REQUEST_URI'] = $requestUrlParts['path'] . (isset($requestUrlParts['query']) ? '?' . $requestUrlParts['query'] : '');
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['SERVER_PORT'] = '80';

        // Define HTTPS and server port:
        if (isset($requestUrlParts['scheme'])) {
            if ($requestUrlParts['scheme'] === 'https') {
                $_SERVER['HTTPS'] = 'on';
                $_SERVER['SERVER_PORT'] = '443';
            }
        }

        $_SERVER['HTTP_USER_AGENT'] = 'TYPO3 Solr Request';
        $hostName = isset($requestUrlParts['host']) ? $requestUrlParts['host'] : 'localhost';
        $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $hostName;
        putenv("HTTP_HOST=$hostName");
        putenv("SERVER_NAME=$hostName");
        if (!empty($requestArguments['headers'])) {
            foreach ($requestArguments['headers'] as $name => $values) {
                $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $name))] = implode(', ', $values);
            }
        }

        if (!is_dir($_SERVER['DOCUMENT_ROOT'])) {
            die('Document root directory "' . $_SERVER['DOCUMENT_ROOT'] . '" does not exist');
        }

        if (!is_file($_SERVER['SCRIPT_FILENAME'])) {
            die('Script file "' . $_SERVER['SCRIPT_FILENAME'] . '" does not exist');
        }
    }

    /**
     * @return void
     */
    public static function executeAndOutput()
    {
        global $TT, $TSFE, $TYPO3_CONF_VARS, $BE_USER, $TYPO3_MISC;
        chdir($_SERVER['DOCUMENT_ROOT']);
        include($_SERVER['SCRIPT_FILENAME']);
    }
}

Helhum_Typo3FrontendRequest_RequestBootstrap::setUpEnvironmentFromRequest((array)'{arguments}');
Helhum_Typo3FrontendRequest_RequestBootstrap::executeAndOutput();
