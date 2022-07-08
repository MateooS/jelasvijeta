<?php

namespace App\HelperClasses;

use Illuminate\Http\Request;
use App\Models\Meal;

class DiffTimeHelper
{
    /**
     * Check diff_time, set $dateTime accordingly
     * 
     * @return string
     */
    public static function getDiffTime(Request $request): string
    {
        if (isset($request['diff_time'])) {
            if ($request['diff_time'] <= 0) {
                exit('Diff time has to be greater than 0');
            } else {
                $dateTime = date('Y-m-d h:i:s', $request['diff_time']);
            }
        } else {
            /* Display every item */
            $dateTime = date('Y-m-d h:i:s', 1);
        }
        return $dateTime;
    }

    /**
     * Get meal's timestamps and set the status accordingly
     * 
     * @return string
     */
    public static function getStatus(Request $request, Meal $meal): string
    {
        if (isset($request['diff_time'])) {
            /* Determine which of the timestamps is latest */
            $createdAt = $meal->created_at;
            $updatedAt = $meal->updated_at;
            $deletedAt= $meal->deleted_at;

            /**
             * If delete timestamp is bigger or equal to created and updated
             * timestamps */
            if ($deletedAt >= $createdAt && $deletedAt >= $updatedAt) {
                $status = 'deleted';
            } elseif ($updatedAt > $createdAt) {
                $status = 'modified';
            } else {
                $status = 'created';
            }
        } else {
            $status = 'created';
        }

        return $status;
    }
}
