TYPO3 extension taxajax
=======================

What is does
------------

This extension provides the xAjax PHP Class Library Version 0.2.4. It is
used by other TYPO3 extensions like tt_products.

Usage
-----

In your extension.

TYPO3 11 and later
~~~~~~~~~~~~~~~~~~

You must set this TypoScript to get the absolute image path:

config.absRefPrefix = /

Otherwise an image url “fileadmin/myimage.png” will not be found,
because HTML will generate a path like
“https://example.com/subpage1/subpage2/fileadmin/myimage.png” out of it.

TYPO3 9.5 and later:
~~~~~~~~~~~~~~~~~~~~

Add the following line into the file ext_localconf.php of your extension
my_ext.

.. code:: php

   $GLOBALS['TYPO3_CONF_VARS']['FE']['taxajax_include']['my_ext'] =  \MyDomain\MyExt\Controller\TaxajaxController::class . '::processRequest';

The links generated by typolink must contain this element inside of the
querystring:

.. code:: php

   $addQueryString = [
       'taxajax' => 'my_ext'
   ];

TYPO3 < 9.5:
~~~~~~~~~~~~

For TYPO3 < 9.5 use the eID parameter instead of taxajax

.. code:: php

   $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include']['my_ext'] =  \MyDomain\MyExt\Controller\TaxajaxController::class . '::processRequest';

The links generated by typolink must contain this element inside of the
querystring:

.. code:: php

   $addQueryString = [
       'eID' => 'my_ext'
   ];

License
-------

While xajax itself comes under the GNU LESSER GENERAL PUBLIC LICENSE
(LGPL) the TYPO3 extension version comes under the GNU General Public
License (GPL) The license change is proposed by LGPL Section 3.3 It is
irreversible for any copy or work derived from this. GNU General Public
License can be found at http://www.gnu.org/copyleft/gpl.html. GNU LESSER
General Public License can be found at
http://www.gnu.org/copyleft/lgpl.html. The original XAJAX library can be
found at http://www.xajax-project.org/

TYPO3 adaptions: Elmar Hinz and Franz Holzinger

Documentation
-------------

For documentation visit http://wiki.xajax-project.org/ and read the
TYPO3 manual.odt document file.

xajax PHP Class Library
=======================

The easiest way to develop asynchronous Ajax applications with PHP

| Version 0.2.4 (stable release) Release Notes:
| http://wiki.xajax-project.org/0.2.4_Release_Notes

1. Introduction

xajax is a PHP library that you can include in your PHP scripts to
provide an easy way for Web pages to call PHP functions or object
methods using Ajax (Asynchronous Javascript And XML). Simply register
one or more functions/methods with the xajax object that return a proper
XML response using the supplied response class, add a statement in your
HTML header to print the Javascript include, and run a request processor
prior to outputting any HTML. Then add some simple Javascript function
calls to your HTML, and xajax takes care of the rest!

xajax includes a Javascript object to facilitate the communication
between the browser and the server, and it can also be used as a
Javascript library directly to simplify certain DOM and event
manipulations. However, you can definitely choose to use a dedicated
Javascript “engine” of your liking and integrate it with xajax’s
client/server communication features in a number of ways. More
tightly-coupled integration will be forthcoming in a future version of
xajax.

2. For More Information

The official xajax Web site is located at: http://www.xajax-project.org

Visit the xajax Forums at: http://community.xajax-project.org to keep
track of the latest news and participate in the community discussion.

3. Installation

To run xajax, you need: \* Apache Web Server or IIS for Windows XP/2003
Server (other servers may or may not work and are not supported at this
time) \* PHP 5.5 and later

To install xajax: Unpack the contents of this archive and copy them to
your main Web site folder. Or if you wish, you can put all of the files
in a dedicated “xajax” folder on your Web server (make sure that you
know what that URL is relative your site pages so you can provide xajax
with the correct installed folder URL). Note that the

“thewall” folder in the “examples” folder needs to be writable by the
Web server for that example to function.

4. Documentation

Detailed documentation for the xajax PHP classes is available on our
wiki (URL listed above in section 2), and more is on the way
(particularly in regards to the Javascript component of xajax). Another
good way of learning xajax is to look at the code for the examples and
tests. If you need any help, pop in the forums and ask for assistance
(and the more specific your questions are, the better the answers will
be).

5. Contributing to xajax

xajax is released under the LGPL open source license. If you wish to
contribute to the project or suggest new features, introduce yourself on
the forums or you can e-mail the lead developers at the addresses listed
at the top of this README.

6. Good luck and enjoy!
