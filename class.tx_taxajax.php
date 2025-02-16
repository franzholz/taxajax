<?php

use TYPO3\CMS\Core\Utility\PathUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/***************************************************************
 *  Copyright notice for implementation of xajax as TYPO3 extension
 *
 *  (c) 2020 Elmar Hinz (elmar.hinz@team-red.net)
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 *   ----------------------------------------------------------------------------
 *   | Online documentation for this class is available on the xajax wiki at:   |
 *   | http://www.xajax-project.org/Documentation:xajax.inc.php                 |
 *   ----------------------------------------------------------------------------
 *
 */


/**
 * The tx_taxajax class generates the xajax javascript for your page including the
 * Javascript wrappers for the PHP functions that you want to call from your page.
 * It also handles processing and executing the command messages in the XML responses
 * sent back to your page from your PHP functions.
 *
 * @package taxajax
 */
class tx_taxajax
{
    /**#@+
     * @access protected
     */
    /**
     * @var array Array of PHP functions that will be callable through javascript wrappers
     */
    public $aFunctions;
    /**
     * @var array Array of object callbacks that will allow Javascript to call PHP methods (key=function name)
     */
    public $aObjects;
    /**
     * @var array Array of RequestTypes to be used with each function (key=function name)
     */
    public $aFunctionRequestTypes;
    /**
     * @var array Array of Include Files for any external functions (key=function name)
     */
    public $aFunctionIncludeFiles;
    /**
     * @var string Name of the PHP function to call if no callable function was found
     */
    public $sCatchAllFunction;
    /**
     * @var string Name of the PHP function to call before any other function
     */
    public $sPreFunction;
    /**
     * @var string The URI for making requests to the xajax object
     */
    public $sRequestURI;
    /**
     * @var string The prefix to prepend to the javascript wraper function name
     */
    public $sWrapperPrefix;
    /**
     * @var boolean Show debug messages (default false)
     */
    public $bDebug;
    /**
     * @var boolean Show messages in the client browser's status bar (default false)
     */
    public $bStatusMessages;
    /**
     * @var boolean Allow xajax to exit after processing a request (default true)
     */
    public $bExitAllowed;
    /**
     * @var boolean Use wait cursor in browser (default true)
     */
    public $bWaitCursor;
    /**
     * @var boolean Use an special xajax error handler so the errors are sent to the browser properly (default false)
     */
    public $bErrorHandler;
    /**
     * @var string Specify what, if any, file xajax should log errors to (and more information in a future release)
     */
    public $sLogFile;
    /**
     * @var boolean Clean all output buffers before outputting response (default false)
     */
    public $bCleanBuffer;
    /**
     * @var string String containing the character encoding used
     */
    public $sEncoding;
    /**
     * @var boolean Decode input request args from UTF-8 (default false)
     */
    public $bDecodeUTF8Input;
    /**
     * @var boolean Convert special characters to HTML entities (default false)
     */
    public $bOutputEntities;
    /**
     * @var array Array for parsing complex objects
     */
    public $aObjArray;
    /**
     * @var integer Position in $aObjArray
     */
    public $iPos;

    /**#@-*/

    /**
     * Constructor. You can set some extra tx_taxajax options right away or use
     * individual methods later to set options.
     *
     * @param string  defaults to the current browser URI
     * @param string  defaults to "taxajax_";
     * @param string  defaults to XAJAX_DEFAULT_CHAR_ENCODING defined above
     * @param boolean defaults to false
     */
    public function __construct($sRequestURI = '', $sWrapperPrefix = 'taxajax_', $sEncoding = XAJAX_DEFAULT_CHAR_ENCODING, $bDebug = false)
    {
        $this->aFunctions = [];
        $this->aObjects = [];
        $this->aFunctionIncludeFiles = [];
        $this->sRequestURI = $sRequestURI;
        if ($this->sRequestURI == '') {
            $this->sRequestURI =
                $GLOBALS['TYPO3_REQUEST']->getAttribute('normalizedParams')->getRequestUri();
        }
        $this->sWrapperPrefix = $sWrapperPrefix;
        $this->bDebug = $bDebug;
        $this->bStatusMessages = false;
        $this->bWaitCursor = true;
        $this->bExitAllowed = true;
        $this->bErrorHandler = false;
        $this->sLogFile = '';
        $this->bCleanBuffer = false;
        $this->setCharEncoding($sEncoding);
        $this->bDecodeUTF8Input = false;
        $this->bOutputEntities = false;
    }

    /**
     * Sets the URI to which requests will be made.
     * <i>Usage:</i> <kbd>$xajax->setRequestURI("http://www.xajax-project.org");</kbd>
     *
     * @param string the URI (can be absolute or relative) of the PHP script
     *               that will be accessed when an xajax request occurs
     */
    public function setRequestURI($sRequestURI): void
    {
        $this->sRequestURI = $sRequestURI;
    }

    /**
     * Sets the prefix that will be appended to the Javascript wrapper
     * functions (default is "taxajax_").
     *
     * @param string
     */
    //
    public function setWrapperPrefix($sPrefix): void
    {
        $this->sWrapperPrefix = $sPrefix;
    }

    /**
     * Enables debug messages for xajax.
     * */
    public function debugOn(): void
    {
        $this->bDebug = true;
    }

    /**
     * Disables debug messages for xajax (default behavior).
     */
    public function debugOff(): void
    {
        $this->bDebug = false;
    }

    /**
     * Enables messages in the browser's status bar for xajax.
     */
    public function statusMessagesOn(): void
    {
        $this->bStatusMessages = true;
    }

    /**
     * Disables messages in the browser's status bar for xajax (default behavior).
     */
    public function statusMessagesOff(): void
    {
        $this->bStatusMessages = false;
    }

    /**
     * Enables the wait cursor to be displayed in the browser (default behavior).
     */
    public function waitCursorOn(): void
    {
        $this->bWaitCursor = true;
    }

    /**
     * Disables the wait cursor to be displayed in the browser.
     */
    public function waitCursorOff(): void
    {
        $this->bWaitCursor = false;
    }

    /**
     * Enables xajax to exit immediately after processing a request and
     * sending the response back to the browser (default behavior).
     */
    public function exitAllowedOn(): void
    {
        $this->bExitAllowed = true;
    }

    /**
     * Disables xajax's default behavior of exiting immediately after
     * processing a request and sending the response back to the browser.
     */
    public function exitAllowedOff(): void
    {
        $this->bExitAllowed = false;
    }

    /**
     * Turns on xajax's error handling system so that PHP errors that occur
     * during a request are trapped and pushed to the browser in the form of
     * a Javascript alert.
     */
    public function errorHandlerOn(): void
    {
        $this->bErrorHandler = true;
    }

    /**
     * Turns off xajax's error handling system (default behavior).
     */
    public function errorHandlerOff(): void
    {
        $this->bErrorHandler = false;
    }

    /**
     * Specifies a log file that will be written to by xajax during a request
     * (used only by the error handling system at present). If you don't invoke
     * this method, or you pass in "", then no log file will be written to.
     * <i>Usage:</i> <kbd>$xajax->setLogFile("/xajax_logs/errors.log");</kbd>
     */
    public function setLogFile($sFilename): void
    {
        $this->sLogFile = $sFilename;
    }

    /**
     * Causes xajax to clean out all output buffers before outputting a
     * response (default behavior).
     */
    public function cleanBufferOn(): void
    {
        $this->bCleanBuffer = true;
    }

    /**
     * Turns off xajax's output buffer cleaning.
     */
    public function cleanBufferOff(): void
    {
        $this->bCleanBuffer = false;
    }

    /**
     * Sets the character encoding for the HTTP output based on
     * <kbd>$sEncoding</kbd>, which is a string containing the character
     * encoding to use. You don't need to use this method normally, since the
     * character encoding for the response gets set automatically based on the
     * <kbd>XAJAX_DEFAULT_CHAR_ENCODING</kbd> constant.
     * <i>Usage:</i> <kbd>$xajax->setCharEncoding("utf-8");</kbd>
     *
     * @param string the encoding type to use (utf-8, iso-8859-1, etc.)
     */
    public function setCharEncoding($sEncoding): void
    {
        $this->sEncoding = $sEncoding;
    }

    /**
     * Gets the previously set character encoding for the HTTP output
     * @see: setCharEncoding
     */
    public function getCharEncoding()
    {
        return $this->sEncoding;
    }

    /**
     * Causes xajax to decode the input request args from UTF-8 to the current
     * encoding if possible. Either the iconv or mb_string extension must be
     * present for optimal functionality.
     */
    public function decodeUTF8InputOn(): void
    {
        $this->bDecodeUTF8Input = true;
    }

    /**
     * Turns off decoding the input request args from UTF-8 (default behavior).
     */
    public function decodeUTF8InputOff(): void
    {
        $this->bDecodeUTF8Input = false;
    }

    /**
     * Tells the response object to convert special characters to HTML entities
     * automatically (only works if the mb_string extension is available).
     */
    public function outputEntitiesOn(): void
    {
        $this->bOutputEntities = true;
    }

    /**
     * Tells the response object to output special characters intact. (default
     * behavior).
     */
    public function outputEntitiesOff(): void
    {
        $this->bOutputEntities = false;
    }

    /**
     * Registers a PHP function or method to be callable through xajax in your
     * Javascript. If you want to register a function, pass in the name of that
     * function. If you want to register a static class method, pass in an
     * array like so:
     * <kbd>["myFunctionName", "myClass", "myMethod"]</kbd>
     * For an object instance method, use an object variable for the second
     * array element (and in PHP 4 make sure you put an & before the variable
     * to pass the object by reference). Note: the function name is what you
     * call via Javascript, so it can be anything as long as it doesn't
     * conflict with any other registered function name.
     *
     * <i>Usage:</i> <kbd>$xajax->registerFunction("myFunction");</kbd>
     * or: <kbd>$xajax->registerFunction(["myFunctionName", &$myObject, "myMethod"]);</kbd>
     *
     * @param mixed  contains the function name or an object callback array
     * @param mixed  request type (XAJAX_GET/XAJAX_POST) that should be used
     *               for this function.  Defaults to XAJAX_POST.
     */
    public function registerFunction($mFunction, $sRequestType = XAJAX_POST): void
    {
        if (is_array($mFunction)) {
            $this->aFunctions[$mFunction[0]] = 1;
            $this->aFunctionRequestTypes[$mFunction[0]] = $sRequestType;
            $this->aObjects[$mFunction[0]] = array_slice($mFunction, 1);
        } else {
            $this->aFunctions[$mFunction] = 1;
            $this->aFunctionRequestTypes[$mFunction] = $sRequestType;
        }
    }

    /**
     * Registers a PHP function to be callable through xajax which is located
     * in some other file.  If the function is requested the external file will
     * be included to define the function before the function is called.
     *
     * <i>Usage:</i> <kbd>$xajax->registerExternalFunction("myFunction","myFunction.inc.php",XAJAX_POST);</kbd>
     *
     * @param string contains the function name or an object callback array
     *               ({@link xajax::registerFunction() see registerFunction} for
     *               more info on object callback arrays)
     * @param string contains the path and filename of the include file
     * @param mixed  the RequestType (XAJAX_GET/XAJAX_POST) that should be used
     *		          for this function. Defaults to XAJAX_POST.
     */
    public function registerExternalFunction($mFunction, $sIncludeFile, $sRequestType = XAJAX_POST): void
    {
        $this->registerFunction($mFunction, $sRequestType);

        if (is_array($mFunction)) {
            $this->aFunctionIncludeFiles[$mFunction[0]] = $sIncludeFile;
        } else {
            $this->aFunctionIncludeFiles[$mFunction] = $sIncludeFile;
        }
    }

    /**
     * Registers a PHP function to be called when xajax cannot find the
     * function being called via Javascript. Because this is technically
     * impossible when using "wrapped" functions, the catch-all feature is
     * only useful when you're directly using the xajax.call() Javascript
     * method. Use the catch-all feature when you want more dynamic ability to
     * intercept unknown calls and handle them in a custom way.
     *
     * <i>Usage:</i> <kbd>$xajax->registerCatchAllFunction("myCatchAllFunction");</kbd>
     *
     * @param string contains the function name or an object callback array
     *               ({@link taxajax::registerFunction() see registerFunction} for
     *               more info on object callback arrays)
     */
    public function registerCatchAllFunction($mFunction): void
    {
        if (is_array($mFunction)) {
            $this->sCatchAllFunction = $mFunction[0];
            $this->aObjects[$mFunction[0]] = array_slice($mFunction, 1);
        } else {
            $this->sCatchAllFunction = $mFunction;
        }
    }

    /**
     * Registers a PHP function to be called before taxajax calls the requested
     * function. xajax will automatically add the request function's response
     * to the pre-function's response to create a single response. Another
     * feature is the ability to return not just a response, but an array with
     * the first element being false (a boolean) and the second being the
     * response. In this case, the pre-function's response will be returned to
     * the browser without xajax calling the requested function.
     *
     * <i>Usage:</i> <kbd>$xajax->registerPreFunction("myPreFunction");</kbd>
     *
     * @param string contains the function name or an object callback array
     *               ({@link taxajax::registerFunction() see registerFunction} for
     *               more info on object callback arrays)
     */
    public function registerPreFunction($mFunction): void
    {
        if (is_array($mFunction)) {
            $this->sPreFunction = $mFunction[0];
            $this->aObjects[$mFunction[0]] = array_slice($mFunction, 1);
        } else {
            $this->sPreFunction = $mFunction;
        }
    }

    /**
     * Returns true if xajax can process the request, false if otherwise.
     * You can use this to determine if xajax needs to process the request or
     * not.
     *
     * @return boolean
     */
    public function canProcessRequests(): bool
    {
        if ($this->getRequestMode() != -1) {
            return true;
        }
        return false;
    }

    /**
     * Returns the current request mode (XAJAX_GET or XAJAX_POST), or -1 if
     * there is none.
     *
     * @return mixed
     */
    public function getRequestMode()
    {
        if (!empty($_GET['xajax'])) {
            return XAJAX_GET;
        }

        if (!empty($_POST['xajax'])) {
            return XAJAX_POST;
        }

        return -1;
    }

    /**
     * This is the main communications engine of xajax. The engine handles all
     * incoming xajax requests, calls the apporiate PHP functions (or
     * class/object methods) and passes the XML responses back to the
     * Javascript response handler. If your RequestURI is the same as your Web
     * page then this function should be called before any headers or HTML has
     * been sent.
     */
    public function processRequests(): void
    {
        $requestMode = -1;
        $sFunctionName = '';
        $bFoundFunction = true;
        $bFunctionIsCatchAll = false;
        $sFunctionNameForSpecial = '';
        $aArgs = [];
        $sPreResponse = '';
        $bEndRequest = false;
        $sResponse = '';

        $requestMode = $this->getRequestMode();
        if ($requestMode == -1) {
            return;
        }

        if ($requestMode == XAJAX_POST) {
            $sFunctionName = $_POST['xajax'];

            if (!empty($_POST['xajaxargs'])) {
                $aArgs = $_POST['xajaxargs'];
            }
        } else {
            header('Expires: Mon, 10 Oct 2029 05:00:00 GMT');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
            header('Cache-Control: no-cache, must-revalidate');
            header('Pragma: no-cache');

            $sFunctionName = $_GET['xajax'];

            if (!empty($_GET['xajaxargs'])) {
                $aArgs = $_GET['xajaxargs'];
            }
        }

        // Use xajax error handler if necessary
        if ($this->bErrorHandler) {
            $GLOBALS['xajaxErrorHandlerText'] = '';
            set_error_handler('xajaxErrorHandler');
        }

        if ($this->sPreFunction) {
            if (!$this->_isFunctionCallable($this->sPreFunction)) {
                $bFoundFunction = false;
                $objResponse = new tx_taxajax_response();
                $objResponse->addAlert('Unknown Pre-Function ' . $this->sPreFunction);
                $sResponse = $objResponse->getXML();
            }
        }
        //include any external dependencies associated with this function name
        if (array_key_exists($sFunctionName, $this->aFunctionIncludeFiles)) {
            ob_start();
            include_once($this->aFunctionIncludeFiles[$sFunctionName]);
            ob_end_clean();
        }

        if ($bFoundFunction) {
            $sFunctionNameForSpecial = $sFunctionName;
            if (!array_key_exists($sFunctionName, $this->aFunctions)) {
                if ($this->sCatchAllFunction) {
                    $sFunctionName = $this->sCatchAllFunction;
                    $bFunctionIsCatchAll = true;
                } else {
                    $bFoundFunction = false;
                    $objResponse = new tx_taxajax_response();
                    $objResponse->addAlert('Unknown Function ' . $sFunctionName);
                    $sResponse = $objResponse->getXML();
                }
            } elseif ($this->aFunctionRequestTypes[$sFunctionName] != $requestMode) {
                $bFoundFunction = false;
                $objResponse = new tx_taxajax_response();
                $objResponse->addAlert('Incorrect Request Type.');
                $sResponse = $objResponse->getXML();
            }
        }

        if ($bFoundFunction) {
            for ($i = 0; $i < sizeof($aArgs); $i++) {
                if (stristr($aArgs[$i], '<xjxobj>') != false) {
                    $aArgs[$i] = $this->_xmlToArray('xjxobj', $aArgs[$i]);
                } elseif (stristr($aArgs[$i], '<xjxquery>') != false) {
                    $aArgs[$i] = $this->_xmlToArray('xjxquery', $aArgs[$i]);
                } elseif ($this->bDecodeUTF8Input) {
                    $aArgs[$i] = $this->_decodeUTF8Data($aArgs[$i]);
                }
            }

            if ($this->sPreFunction) {
                $mPreResponse =
                    $this->_callFunction(
                        $this->sPreFunction,
                        [$sFunctionNameForSpecial, $aArgs]
                    );

                if (is_array($mPreResponse) && $mPreResponse[0] === false) {
                    $bEndRequest = true;
                    $sPreResponse = $mPreResponse[1];
                } else {
                    $sPreResponse = $mPreResponse;
                }

                if (is_a($sPreResponse, 'tx_taxajax_response')) {
                    $sPreResponse = $sPreResponse->getXML();
                }

                if ($bEndRequest) {
                    $sResponse = $sPreResponse;
                }
            }

            if (!$bEndRequest) {
                if (!$this->_isFunctionCallable($sFunctionName)) {
                    $objResponse = new tx_taxajax_response();
                    $objResponse->addAlert('The Registered Function ' . $sFunctionName . ' Could Not Be Found.');
                    $sResponse = $objResponse->getXML();
                } else {
                    if ($bFunctionIsCatchAll) {
                        $aArgs = [$sFunctionNameForSpecial, $aArgs];
                    }
                    $sResponse = $this->_callFunction($sFunctionName, $aArgs);
                }
                if (is_a($sResponse, 'tx_taxajax_response')) {
                    $sResponse = $sResponse->getXML();
                }
                if (!is_string($sResponse) || strpos($sResponse, '<xjx>') === false) {
                    $objResponse = new tx_taxajax_response();
                    $objResponse->addAlert('No XML Response Was Returned By Function ß' . $sFunctionName);
                    $sResponse = $objResponse->getXML();
                } elseif ($sPreResponse != '') {
                    $sNewResponse = new tx_taxajax_response($this->sEncoding, $this->bOutputEntities);
                    $sNewResponse->loadXML($sPreResponse);
                    $sNewResponse->loadXML($sResponse);
                    $sResponse = $sNewResponse->getXML();
                }
            }
        }

        $sContentHeader = 'Content-type: text/xml;';
        if ($this->sEncoding && strlen(trim($this->sEncoding)) > 0) {
            $sContentHeader .= ' charset=' . $this->sEncoding;
        }
        header($sContentHeader);
        if ($this->bErrorHandler && !empty($GLOBALS['xajaxErrorHandlerText'])) {
            $sErrorResponse = new tx_taxajax_response();
            $sErrorResponse->addAlert('** PHP Error Messages: **' . $GLOBALS['xajaxErrorHandlerText']);
            if ($this->sLogFile) {
                $fH = @fopen($this->sLogFile, 'a');
                if (!$fH) {
                    $sErrorResponse->addAlert('** Logging Error **\n\ntaxajax was unable to write to the error log file:\n' . $this->sLogFile);
                } else {
                    fwrite($fH, '** taxajax Error Log - ' . strftime('%b %e %Y %I:%M:%S %p') . ' **' . $GLOBALS['xajaxErrorHandlerText'] . '\n\n\n');
                    fclose($fH);
                }
            }

            $sErrorResponse->loadXML($sResponse);
            $sResponse = $sErrorResponse->getXML();
        }

        if ($this->bCleanBuffer) {
            while (@ob_end_clean());
        }

        print $sResponse;

        if ($this->bErrorHandler) {
            restore_error_handler();
        }

        if ($this->bExitAllowed) {
            exit();
        }
    }

    /**
     * Prints the xajax Javascript header and wrapper code into your page by
     * printing the output of the getJavascript() method. It should only be
     * called between the <pre><head> </head></pre> tags in your HTML page.
     * Remember, if you only want to obtain the result of this function, use
     * {@link xajax::getJavascript()} instead.
     *
     * <i>Usage:</i>
     * <code>
     *  <head>
     *		...
     *		< ?php $xajax->printJavascript(); ? >
     * </code>
     *
     * @param string the relative address of the folder where xajax has been
     *               installed. For instance, if your PHP file is
     *               "http://www.myserver.com/myfolder/mypage.php"
     *               and xajax was installed in
     *               "http://www.myserver.com/anotherfolder", then $sJsURI
     *               should be set to "../anotherfolder". Defaults to assuming
     *               xajax is in the same folder as your PHP file.
     * @param string the relative folder/file pair of the xajax Javascript
     *               engine located within the xajax installation folder.
     *               Defaults to Resources/Public/JavaScript/xajax.js.
     */
    public function printJavascript($sJsURI = '', $sJsFile = null): void
    {
        print $this->getJavascript($sJsURI, $sJsFile);
    }

    /**
     * Returns the xajax Javascript code that should be added to your HTML page
     * between the <kbd><head> </head></kbd> tags.
     *
     * <i>Usage:</i>
     * <code>
     *  < ?php $xajaxJSHead = $xajax->getJavascript(); ? >
     *	<head>
     *		...
     *		< ?php echo $xajaxJSHead; ? >
     * </code>
     *
     * @param string the relative address of the folder where xajax has been
     *               installed. For instance, if your PHP file is
     *               "http://www.myserver.com/myfolder/mypage.php"
     *               and xajax was installed in
     *               "http://www.myserver.com/anotherfolder", then $sJsURI
     *               should be set to "../anotherfolder". Defaults to assuming
     *               xajax is in the same folder as your PHP file.
     * @param string the relative folder/file pair of the xajax Javascript
     *               engine located within the xajax installation folder.
     *               Defaults to Resources/Public/JavaScript/xajax.js.
     * @return string
     */
    public function getJavascript($sJsURI = '', $sJsFile = null)
    {
        $html = $this->getJavascriptConfig();
        $html .= $this->getJavascriptInclude($sJsURI, $sJsFile);

        return $html;
    }

    /**
     * Returns a string containing inline Javascript that sets up the xajax
     * runtime (typically called internally by xajax from get/printJavascript).
     *
     * @return string
     */
    public function getJavascriptConfig()
    {
        $html  = chr(9) . '<script>' . chr(13);
        $html .= '/*<![CDATA[*/' . chr(13);
        $html .= 'var xajaxRequestUri="' . $this->sRequestURI . '";' . chr(13);
        $html .= 'var xajaxDebug=' . ($this->bDebug ? 'true' : 'false') . ';' . chr(13);
        $html .= 'var xajaxStatusMessages=' . ($this->bStatusMessages ? 'true' : 'false') . ';' . chr(13);
        $html .= 'var xajaxWaitCursor=' . ($this->bWaitCursor ? 'true' : 'false') . ';' . chr(13);
        $html .= 'var xajaxDefinedGet=' . XAJAX_GET . ';' . chr(13);
        $html .= 'var xajaxDefinedPost=' . XAJAX_POST . ';' . chr(13);
        $html .= 'var xajaxLoaded=false;' . chr(13);

        foreach($this->aFunctions as $sFunction => $bExists) {
            $html .= $this->_wrap($sFunction, $this->aFunctionRequestTypes[$sFunction]);
        }

        $html .= '/*]]>*/' . chr(13);
        $html .= chr(9)  . '</script>' . chr(13);
        return $html;
    }

    /**
     * Returns a string containing a Javascript include of the xajax.js file
     * along with a check to see if the file loaded after six seconds
     * (typically called internally by xajax from get/printJavascript).
     *
     * @param string the relative address of the folder where xajax has been
     *               installed. For instance, if your PHP file is
     *               "http://www.myserver.com/myfolder/mypage.php"
     *               and xajax was installed in
     *               "http://www.myserver.com/anotherfolder", then $sJsURI
     *               should be set to "../anotherfolder". Defaults to assuming
     *               xajax is in the same folder as your PHP file.
     * @param string the relative folder/file pair of the xajax Javascript
     *               engine located within the xajax installation folder.
     *               Defaults to Resources/Public/JavaScript/xajax.js.
     * @return string
     */
    public function getJavascriptInclude($sJsURI = '', $sJsFile = null)
    {
        $useDefaultFile = false;
        if ($sJsURI == null) {
            $sJsURI =
                PathUtility::stripPathSitePrefix(
                    ExtensionManagementUtility::extPath(TAXAJAX_EXT)
                );
        }
        if ($sJsFile == null) {
            $useDefaultFile = true;
            $sJsFile = 'Resources/Public/JavaScript/xajax.js';
        }

        if ($sJsURI != '' && substr($sJsURI, -1) != '/') {
            $sJsURI .= '/';
        }

        if ($useDefaultFile && !file_exists($sJsURI . $sJsFile)) {
            $sJsFile = 'Resources/Public/JavaScript/xajax_uncompressed.js';
        }

        $html = chr(9) . '<script src="' . $sJsURI . $sJsFile . '"></script>' . chr(13);
        $html .= chr(9) . '<script>' . chr(13);
        $html .=
'window.setTimeout(
	function () {
		if (!xajaxLoaded) {
			alert(\'Error: The xajax Javascript file could not be included. \nPerhaps the following URL is incorrect?\n' . $sJsURI . $sJsFile . '\');
		}
	},
	6000
);' . chr(13);
        $html .= chr(9) . '</script>' . chr(13);
        return $html;
    }

    /**
     * This method can be used to create a new xajax.js file out of the
     * xajax_uncompressed.js file (which will only happen if xajax.js doesn't
     * already exist on the filesystem).
     *
     * TYPO3 example:
     * 		$taxajax = GeneralUtility::makeInstance('tx_taxajax');
     *      $taxajax->autoCompressJavascript(null, true);
     *
     * @param string an optional argument containing the full server file path
     *               of xajax.js.
     * @param bAlways - (boolean):  Compress the file, even if it already exists.
     */
    public function autoCompressJavascript($sJsFullFilename = null, $bAlways = false): void
    {
        $sJsFile = 'Resources/Public/JavaScript/xajax.js';

        if ($sJsFullFilename) {
            $realJsFile = $sJsFullFilename;
        } else {
            $realPath = realpath(__DIR__);
            $realJsFile = $realPath . '/' . $sJsFile;
        }

        // Create a compressed file if necessary
        if (!file_exists($realJsFile) || true == $bAlways) {
            $srcFile = str_replace('.js', '_uncompressed.js', $realJsFile);
            if (!file_exists($srcFile)) {
                trigger_error('The xajax uncompressed Javascript file could not be found in the <b>' . dirname($realJsFile) . '</b> folder. Error ', E_USER_ERROR);
            }

            $javaScript = implode('', file($srcFile));
            $compressedScript = xajaxCompress::xajaxCompressJavascript($javaScript);
            $fH = @fopen($realJsFile, 'w');

            if (!$fH) {
                trigger_error('The xajax compressed javascript file could not be written in the <b>' . dirname($realJsFile) . '</b> folder. Error ', E_USER_ERROR);
            } else {
                fwrite($fH, $compressedScript);
                fclose($fH);
            }
        }
    }

    /**
     * Returns true if the function name is associated with an object callback,
     * false if not.
     *
     * @param string the name of the function
     * @access private
     * @return boolean
     */
    public function _isObjectCallback($sFunction): bool
    {
        if (array_key_exists($sFunction, $this->aObjects)) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if the function or object callback can be called, false if
     * not.
     *
     * @param string the name of the function
     * @access private
     * @return boolean
     */
    public function _isFunctionCallable($sFunction)
    {
        if ($this->_isObjectCallback($sFunction)) {
            if (is_object($this->aObjects[$sFunction][0])) {
                return method_exists($this->aObjects[$sFunction][0], $this->aObjects[$sFunction][1]);
            } else {
                return is_callable($this->aObjects[$sFunction]);
            }
        } else {
            return function_exists($sFunction);
        }
    }

    /**
     * Calls the function, class method, or object method with the supplied
     * arguments.
     *
     * @param string the name of the function
     * @param array  arguments to pass to the function
     * @access private
     * @return mixed the output of the called function or method
     */
    public function _callFunction($sFunction, $aArgs)
    {
        if ($this->_isObjectCallback($sFunction)) {
            $mReturn = call_user_func_array($this->aObjects[$sFunction], $aArgs);
        } else {
            $mReturn = call_user_func_array($sFunction, $aArgs);
        }
        return $mReturn;
    }

    /**
     * Generates the Javascript wrapper for the specified PHP function.
     *
     * @param string the name of the function
     * @param mixed  the request type
     * @access private
     * @return string
     */
    public function _wrap($sFunction, $sRequestType = XAJAX_POST)
    {
        $js = 'function ' . $this->sWrapperPrefix . $sFunction . '(){return xajax.call("' . $sFunction . '", arguments, ' . $sRequestType . ');}' . chr(13);
        return $js;
    }

    /**
     * Takes a string containing xajax xjxobj XML or xjxquery XML and builds an
     * array representation of it to pass as an argument to the PHP function
     * being called.
     *
     * @param string the root tag of the XML
     * @param string XML to convert
     * @access private
     * @return array
     */
    public function _xmlToArray($rootTag, $sXml)
    {
        $aArray = [];
        $sXml = str_replace('<' . $rootTag . '>', '<' . $rootTag . '>|~|', $sXml);
        $sXml = str_replace('</' . $rootTag . '>', '</' . $rootTag . '>|~|', $sXml);
        $sXml = str_replace('<e>', '<e>|~|', $sXml);
        $sXml = str_replace('</e>', '</e>|~|', $sXml);
        $sXml = str_replace('<k>', '<k>|~|', $sXml);
        $sXml = str_replace('</k>', '|~|</k>|~|', $sXml);
        $sXml = str_replace('<v>', '<v>|~|', $sXml);
        $sXml = str_replace('</v>', '|~|</v>|~|', $sXml);
        $sXml = str_replace('<q>', '<q>|~|', $sXml);
        $sXml = str_replace('</q>', '|~|</q>|~|', $sXml);

        $this->aObjArray = explode('|~|', $sXml);

        $this->iPos = 0;
        $aArray = $this->_parseObjXml($rootTag);

        return $aArray;
    }

    /**
     * A recursive function that generates an array from the contents of
     * $this->aObjArray.
     *
     * @param string the root tag of the XML
     * @access private
     * @return array
     */
    public function _parseObjXml($rootTag)
    {
        $aArray = [];

        if ($rootTag == 'xjxobj') {
            while(!stristr($this->aObjArray[$this->iPos], '</xjxobj>')) {
                $this->iPos++;
                if(stristr($this->aObjArray[$this->iPos], '<e>')) {
                    $key = '';
                    $value = null;

                    $this->iPos++;
                    while(!stristr($this->aObjArray[$this->iPos], '</e>')) {
                        if(stristr($this->aObjArray[$this->iPos], '<k>')) {
                            $this->iPos++;
                            while(!stristr($this->aObjArray[$this->iPos], '</k>')) {
                                $key .= $this->aObjArray[$this->iPos];
                                $this->iPos++;
                            }
                        }

                        if(stristr($this->aObjArray[$this->iPos], '<v>')) {
                            $this->iPos++;
                            while(!stristr($this->aObjArray[$this->iPos], '</v>')) {
                                if(stristr($this->aObjArray[$this->iPos], '<xjxobj>')) {
                                    $value = $this->_parseObjXml('xjxobj');
                                    $this->iPos++;
                                } else {
                                    $value .= $this->aObjArray[$this->iPos];
                                    if ($this->bDecodeUTF8Input) {
                                        $value = $this->_decodeUTF8Data($value);
                                    }
                                }
                                $this->iPos++;
                            }
                        }
                        $this->iPos++;
                    }

                    $aArray[$key] = $value;
                }
            }
        }

        if ($rootTag == 'xjxquery') {
            $sQuery = '';
            $this->iPos++;
            while(!stristr($this->aObjArray[$this->iPos], '</xjxquery>')) {
                if (
                    stristr($this->aObjArray[$this->iPos], '<q>') ||
                    stristr($this->aObjArray[$this->iPos], '</q>')
                ) {
                    $this->iPos++;
                    continue;
                }
                $sQuery .= $this->aObjArray[$this->iPos];
                $this->iPos++;
            }

            parse_str($sQuery, $aArray);
            if ($this->bDecodeUTF8Input) {
                foreach($aArray as $key => $value) {
                    $aArray[$key] = $this->_decodeUTF8Data($value);
                }
            }
        }

        return $aArray;
    }

    /**
     * Decodes string data from UTF-8 to the current xajax encoding.
     *
     * @param string data to convert
     * @access private
     * @return string converted data
     */
    public function _decodeUTF8Data($sData)
    {
        $sValue = $sData;

        if ($this->bDecodeUTF8Input) {
            if (is_string($sValue)) {
                $sValue = mb_convert_encoding($sValue, $this->sEncoding, 'UTF-8');
            }
        }
        return $sValue;
    }

}// end class xajax

/**
 * This function is registered with PHP's set_error_handler() function if
 * the xajax error handling system is turned on.
 */
function xajaxErrorHandler($errno, $errstr, $errfile, $errline): void
{
    $errorReporting = error_reporting();
    if (($errno & $errorReporting) == 0) {
        return;
    }

    if ($errno == E_NOTICE) {
        $errTypeStr = 'NOTICE';
    } elseif ($errno == E_WARNING) {
        $errTypeStr = 'WARNING';
    } elseif ($errno == E_USER_NOTICE) {
        $errTypeStr = 'USER NOTICE';
    } elseif ($errno == E_USER_WARNING) {
        $errTypeStr = 'USER WARNING';
    } elseif ($errno == E_USER_ERROR) {
        $errTypeStr = 'USER FATAL ERROR';
    } elseif (defined('E_STRICT') && $errno == E_STRICT) {
        return;
    } else {
        $errTypeStr = 'UNKNOWN:' . $errno;
    }
    $sCrLf = "\n";

    ob_start();
    echo $GLOBALS['xajaxErrorHandlerText'];
    echo $sCrLf;
    echo '----';
    echo $sCrLf;
    echo '[';
    echo $errTypeStr;
    echo '] ';
    echo $errstr;
    echo $sCrLf;
    echo 'Error on line ';
    echo $errline;
    echo ' of file ';
    echo $errfile;
    $GLOBALS['xajaxErrorHandlerText'] = ob_get_clean();
}
