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

class Honeypot extends BaseConfig
{
    /**
     * Makes Honeypot visible or not to human.
     */
    public bool $hidden = true;

    /**
     * Honeypot Label Content.
     */
    public string $label = 'Fill This Field';

    /**
     * Honeypot Field Name.
     */
    public string $name = 'honeypot';

    /**
     * Honeypot HTML Template.
     */
    public string $template = '<label>{label}</label><input type="text" name="{name}" value="">';

    /**
     * Honeypot container.
     *
     * If you enabled CSP, you can remove `style="display:none"`.
     */
    public string $container = '<div style="display:none">{template}</div>';

    /**
     * The id attribute for Honeypot container tag.
     *
     * Used when CSP is enabled.
     */
    public string $containerId = 'hpc';
}
