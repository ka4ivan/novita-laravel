<?php

namespace App\Models\Traits;

trait HasDatetimeFormatterTz
{
    /**
     * @param null $field
     * @param null $format
     * @return \Carbon\Carbon|false|string|null
     */
    public function getDatetime($field = null, $format = null)
    {
        $res = null;
        $clientTZ = config('app.timezone_client') ?: config('app.timezone');

        if ($field && $field instanceof \DateTime) {
            $res = $field->timezone($clientTZ);
        } elseif (is_string($field) && $this->getOriginal($field)) {
            $date = $this->{$field} instanceof \DateTime
                ? $this->{$field}
                : \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->{$field});
            $res = $date->timezone($clientTZ);

        }

        if ($res && $format) {
            return $res->format($format);
        }

        return $res;
    }
}
