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

namespace App\Data;

use App\Models\KvStore;

/**
 * App statistics.
 *
 * These are stored inside a database table kv_store which is modelled by
 * `App\Models\KvStore`.
 */
enum StatsName: string
{
    case TotalImageConvcersions = 'stat_total_image_conversions';
    case TotalImageCompressed = 'state_total_image_compressed';
    case TotalQrGenerated = 'state_total_qr_generated';

    public function get(string $subkey = ''): ?string
    {
        return model(KvStore::class)->select('value_int')
            ->where('key_name', $this->value)
            ->where('key_subname', $subkey)
            ->first()['value_int'] ?? null;
    }

    public function increment(string $subkey = '', int $by = 1): void
    {
        $keyName = $this->value;
        log_message('info', sprintf('Increasing kv_store %s.%s by %d.', $keyName, $subkey, $by));

        $oldValue = $this->get($subkey);

        $data = [
            'key_name' => $keyName,
            'key_subname' => $subkey,
            'value_int' => 0,
        ];

        if (is_null($oldValue)) {
            model(KvStore::class)->insert($data);
            $oldValue = 0;
        }

        model(KvStore::class)
            ->where('key_name', $keyName)
            ->where('key_subname', $subkey)
            ->set('value_int', $oldValue + $by)
            ->update();

    }

    public static function initialize(): void
    {
        log_message('info', 'Seeding/Initializing StatsName values');
        foreach (self::cases() as $item) {
            $data = [
                'key_name' => $item->value,
                'value_int' => 0,
            ];

            // @phpstan-ignore method.notFound
            model(KvStore::class)->ignore(true)->insert($data);
        }
    }

    /**
     * Statistics table.
     */
    public static function table(): string
    {
        $html = ['<div class="row d-flex justify-content-between">'];
        foreach (self::cases() as $item) {
            $html[] = "<div class='col-4 col-sm-3'>";
            $html[] = "<div class='badge badge-info bg-info'>"
                .$item->label()
                .' <strong>'
                .$item->get()
                .'</strong></div>';
            $html[] = '</div>';
        }

        $html[] = '</div>';

        return implode(' ', $html);
    }

    private function label(): string
    {
        return match($this) {
            self::TotalImageConvcersions => '#Images Converted',
            self::TotalImageCompressed => '#Images Compressed',
            self::TotalQrGenerated => '#QR generated',
        };
    }
}
