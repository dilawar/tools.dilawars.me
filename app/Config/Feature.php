<?php

/*
 * This file is part of the proprietary project.
 *
 * This file and its contents are confidential and protected by copyright law.
 * Unauthorized copying, distribution, or disclosure of this content
 * is strictly prohibited without prior written consent from the author or
 * copyright owner.
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Enable/disable backward compatibility breaking features.
 */
class Feature extends BaseConfig
{
    /**
     * Use improved new auto routing instead of the legacy version.
     */
    public bool $autoRoutesImproved = true;

    /**
     * Use filter execution order in 4.4 or before.
     */
    public bool $oldFilterOrder = false;

    /**
     * The behavior of `limit(0)` in Query Builder.
     *
     * If true, `limit(0)` returns all records. (the behavior of 4.4.x or before in version 4.x.)
     * If false, `limit(0)` returns no records. (the behavior of 3.1.9 or later in version 3.x.)
     */
    public bool $limitZeroAsAll = true;

    /**
     * Use strict location negotiation.
     *
     * By default, the locale is selected based on a loose comparison of the language code (ISO 639-1)
     * Enabling strict comparison will also consider the region code (ISO 3166-1 alpha-2).
     */
    public bool $strictLocaleNegotiation = false;
}
