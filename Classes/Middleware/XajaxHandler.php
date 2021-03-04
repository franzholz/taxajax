<?php
declare(strict_types = 1);
namespace JambageCom\Taxajax\Middleware;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Exception;
use TYPO3\CMS\Core\Http\Dispatcher;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Lightweight alternative to regular frontend requests; used when $_GET[eID] is set.
 * In the future, logic from the EidUtility will be moved to this class, however in most cases
 * a custom PSR-15 middleware will be better suited for whatever job the eID functionality does currently.
 *
 * @internal
 */
class XajaxHandler implements MiddlewareInterface
{
    /**
     * Dispatches the request to the corresponding eID class or eID script
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $eID = $request->getParsedBody()['eID'] ?? $request->getQueryParams()['eID'] ?? null;
        $taxajax = $request->getParsedBody()['taxajax'] ?? $request->getQueryParams()['taxajax'] ?? null;

        // Do not use any more eID for xAjax!
        if ($eID != null || $taxajax === null) {
            return $handler->handle($request);
        }

        $pageId = \JambageCom\Div2007\Utility\FrontendUtility::getPageId($request);
        if ($pageId) {
            $_REQUEST['id'] = $_GET['id'] = $pageId;
        }

        // Remove any output produced until now
        ob_clean();

        /** @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['FE']['taxajax_include'][$taxajax])) {
            return $response->withStatus(404, 'taxajax has not been registered!');
        }

        $configuration = $GLOBALS['TYPO3_CONF_VARS']['FE']['taxajax_include'][$taxajax];

        // Simple check to make sure that it is not an absolute file (to use the fallback)
        if (strpos($configuration, '::') !== false || is_callable($configuration)) {
            if (
                defined('TYPO3_version') &&
                version_compare(TYPO3_version, '10.4.0', '>=')
            ) {
                $container = GeneralUtility::getContainer();
                /** @var Dispatcher $dispatcher */
                $dispatcher = GeneralUtility::makeInstance(Dispatcher::class, $container);
            } else {
                /** @var Dispatcher $dispatcher */
                $dispatcher = GeneralUtility::makeInstance(Dispatcher::class);
            }
            $request = $request->withAttribute('target', $configuration);
            return $dispatcher->dispatch($request, $response) ?? new NullResponse();
        }
        trigger_error(
            'taxajax "' . $taxajax . '" is registered with a script to a file. This behaviour will be removed in taxajax v0.6.'
            . ' Register taxajax with a class::method syntax like "\MyVendor\MyExtension\Controller\MyTaxajaxController::myMethod" instead.',
            E_USER_DEPRECATED
        );
        $scriptPath = GeneralUtility::getFileAbsFileName($configuration);
        if ($scriptPath === '') {
            throw new Exception('The registered xajax has an invalid script path.', 11518042216);
        }
        include $scriptPath;

        return new NullResponse();
    }
}
