<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait GeneratesUniqueUidTrait
{
    /**
     * Generate a unique UID for a given model and column.
     *
     * @param string $modelClass The Eloquent model class (e.g., Customer::class)
     * @param string $column The column to check uniqueness (default: 'uid')
     * @param int $maxAttempts Number of attempts to generate a unique UID
     * @param int $length Length of the UID
     * @return string|array Returns UID string or error array
     */
    public function generateUniqueUid($modelClass, $column = 'uid', $maxAttempts = 5, $length = 8)
    {
        $uids = [];
        for ($i = 0; $i < $maxAttempts; $i++) {
            $uids[] = strtoupper(substr(md5(uniqid('', true)), 0, $length));
        }

        $existingUids = $modelClass::whereIn($column, $uids)->pluck($column)->toArray();
        $uniqueUids = array_diff($uids, $existingUids);

        if (empty($uniqueUids)) {
            Log::info('Maximum attempts reached for generating unique UID ' . $modelClass);
            return [
                'error' => true,
                'code' => 500,
                'data' => __('messages.uid_max_attempt'),
            ];
        }

        return reset($uniqueUids);
    }
}
