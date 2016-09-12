# AssetHandler
Super simple asset handler for the laravel framework.

Master.  
[![Build Status](https://travis-ci.org/Johannestegner/AssetHandler.svg?branch=master)](https://travis-ci.org/Johannestegner/AssetHandler)   
Develop.  
[![Build Status](https://travis-ci.org/Johannestegner/AssetHandler.svg?branch=develop)](https://travis-ci.org/Johannestegner/AssetHandler)

### Why?
This package is created for internal projects mainly. You are welcome to use it, contribute or just further develop it.  
The license used is the standard **MIT License** (check the provided LICENSE further down).  
  
The main reason I created this package is cause I needed a asset handler for my projects which followed my personal taste in how it was used.  
I tried a few different ones already existing and thought I could write my own quite fast. Hah... Yeah... Well, a few weeks and a bunch of hours later, here it is!  
Hopefully it will come in handy for others than me.  

### Installation and usage in laravel.

#### Install via composer, require the package.
`composer require johannestegner/assethandler`

Add the Asset handler to the autoloaded service providers array in `app.php`: 

```php

'providers' => [
    // ......      
    JohannesTegner\AssetHandler\AssetHandlerServiceProvider::class,
]
```

Add the facade alias to the class aliases array in `app.php`:

```php
'aliases' => [
    // .......
    'AssetHandler' => Jite\AssetHandler\AssetHandlerFacade::class,
]
```

If wanted, publish the config file via `artisan vendor:publish` and change any necessary settings.  
Thats it, its now installed and the AssetHandler can be used.

### Short example

```php
// add asset to the "scripts" container.
AssetHandler::add('libs/jquery.js', 'jquery', 'scripts');

// Print the script tag.
AssetHandler::print('jquery');
```


### Docs
Further documentation can be found in the Wiki.

### Requirements
AssetHandler currently requires php7.  
A 5.x fork or branch is planned in the near future.

#### Contributions
Johannes Tegnér

#### License

```
MIT License

Copyright (c) 2016 Johannes Tegnér

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
```
