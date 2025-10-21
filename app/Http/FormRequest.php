<?php

namespace App\Http;

use Illuminate\Support\Carbon;

class FormRequest extends \Illuminate\Foundation\Http\FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidationPhoneValues(string ...$fields)
    {
        $merges = [];
        foreach ($fields as $field) {
            if ($value = $this->get($field)) {
                $merges[$field] = $this->clearPhoneValue($value);
            }
        }

        $this->merge($merges);
    }

    protected function clearPhoneValue($value): string
    {
        return preg_replace('/[^0-9]/si', '', $value);
    }

    protected function prepareForValidationDateValues(string ...$fields)
    {
        $appTZ = config('app.timezone');
        $clientTZ = config('app.timezone_client') ?: $appTZ;

        $merges = [];
        foreach ($fields as $field) {
            if ($value = $this->get($field)) {
                $merges[$field] = Carbon::parse($value, $clientTZ)
                    ->setTimezone($appTZ);
            }
        }

        $this->merge($merges);
    }

    protected function prepareForValidationDateValue(string $field, string $timeOfDay = null)
    {
        $appTZ = config('app.timezone');
        $clientTZ = config('app.timezone_client') ?: $appTZ;

        $merges = [];
        if ($value = $this->get($field)) {
            $datetime = Carbon::parse($value, $clientTZ);

            $datetime = match ($timeOfDay) {
                'start' => $datetime->startOfDay(),
                'end' => $datetime->endOfDay(),
                default => $datetime
            };

            $merges[$field] = $datetime->setTimezone($appTZ);
        }

        $this->merge($merges);
    }

    protected function prepareForValidationDatetimeValues(string ...$fields)
    {
        $appTZ = config('app.timezone');
        $clientTZ = config('app.timezone_client') ?: $appTZ;

        $merges = [];
        foreach ($fields as $field) {
            if ($value = $this->get($field)) {
                $merges[$field] = Carbon::parse($value, $clientTZ)
                    ->setTimezone($appTZ)
                    ->toString();
            }
        }

        $this->merge($merges);
    }

    public function prepareForValidationBooleanValues(string ...$fields)
    {
        $merges = [];
        foreach ($fields as $field) {
            if ($value = $this->get($field)) {
                $merges[$field] = false;
                if ($value === 'true' || $value === '1' || $value === true || $value === 1 || $value === 'TRUE') {
                    $merges[$field] = true;
                }
            }
        }

        $this->merge($merges);
    }
}
