<?php

declare(strict_types=1);

/**
 * Myanmar UI overrides for demo. Keys not listed fall back to English in the seeder.
 */
return array_merge(
    require __DIR__.'/my/part-nav-home.php',
    require __DIR__.'/my/part-admin-monastery.php',
    require __DIR__.'/my/part-content-messages.php',
);
