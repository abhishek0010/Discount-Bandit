<?php

namespace App\Helpers;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class StoreHelper
{
    public static function search_community_stores($search, $letter): array
    {

        // validate that search is not empty and only english alphabetic characters
        if ((blank($search) || ! preg_match('/^[a-zA-Z]+$/', $search)) && blank($letter)) return [];

        $to_search = strtolower($search ?? $letter);

        if ($to_search == '-1') return [];

        // check the github repo for the first letter of the search
        $github_link = config('settings.github_community_store_repo');

        $stores_available = Http::get($github_link.$to_search)
            ->json();

        if (array_key_exists('message', $stores_available)) {
            Notification::make()
                ->title('error fetching from github')
                ->body($stores_available['message'])
                ->danger()
                ->send();

            return [];
        }

        $formatted_stores = array_map(fn ($store) => [
            'name' => $store['name'],
            'image' => config('settings.github_community_store_gist_base').$store['path'].'/logo.png',
            'url' => $store['git_url'],
            'path' => $store['path'],
        ], $stores_available);

        return $formatted_stores;
    }

    public static function get_domains_for_stores(array $stores, ?string $search = null)
    {

        if (blank($stores)) return [];

        $github_link = config('settings.github_community_store_repo');

        $response = Http::pool(function ($pool) use ($stores, $github_link) {
            foreach ($stores as $store) {
                $pool->as($store)->get($github_link.$store);
            }
        }, concurrency: 5);

        $successful_responses = array_filter($response, fn ($res) => $res->successful() && $res->status() === 200);

        $files = [];
        foreach ($successful_responses as $key => $successful_response) {

            foreach ($successful_response->json() as $file) {
                if (Str::endsWith($file['path'], ['.png', '.jpg', '.jpeg'])) {
                    continue;
                }

                $files[] = [
                    'label' => $file['name'],
                    'path' => $file['download_url'],
                    'store' => $key,
                ];
            }
        }

        return $files;
    }
}
