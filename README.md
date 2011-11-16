EdpMagicRoute
=============
Version 0.0.1 Created by Evan Coury

Introduction
------------
This module provides basic ZF1-style automatic routing to ZF2.

**This module was developed in response to [this](http://zend-framework-community.634137.n4.nabble.com/Routes-module-controller-tt4039610.html) thread on the ZF-Contributor mailing list. I do not personally use or endorse the use of this module. It is being provided on an AS-IS basis for those who prefer this sort of automatic routing in their applications. My opinion is that routes are generally better off being defined explicitly by modules themselves.**

The logic used when resolving the URL to controller classes is as follows:

    /foo
     - check for 'foo' DI alias
     - Foo\Controller\IndexController::indexAction()
     - Application\Controller\FooController::indexAction()

    /foo-bar
     - check for 'foo-bar' DI alias
     - FooBar\Controller\IndexController::indexAction()
     - Application\Controller\FooBarController::indexAction()

    /foo/bar
     - check for 'foo' DI alias
     - Foo\Controller\BarController::indexAction()
     - Application\Controller\FooController::barAction()

    /foo/bar/baz
     - check for 'foo' DI alias
     - Foo\Controller\BarController::bazAction()

Installation
------------
Currently, in order to override the `default` route in the config, this module
must be enabled **AFTER** your `Application` module in
`configs/application.config.php`.

Limitations
-----------

* This does not yet support `param/value/param/value` parameters.
* This does not prevent modules from overriding the view script paths of other
  modules with controllers of the same name.
