# Architecture

This document outlines a bird's eye view of the code structure in this repository.

The Seymour RSS client is built on the [TALL Stack](https://tallstack.dev/):

- [Tailwind CSS](https://tailwindcss.com/) A CSS framework emphasizing design composition with utility classes rather than traditional cascading inheritance.
- [Alpine.js](https://github.com/alpinejs/alpine) A lightweight javascript framework with a declarative syntax and a robust API; ideal for UI work.
- [Laravel](https://laravel.com/) A 'batteries included' framework for building PHP applications. An excellent way to work efficiently with PHP and get things done.
- [Livewire](https://laravel-livewire.com/) A tool for creating rich interactive application components without writing custom javascript. Similar to Phoenix LiveView.

As a Laravel/PHP application; this project follows most of the conventions defined as 'best practices' by the Laravel community.

Here is a breakdown of the architecture that is specific to this application:

## Feeds

In this application a `Feed` represents parsed RSS content that has been fetched from the web. An `Entry` represents a single episode or post within a `Feed`.

When the XML content of a feed has been fetched, the `Interpreter` classifies the `Variant` of RSS being parsed: Atom, RSS 1.0, RSS 2.0, etc. The variant will then dictate which parser class will be used to parse the XML and instantiate the `Feed` and `Entry` classes that represent that feed's content.

Each RSS variant is slightly different.  The `Feed` and `Entry` classes are set up as a composite version of all of them; they are intended to represent a Platonic Ideal.  The variant parsers are responsible for converting their feed and entry content into this idealized format.  When a variant has elements that are not easily translated into a common structure those elements are stored in an `extras` array that is meant to be a loosely organized container of ancillary content.

Each variant parser is also responsible for interpreting namespaced elements that may or may not be present in the XML. Initial support for interpreting namespaced content is minimal; this will be fleshed out more over time.

## Subscriptions & Articles

A `Subscription` is a model of a database record that represents a user's subscription to a feed. An `Article` is a model of a database record that represents an entry from a feed that is available for a user to view.

When a user subscribes to a feed for the first time, the system will fetch the `Feed` and `Entry` content, as described above, and save them to the database as a `Subscription` that belongs to the user, and `Article` records that belong to the subscription.

When the system checks for new content it loops through a user's subscriptions, checks each feed for new content and stores the additional `Article` records as appropriate.

For the most part a `Subscription` and a `Feed` have very similar attributes, but only the `Subscription` is stored in the database. Same goes for an `Entry` and an `Article`; only the `Article` is stored in the database.  Any "extra" content that is retrieved from a feed or an entry that does not have a dedicated table column will be stored as JSON content in a column called `extra`.

## User Management

This project uses [Jetstream](https://jetstream.laravel.com/) as a scaffolding for user management and authentication.

## Actions

Actions are a convenient way to bundle domain specific code into re-usable classes and isolate them from the parts of an application that have nothing to do with domain logic. This project uses the [stagerightlabs/actions](https://github.com/stagerightlabs/actions/) package to unify all action classes with a common API. The only action classes that do not make use of this interface are the ones that come from Jetstream.

More information about the Action pattern can be found here:

- [Brent Roose - Actions](https://stitcher.io/blog/laravel-beyond-crud-03-actions)
- [Brent Roose - Domain Oriented Laravel](https://stitcher.io/blog/laravel-beyond-crud-01-domain-oriented-laravel)
- [Freek Van der Herten - Refactoring to Actions](https://freek.dev/1371-refactoring-to-actions)

## Infrastructure

This application is intended to be run within a Docker container. The official distributions will be available as containers on Docker Hub. As a Laravel application it can be run without Docker rather easily, but that strategy of deployment will not be supported.

The local development environment also makes use of Docker and Docker Compose; see the readme for more information.
