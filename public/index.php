<?php declare(strict_types=1);

use App\Kernel;
use Symfony\Component\HttpFoundation\Request;

$timing = microtime(true);

require dirname(__DIR__).'/config/bootstrap.php';

$env = $_SERVER['APP_ENV'] ?? 'dev';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? ('prod' !== $env));

$trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false;
if ($trustedProxies) {
    Request::setTrustedProxies(
        explode(',', $trustedProxies),
        Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST
    );
}

$trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false;
if ($trustedHosts) {
    Request::setTrustedHosts(explode(',', $trustedHosts));
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);

if ($response->headers->get('content-type') === 'text/html; charset=UTF-8') {
    $content = $response->getContent();
    $content .= sprintf(
        '<!-- debug: %f / %s -->',
        microtime(true) - $timing,
        gethostname()
    );
    $response->setContent($content);
}

$response->send();
$kernel->terminate($request, $response);
