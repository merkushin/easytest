# PHP: The Easy Test

The experimental tool for writing tests easily.

It uses PHP attributes for simpler organising and running tests.

Current implementation is extremely naive and allows writing test using functions. 

Run tests in `./tests` directory with `./vendor/autoload.php` bootstrap script:

```sh
./vendor/bin/php-easyest ./tests ./vendor/autoload.php
```

Tests exmple:
```php
<?php

namespace \TestNamespace\Example1;

#[Fixture]
function num(): int {
    return 1;
}

#[Test]
function test_is_one(int $num): void {
    assert($num === 1);
}

```

You can use `#[Setup]` and `#[TearDown]` attributes to define correspoding functions.

