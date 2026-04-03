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

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('home', [
            'page_title'       => 'Free Online Tools — QR Code Generator, Image Converter, PDF & OCR',
            'page_description' => 'Free, private, browser-based tools: generate QR codes, convert and compress images, convert PDF to JPG, extract text with OCR, and plan running routes. No sign-up required.',
        ]);
    }
}
