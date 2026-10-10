<?php

declare(strict_types=1);

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

use TYPO3\CMS\Core\EventDispatcher\ListenerProvider;
use TYPO3\CMS\Core\Http\Dispatcher;
use TYPO3\CMS\Core\Http\NullResponse;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Resource\Event\GeneratePublicUrlForResourceEvent;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Controller\ErrorController;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;
use TYPO3\CMS\Frontend\Page\PageAccessFailureReasons;
use TYPO3\CMS\Frontend\Resource\PublicUrlPrefixer;

use JambageCom\Div2007\Utility\FrontendUtility;

/**
 * Lightweight alternative to regular frontend requests; used when $_GET[eID] is set.
 * In the future, logic from the EidUtility will be moved to this class, however in most cases
 * a custom PSR-15 middleware will be better suited for whatever job the eID functionality does currently.
 *
 * @internal
 */
class XajaxHandler implements MiddlewareInterface
{
    public function __construct(
        private readonly ListenerProvider $listenerProvider,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $parsedBody = $request->getParsedBody() ?? [];
        $queryParams = $request->getQueryParams() ?? [];

        $eID = $parsedBody['eID'] ?? $queryParams['eID'] ?? null;
        $taxajax = $parsedBody['taxajax'] ?? $queryParams['taxajax'] ?? null;

        // 1. Route splitting: If it is a normal page load (no active incoming taxajax request),
        // we step back immediately and let TYPO3 do its native asset collection and rendering.
        if ($taxajax === null) {
            return $handler->handle($request);
        }

        // 2. Guard Clause: If it is a distinct foreign eID request not meant for us, skip entirely
        if ($eID !== null) {
            return $handler->handle($request);
        }

        // --- Beyond this point, we only handle real incoming active AJAX processing ---
        // Remove any output produced until now
        ob_clean();

        /** @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);

        if (!isset($GLOBALS['TYPO3_CONF_VARS']['FE']['taxajax_include'][$taxajax])) {
            return $response->withStatus(404, 'taxajax has not been registered!');
        }

        $configuration = $GLOBALS['TYPO3_CONF_VARS']['FE']['taxajax_include'][$taxajax];

        $site = $request->getAttribute('site');
        if (!$site instanceof Site) {
            return GeneralUtility::makeInstance(ErrorController::class)->pageNotFoundAction(
                $request,
                'No site configuration found.',
                ['code' => PageAccessFailureReasons::PAGE_NOT_FOUND]
            );
        }

        $pageId = FrontendUtility::getPageId($request);
        if (is_int($pageId)) {
            $_REQUEST['id'] = $_GET['id'] = $pageId;
        }

        if (strpos($configuration, '::') !== false || is_callable($configuration)) {
            $container = GeneralUtility::getContainer();
            /** @var Dispatcher $dispatcher */
            $dispatcher = GeneralUtility::makeInstance(Dispatcher::class, $container);
            $request = $request->withAttribute('target', $configuration);

            return $dispatcher->dispatch($request) ?? new NullResponse();
        }

        trigger_error(
            'taxajax "' . $taxajax . '" is registered with a script to the file "' . GeneralUtility::getFileAbsFileName($configuration) . '". This behaviour has been removed in taxajax v0.6.0'
            . ' Register taxajax with a class::method syntax like "\MyVendor\MyExtension\Controller\MyTaxajaxController::myMethod" instead.',
            E_USER_ERROR
        );

        return new NullResponse();
    }
}
