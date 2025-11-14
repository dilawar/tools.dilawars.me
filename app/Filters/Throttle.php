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

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Throttle implements FilterInterface
{
    /**
     * Using the Throttler class to implement rate limiting for your application.
     *
     * @param list<string>|null $arguments
     *
     * @return ResponseInterface|null
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $throttler = service('throttler');
        // Rate limit tool usage 5 times a minutes (max).
        $uriPath = $request->getUri()->getPath();
        if (str_starts_with($uriPath, '/tools')) {
            log_message('debug', 'uri is '.$uriPath);
            // Ensure that one does not use more than 5 times a minutes.
            if (false === $throttler->check(md5($request->getIPAddress()), 5, MINUTE)) {
                $body = "<h3 class='text-warning'>Too many requests. This service allows maximum 5 usage of any tools per minutes. Try again in some time.</h3>";

                return service('response')->setStatusCode(429)->setBody($body);
            }
        }

        // Restrict an IP address to no more than 1 request
        // per second across the entire site.
        if (false === $throttler->check(md5($request->getIPAddress()), 60, MINUTE)) {
            $body = "<h3 class='text-warning'>Too many requests. This free service allows a maximum of 60 requests per minute.</h3>";

            return service('response')->setStatusCode(429);
        }

        return null;
    }

    /**
     * We don't have anything to do here.
     *
     * @param list<string>|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): null
    {
        return null;
    }
}
