# UHTS - Ultimate Hour Tracking System

Welcome, welcome, to the `illuminate` of the UHTS!

Here, I have made sure to write code in the project *just* how I would've if I was allowed to do whatever the hell I want.

Using the [Laravel Components](https://github.com/illuminate), namely `events`, `routing`, `database` and `console`, I think I did a pretty good job at providing myself with a handy toolset (with `artisan`) and clean code[^1].

---

## The directory structure

### `src/Console`

This directory contains all the commands loaded by `artisan`. Extending the `Command` class in a file makes fit for being loaded through `$artisan->resolve(...)`.

### `src/Controllers`

These are the application controllers. They contain all the mass logic to handle requests entering the application. If not for controllers, I'd have to be writing the logic in the `routes.php` file.

### `src/Models`

This directory contains all the [Eloquent model classes](https://laravel.com/docs/9.x/eloquent). The Eloquent ORM provides an insanely simple implementation of Rails' ActiveRecord for working with your database. Each database table has a corresponding "Model" which is used to interact with that table. Models allow you to query data in your tables, as well as insert new records into the table.

### `src/queries`

This directory isn't special. It contains the queries needed to bootstrap the database. These will be replaced by migrations soon.

---

### `resources/components`

This directory contains the reusable project components. Snippets of commonly used code that shouldn't be copy-pasted into each file reside here.

### `resources/scss`

This directory contains all the stylesheets. They are written in [Sass](https://sass-lang.com) and are compiled at page-load using [scssphp](https://scssphp.github.io/scssphp/).

### `resources/templates`

This directory contains the page templates. Essentially, they contain all the code that each page using the template should have. Once again, a measure to prevent useless copy-pasting of common code everywhere.

### `resources/views`

This directory contains all the viewable pages themselves. They are fetched by Extersia's [`FileViewFinder`](https://github.com/lexisother/GDOS-URS/blob/master/Extersia/View/FileViewFinder.php) which is used by the global [`view()` helper](https://github.com/lexisother/GDOS-URS/blob/f151d0141d45e99336b2b7f3f4c8f2c01750e403/helpers.php#L71-L94). Nothing here is really special, you run `view('home')` and it shows you `resources/views/home.php`. 

---
 
## Requirements

- PHP 8.1.7
- [Composer](https://getcomposer.org/download)
- project dependencies (`composer install`)

## License

See [LICENSE](https://gitlab.com/GildeCoding/yh/21-22/p1.4/135991urs/-/blob/master/LICENSE)

## Footnotes

[^1]: Who wants to write full SQL queries when you can do this?
```php
$employees = Employee::where('actief', 'ja')
               ->orderBy('naam')
               ->take(10)
               ->get();
```
