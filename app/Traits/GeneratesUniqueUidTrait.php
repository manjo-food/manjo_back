<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait GeneratesUniqueUidTrait
{
    // This function generates a unique username by combining the prefix "chicken"
// with a random numeric suffix of 6–8 digits.
// With a maximum of 5 attempts per call, it checks each generated username
// against the database to ensure uniqueness.
// Since the total possible usernames (~99.9 million) far exceeds our maximum
// user count (5 million), the probability of hitting the 5-attempt limit is
// extremely low (~0.00003%). Therefore, the function will almost always return
// a unique username on the first attempt.

    public function generateUniqueUid(
        $modelClass,
        $column = 'username',
        $maxAttempts = 5,
        $prefix = 'chicken',
        $minDigits = 6,
        $maxDigits = 8
    )
    {
        $uids = [];

        for ($i = 0; $i < $maxAttempts; $i++) {
            $digits = random_int($minDigits, $maxDigits);

            $suffix = (string)random_int(1, 9);
            for ($d = 1; $d < $digits; $d++) {
                $suffix .= (string)random_int(0, 9);
            }

            $uids[] = $prefix . $suffix;
        }

        $existingUids = $modelClass::whereIn($column, $uids)->pluck($column)->toArray();

        $uniqueUids = array_values(array_diff($uids, $existingUids));

        if (empty($uniqueUids)) {
            Log::info('Maximum attempts reached for generating unique UID ' . $modelClass);
            return [
                'error' => true,
                'code' => 500,
                'data' => __('messages.uid_max_attempt'),
            ];
        }

        return reset($uniqueUids); // returns the first unique username string
    }

}
