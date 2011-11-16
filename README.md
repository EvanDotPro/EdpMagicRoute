EdpMagicRoute
=============
Version 0.0.1 Created by Evan Coury

Introduction
------------
This module provides basic ZF1-style automatic routing to ZF2.

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

Limitations
-----------

* This does not yet support `param/value/param/value` parameters.
* This does not prevent modules from overriding the view script paths of other
  modules with controllers of the same name.
