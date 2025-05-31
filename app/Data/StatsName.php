<?php

namespace App\Data;

use App\Models\KvStore;

/**
 * App statics
 *
 * These are stored inside database table kv_store modelled by KvStore.
 */
enum StatsName: string {
    case TotalImageConvcersions = 'stat_total_image_conversions';
    case TotalImageCompressed = 'state_total_image_compressed';

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
        log_message('info', "Increasing kv_store $keyName.$subkey by $by.");

        $oldValue = $this->get($subkey);

        $data = [
            'key_name' => $keyName,
            'key_subname' => $subkey,
            'value_int' => 0,
        ];

        if(is_null($oldValue)) {
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
        log_message('info', "Seeding/Initializing StatsName values");
        foreach(self::cases() as $item) {
            $data = [
                'key_name' => $item->value,
                'value_int' => 0,
            ];

            // @phpstan-ignore method.notFound
            model(KvStore::class)->ignore(true)->insert($data);
        }
    }

    public static function table(): string
    {
        $html = [];
        foreach(self::cases() as $item) {
            $html[] = "<div class='row bg-light px-2 py-1 mt-1'>";
            $html[] = "<div class='col-10'>" . $item->label() . "</div>";
            $html[] = "<div class='col-2'>" . $item->get() . "</div>";
            $html[] = "</div>";
        }

        return implode(' ', $html);
    }

    private function label(): string 
    {
        $value = match($this) {
            self::TotalImageConvcersions => "Total Images Converted",
            self::TotalImageCompressed => "Total Images Compressed",
        };
        return $value;
    }
}
