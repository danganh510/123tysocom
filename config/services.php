<?php

/**
 * Services are globally registered in this file
 */

use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use Phalcon\Session\Adapter\Files as SessionAdapter;
use Phalcon\Loader;
use Phalcon\Mvc\Model\Manager;

if (!defined('MyS3Key')) {
    define('MyS3Key', 'AKIAZPULICYWP6EU5M4F');
}
if (!defined('MyS3Secret')) {
    define('MyS3Secret', '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');
}
if (!defined('MyS3Bucket')) {
    define('MyS3Bucket', 'bincgcom');
}
if (!defined('MyCloudFrontURL')) {
    define('MyCloudFrontURL', 'https://dhjyr2o4dvaja.cloudfront.net/');
}
date_default_timezone_set('UTC');
/**
 * Read configuration
 */
$config = include __DIR__ . "/config.php";
if (!defined('URL_SITE')) {
    define('URL_SITE', 'https://www.bincg.com');
}
if (!defined('URL_IS_REAL_SITE')) {
    define('URL_IS_REAL_SITE', URL_SITE == 'https://www.bincg.com');
}
/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * We're a registering a set of directories taken from the configuration file
 */
$loader = new Loader();
$loader->registerNamespaces(array(
    'General\Models' => __DIR__ . '/../apps/models/general/',
    'Bincg\Models' => __DIR__ . '/../apps/models/',
    'Bincg\Repositories' => __DIR__ . '/../apps/repositories/',
    'Bincg\Library' => __DIR__ . '/../apps/library/',
    'Bincg\Utils' => __DIR__ . '/../apps/library/Utils/',
    'Bincg\Google' => __DIR__ . '/../apps/library/google-cloud-translate/'
));

$loader->registerDirs(
    array(
        __DIR__ . '/../apps/library/',
        __DIR__ . '/../apps/library/SMTP/
'
    )
);
$loader->register();

/**
 * Cloud Flare Fix CUSTOMER IP
 */
function ip_in_range($ip, $range) {
    if (strpos($range, '/') !== false) {
        // $range is in IP/NETMASK format
        list($range, $netmask) = explode('/', $range, 2);
        if (strpos($netmask, '.') !== false) {
            // $netmask is a 255.255.0.0 format
            $netmask = str_replace('*', '0', $netmask);
            $netmask_dec = ip2long($netmask);
            return ( (ip2long($ip) & $netmask_dec) == (ip2long($range) & $netmask_dec) );
        }
        else {
            // $netmask is a CIDR size block
            // fix the range argument
            $x = explode('.', $range);
            while(count($x)<4) $x[] = '0';
            list($a,$b,$c,$d) = $x;
            $range = sprintf("%u.%u.%u.%u", empty($a)?'0':$a, empty($b)?'0':$b,empty($c)?'0':$c,empty($d)?'0':$d);
            $range_dec = ip2long($range);
            $ip_dec = ip2long($ip);

            # Strategy 1 - Create the netmask with 'netmask' 1s and then fill it to 32 with 0s
            #$netmask_dec = bindec(str_pad('', $netmask, '1') . str_pad('', 32-$netmask, '0'));

            # Strategy 2 - Use math to create it
            $wildcard_dec = pow(2, (32-$netmask)) - 1;
            $netmask_dec = ~ $wildcard_dec;

            return (($ip_dec & $netmask_dec) == ($range_dec & $netmask_dec));
        }
    }
    else {
        // range might be 255.255.*.* or 1.2.3.0-1.2.3.255
        if (strpos($range, '*') !==false) { // a.b.*.* format
            // Just convert to A-B format by setting * to 0 for A and 255 for B
            $lower = str_replace('*', '0', $range);
            $upper = str_replace('*', '255', $range);
            $range = "$lower-$upper";
        }

        if (strpos($range, '-')!==false) { // A-B format
            list($lower, $upper) = explode('-', $range, 2);
            $lower_dec = (float)sprintf("%u",ip2long($lower));
            $upper_dec = (float)sprintf("%u",ip2long($upper));
            $ip_dec = (float)sprintf("%u",ip2long($ip));
            return ( ($ip_dec>=$lower_dec) && ($ip_dec<=$upper_dec) );
        }
        return false;
    }
}
if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
    $cf_ip_ranges = array('204.93.240.0/24',
        '204.93.177.0/24',
        '199.27.128.0/21',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/12',
        '131.0.72.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '2400:cb00::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2405:b500::/32',
        '2405:8100::/32',
        '2c0f:f248::/32',
        '2a06:98c0::/29');
    foreach ($cf_ip_ranges as $range) {
        if (ip_in_range($_SERVER['REMOTE_ADDR'], $range)) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_CF_CONNECTING_IP'];
            break;
        }
    }
}

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di['db'] = function () use ($config) {
    return new DbAdapter(array(
        "host" => $config->database->host,
        "username" => $config->database->username,
        "password" => $config->database->password,
        "dbname" => $config->database->name,
        "schema" => $config->database->name,
        'charset'=> $config->database->charset
    ));
};
$di['db_general'] = function () use ($config) {
    return new DbAdapter(array(
        "host" => $config->database_general->host,
        "username" => $config->database_general->username,
        "password" => $config->database_general->password,
        "dbname" => $config->database_general->name,
        "schema" => $config->database_general->name,
        'charset' => $config->database_general->charset
    ));
};
$di['dbSlave'] = function () use ($config) {
    return new DbAdapter(array(
        "host" => $config->dbSlave->host,
        "username" => $config->dbSlave->username,
        "password" => $config->dbSlave->password,
        "dbname" => $config->dbSlave->name,
        'charset'=> $config->dbSlave->charset
    ));
};
/**
 * Registering a router
 */
$locationCodesRegex = \Bincg\Repositories\Location::findAllLocationCountryCodes();
array_push($locationCodesRegex,'gx');
$locationCodesRegex = implode('|',$locationCodesRegex);
$languageCodesRegex = implode('|', \Bincg\Repositories\Language::findAllLanguageCodes());
$di['router'] = function ()use ($locationCodesRegex, $languageCodesRegex) {
    $router = new Router(false);
    $router->removeExtraSlashes(true);
    $router->setDefaultModule("frontend");
    //Set 404 paths
    $router->notFound(array(
        "module" => "frontend",
        "controller" => "notfound",
        "action" => "index"
    ));
    //Define a router index
    $router->add("/", array(
        "module" => "frontend",
        "controller" => "index",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}", array(
        "module" => "frontend",
        "controller" => "index",
        "action"     => "index",
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}", array(
        "module" => "frontend",
        "controller" => "index",
        "action"     => "index"
    ));
    //Set a router not found
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/notfound", array(
        "module" => "frontend",
        "controller" => "notfound",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/info/{info-ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "info",
        "action"     => "detail"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/about-us", array(
        "module" => "frontend",
        "controller" => "aboutus",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/about-us/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "aboutus",
        "action"     => "detail"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/newspapers", array(
        "module" => "frontend",
        "controller" => "newspapers",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/newspapers/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "newspapers",
        "action"     => "detail"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/newspapers/getdata", array(
        "module" => "frontend",
        "controller" => "newspapers",
        "action"     => "getdata"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/contact-us", array(
        "module" => "frontend",
        "controller" => "contactus",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/corporate-social-responsibility", array(
        "module" => "frontend",
        "controller" => "corporatesocialresponsibility",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/corporate-social-responsibility/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "corporatesocialresponsibility",
        "action"     => "detail"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/services", array(
        "module" => "frontend",
        "controller" => "services",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/services/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "services",
        "action"     => "detail"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/news", array(
        "module" => "frontend",
        "controller" => "news",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/news/{type-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "news",
        "action"     => "type"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/newsroom", array(
        "module" => "frontend",
        "controller" => "newsroom",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/newsroom/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "newsroom",
        "action"     => "detail"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/careers", array(
        "module" => "frontend",
        "controller" => "careers",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/careers/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "careers",
        "action"     => "detail"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/careers/search", array(
        "module" => "frontend",
        "controller" => "careers",
        "action"     => "search"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/newsletter", array(
        "module" => "frontend",
        "controller" => "newsletter",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/apply-cv", array(
        "module" => "frontend",
        "controller" => "applycv",
        "action"     => "applycv"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/search", array(
        "module" => "frontend",
        "controller" => "search",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/suggest", array(
        "controller" => "search",
        "action"     => "suggest"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/cron/update-sitemap", array(
        "module"	=> "frontend",
        "controller" => "generatesitemap",
        "action"     => "index"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/cron", array(
        "module"	=> "frontend",
        "controller" => "cronv3",
        "action"     => "cron"
    ));
    $router->add("/{location:$locationCodesRegex}/{language:$languageCodesRegex}/crondetail", array(
        "module"	=> "frontend",
        "controller" => "cronv3",
        "action"     => "crondetail"
    ));
    //Set a router not found
    $router->add("/notfound", array(
        "module" => "frontend",
        "controller" => "notfound",
        "action"     => "index"
    ));
    $router->add("/info/{info-ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "info",
        "action"     => "detail"
    ));
    $router->add("/about-us", array(
        "module" => "frontend",
        "controller" => "aboutus",
        "action"     => "index"
    ));
    $router->add("/about-us/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "aboutus",
        "action"     => "detail"
    ));
    $router->add("/newspapers", array(
        "module" => "frontend",
        "controller" => "newspapers",
        "action"     => "index"
    ));
    $router->add("/newspapers/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "newspapers",
        "action"     => "detail"
    ));
    $router->add("/newspapers/getdata", array(
        "module" => "frontend",
        "controller" => "newspapers",
        "action"     => "getdata"
    ));
    $router->add("/contact-us", array(
        "module" => "frontend",
        "controller" => "contactus",
        "action"     => "index"
    ));
    $router->add("/corporate-social-responsibility", array(
        "module" => "frontend",
        "controller" => "corporatesocialresponsibility",
        "action"     => "index"
    ));
    $router->add("/corporate-social-responsibility/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "corporatesocialresponsibility",
        "action"     => "detail"
    ));
    $router->add("/services", array(
        "module" => "frontend",
        "controller" => "services",
        "action"     => "index"
    ));
    $router->add("/services/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "services",
        "action"     => "detail"
    ));
    $router->add("/news", array(
        "module" => "frontend",
        "controller" => "news",
        "action"     => "index"
    ));
    $router->add("/news/{type-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "news",
        "action"     => "type"
    ));
    $router->add("/newsroom", array(
        "module" => "frontend",
        "controller" => "newsroom",
        "action"     => "index"
    ));
    $router->add("/newsroom/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "newsroom",
        "action"     => "detail"
    ));
    $router->add("/careers", array(
        "module" => "frontend",
        "controller" => "careers",
        "action"     => "index"
    ));
    $router->add("/careers/{ar-key:([a-zA-Z0-9_-]+)}", array(
        "module" => "frontend",
        "controller" => "careers",
        "action"     => "detail"
    ));
    $router->add("/careers/search", array(
        "module" => "frontend",
        "controller" => "careers",
        "action"     => "search"
    ));
    $router->add("/newsletter", array(
        "module" => "frontend",
        "controller" => "newsletter",
        "action"     => "index"
    ));
    $router->add("/apply-cv", array(
        "module" => "frontend",
        "controller" => "applycv",
        "action"     => "applycv"
    ));
    $router->add("/search", array(
        "module" => "frontend",
        "controller" => "search",
        "action"     => "index",
    ));
    $router->add("/suggest", array(
        "controller" => "search",
        "action"     => "suggest",
    ));
    $router->add("/cron/update-sitemap", array(
        "module"	=> "frontend",
        "controller" => "generatesitemap",
        "action"     => "index"
    ));
    $router->add("/cron", array(
        "module"	=> "frontend",
        "controller" => "cronv3",
        "action"     => "cron"
    ));
    $router->add("/crondetail", array(
        "module"	=> "frontend",
        "controller" => "cronv3",
        "action"     => "crondetail"
    ));
    $router->add('/dashboard', array(
        "module" => "backend",
        "controller" => "index",
        "action"	=> "index"
    ));
    $router->add('/dashboard/:controller', array(
        "module" => "backend",
        "controller" => 1,
        "action"	=> "index"
    ));
    // User Controller
    $router->add("/dashboard/accessdenied", array(
        "module" => "backend",
        "controller" => "index",
        "action" => "accessdenied"
    ));
    $router->add("/dashboard/login", array(
        "module" => "backend",
        "controller" => "login",
        "action" => "index"
    ));
    $router->add("/dashboard/logout", array(
        "module" => "backend",
        "controller" => "login",
        "action" => "logout"
    ));
    // Role Controller
    $router->add("/dashboard/list-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "index"
    ));
    $router->add("/dashboard/create-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "create"
    ));
    $router->add("/dashboard/edit-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "edit"
    ));
    $router->add("/dashboard/delete-role", array(
        "module" => "backend",
        "controller" => "role",
        "action" => "delete"
    ));
    // Upload File Controller
    $router->add('/dashboard/cloud-upload', array(
        "module" => "backend",
        "controller" => 'cloudupload',
        "action"	=> 'index'
    ));
    //    ------------Config Controller
    $router->add('/dashboard/list-config', array(
        "module"     => "backend",
        "controller" => "config",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-config', array(
        "module"     => "backend",
        "controller" => "config",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-config', array(
        "module" => "backend",
        "controller" => "config",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-config', array(
        "module" => "backend",
        "controller" => "config",
        "action"	=> "delete"
    ));
    // Type Controller
    $router->add('/dashboard/list-type', array(
        "module" => "backend",
        "controller" => "type",
        "action"	=> "index"
    ));
    $router->add('/dashboard/create-type', array(
        "module" => "backend",
        "controller" => "type",
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-type', array(
        "module" => "backend",
        "controller" => "type",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-type', array(
        "module" => "backend",
        "controller" => "type",
        "action"	=> "delete"
    ));
// Article Controller
    $router->add('/dashboard/list-article', array(
        "module"     => "backend",
        "controller" => "article",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-article', array(
        "module"     => "backend",
        "controller" => "article",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-article', array(
        "module" => "backend",
        "controller" => "article",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-article', array(
        "module" => "backend",
        "controller" => "article",
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-article-export', array(
        "module"     => "backend",
        "controller" => "article",
        "action"     => "export"
    ));


    //   Page Controller
    $router->add('/dashboard/list-page', array(
        "module"     => "backend",
        "controller" => "page",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-page', array(
        "module"     => "backend",
        "controller" => "page",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-page', array(
        "module" => "backend",
        "controller" => "page",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-page', array(
        "module" => "backend",
        "controller" => "page",
        "action"	=> "delete"
    ));
    //    ------------Banner Controller
    $router->add('/dashboard/list-banner', array(
        "module"     => "backend",
        "controller" => "banner",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-banner', array(
        "module"     => "backend",
        "controller" => "banner",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-banner', array(
        "module" => "backend",
        "controller" => "banner",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-banner', array(
        "module" => "backend",
        "controller" => "banner",
        "action"	=> "delete"
    ));
    // user controller
    $router->add('/dashboard/list-user', array(
        "module"     => "backend",
        "controller" => "user",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "create"
    ));
    $router->add('/dashboard/view-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "view"
    ));
    $router->add('/dashboard/delete-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "delete"
    ));
    $router->add('/dashboard/password-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "password"
    ));
    $router->add('/dashboard/information-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "information"
    ));
    $router->add('/dashboard/role-user', array(
        "module" => "backend",
        "controller" => "user",
        "action"	=> "role"
    ));
    // Language
    $router->add('/dashboard/list-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action"	=> "index"
    ));
    $router->add('/dashboard/create-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action"	=> "create"
    ));
    $router->add('/dashboard/delete-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action"	=> "delete"
    ));
    $router->add('/dashboard/edit-language', array(
        "module" => "backend",
        "controller" => 'language',
        "action"	=> "edit"
    ));
    // Email template
    $router->add('/dashboard/list-emailtemplate', array(
        "module"     => "backend",
        "controller" => "emailtemplate",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-emailtemplate', array(
        "module"     => "backend",
        "controller" => "emailtemplate",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-emailtemplate', array(
        "module" => "backend",
        "controller" => "emailtemplate",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-emailtemplate', array(
        "module" => "backend",
        "controller" => "emailtemplate",
        "action"	=> "delete"
    ));
    // azure search controller
    $router->add('/dashboard/azure-search', array(
        "module"     => "backend",
        "controller" => "azuresearch",
        "action"     => "index"
    ));
    // Contact Us Dashboard controller
    $router->add('/dashboard/list-contactus', array(
        "module"     => "backend",
        "controller" => "contactus",
        "action"     => "index"
    ));
    $router->add('/dashboard/view-contactus', array(
        "module" => "backend",
        "controller" => "contactus",
        "action"	=> "view"
    ));
    // controller  Newspaper
    $router->add('/dashboard/list-newspaper', array(
        "module"     => "backend",
        "controller" => "newspaper",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-newspaper', array(
        "module"     => "backend",
        "controller" => "newspaper",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-newspaper', array(
        "module" => "backend",
        "controller" => "newspaper",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-newspaper', array(
        "module" => "backend",
        "controller" => "newspaper",
        "action"	=> "delete"
    ));
    // controller  Newspaper Article
    $router->add('/dashboard/list-newspaper-article', array(
        "module"     => "backend",
        "controller" => "newspaperarticle",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-newspaper-article', array(
        "module"     => "backend",
        "controller" => "newspaperarticle",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-newspaper-article', array(
        "module" => "backend",
        "controller" => "newspaperarticle",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-newspaper-article', array(
        "module" => "backend",
        "controller" => "newspaperarticle",
        "action"	=> "delete"
    ));
    // controller  Career
    $router->add('/dashboard/list-career', array(
        "module"     => "backend",
        "controller" => "career",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-career', array(
        "module"     => "backend",
        "controller" => "career",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-career', array(
        "module" => "backend",
        "controller" => "career",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-career', array(
        "module" => "backend",
        "controller" => "career",
        "action"	=> "delete"
    ));
    $router->add('/dashboard/list-career-export', array(
        "module"     => "backend",
        "controller" => "career",
        "action"     => "export"
    ));
// Apply controller
    $router->add('/dashboard/list-apply', array(
        "module"     => "backend",
        "controller" => "apply",
        "action"     => "index"
    ));
    // controller  Career
    $router->add('/dashboard/list-career', array(
        "module"     => "backend",
        "controller" => "career",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-career', array(
        "module"     => "backend",
        "controller" => "career",
        "action"     => "create"
    ));
    // Location
    $router->add('/dashboard/list-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action"	=> "index"
    ));
    $router->add('/dashboard/create-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action"	=> "create"
    ));
    $router->add('/dashboard/edit-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-location', array(
        "module" => "backend",
        "controller" => 'location',
        "action"	=> "delete"
    ));
// Album Controller
    $router->add("/dashboard/list-album", array(
        "module" => "backend",
        "controller" => "album",
        "action" => "index"
    ));
    $router->add("/dashboard/create-album", array(
        "module" => "backend",
        "controller" => "album",
        "action" => "create"
    ));
    $router->add("/dashboard/edit-album", array(
        "module" => "backend",
        "controller" => "album",
        "action" => "edit"
    ));
    $router->add("/dashboard/delete-album", array(
        "module" => "backend",
        "controller" => "album",
        "action" => "delete"
    ));
//    -----------Office Controller
    $router->add('/dashboard/list-office', array(
        "module"     => "backend",
        "controller" => "office",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-office', array(
        "module"     => "backend",
        "controller" => "office",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-office', array(
        "module" => "backend",
        "controller" => "office",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-office', array(
        "module" => "backend",
        "controller" => "office",
        "action"	=> "delete"
    ));

    // Image Controller
    $router->add("/dashboard/list-image", array(
        "module" => "backend",
        "controller" => "image",
        "action" => "index"
    ));
    $router->add("/dashboard/create-image", array(
        "module" => "backend",
        "controller" => "image",
        "action" => "create"
    ));
    $router->add("/dashboard/edit-image", array(
        "module" => "backend",
        "controller" => "image",
        "action" => "edit"
    ));
    $router->add("/dashboard/delete-image", array(
        "module" => "backend",
        "controller" => "image",
        "action" => "delete"
    ));

    // Communication Channel
    $router->add("/dashboard/list-communication-channel", array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action" => "index"
    ));
    $router->add("/dashboard/create-communication-channel", array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action" => "create"
    ));
    $router->add("/dashboard/edit-communication-channel", array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action" => "edit"
    ));
    $router->add("/dashboard/delete-communication-channel", array(
        "module" => "backend",
        "controller" => "communicationchannel",
        "action" => "delete"
    ));
    // Subscribe controller
    $router->add('/dashboard/list-subscribe', array(
        "module"     => "backend",
        "controller" => "subscribe",
        "action"     => "index"
    ));
    $router->add('/dashboard/view-subscribe', array(
        "module" => "backend",
        "controller" => "subscribe",
        "action"	=> "view"
    ));
    //    -----------Office Image Controller
    $router->add('/dashboard/list-office-image', array(
        "module"     => "backend",
        "controller" => "officeimage",
        "action"     => "index"
    ));
    $router->add('/dashboard/create-office-image', array(
        "module"     => "backend",
        "controller" => "officeimage",
        "action"     => "create"
    ));
    $router->add('/dashboard/edit-office-image', array(
        "module" => "backend",
        "controller" => "officeimage",
        "action"	=> "edit"
    ));
    $router->add('/dashboard/delete-office-image', array(
        "module" => "backend",
        "controller" => "officeimage",
        "action"	=> "delete"
    ));
    $router->add('/dashboard/logo-upload', array(
        "module" => "backend",
        "controller" => 'logoupload',
        "action"	=> 'index'
    ));
    $router->add("/dashboard/translate", array(
        "module" => "backend",
        "controller" => "translate",
        "action" => "index"
    ));
    $router->add("/dashboard/tool-translate", array(
        "module" => "backend",
        "controller" => "tooltranslate",
        "action" => "index"
    ));
    $router->handle();
    return $router;
};

/**
 * Start the session the first time some component request the session service
 */
$di['session'] = function () {
    $session = new SessionAdapter();
    $session->start();
    return $session;
};
/**
 * Register My component
 */
$di->set('my', function(){
    return new \My();
});

/**
 * Register GlobalVariable component
 */
$di->set('globalVariable', function(){
    return new \GlobalVariable();
});

/**
 * Register cookie
 */
$di->set('cookies', function() {
    $cookies = new \Phalcon\Http\Response\Cookies();
    $cookies->useEncryption(false);
    return $cookies;
}, true);

/**
 * Register key for cookie encryption
 */
$di->set('crypt', function() {
    $crypt = new \Phalcon\Crypt();
    $crypt->setKey('binmedia123@@##'); //Use your own key!
    return $crypt;
});

/**
 * Register models manager
 */
$di->set('modelsManager', function() {
    return new Manager();
});

/**
 * Register PHPMailer manager
 */
$di->set('myMailer', function() {
    require_once(__DIR__ . "/../apps/library/SMTP/class.phpmailer.php");
    $mail = new \PHPMailer();
    $mail->IsSMTP();//telling the class to use SMTP
    $mail->SMTPAuth   = true;
    $mail->SMTPSecure = "tls";
    $mail->Host       = "email-smtp.us-west-2.amazonaws.com";
    $mail->Username   = "AKIAZPULICYWJ7UI6GUZ";
    $mail->Password   = "BNr0S+jBNdlDm0RtmJIIN8L1dFia0Y5r1R9Sk6Xg2a9r";
    $mail->CharSet    = 'utf-8';
    return $mail;
});
// Load Stripe API
require_once (__DIR__.'/../apps/library/google-cloud-translate/vendor/autoload.php');
