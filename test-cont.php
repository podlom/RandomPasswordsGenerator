<?php
/**
 * Created by PhpStorm.
 * User: Taras
 * Date: 29.04.2019
 * Time: 13:12
 *
 * @author Taras Shkodenko <taras@shkodenko.com>
 */

function confidental()
{
    return 'Test Contacts confidental information goes here...';
}

function terms()
{
    return 'Test Contacts terms of use goes here...';
}

function auth()
{
    return 'Test Contact after auth handler';
}

// echo '<pre>' . var_export($_SERVER, 1) . '</pre>';
switch ($_SERVER['QUERY_STRING']) {
    case 'confidental':
        echo confidental();
        exit();
        break;

    case 'terms':
        echo terms();
        exit();
        break;

    case 'auth':
        echo auth();
        exit();
        break;
}

