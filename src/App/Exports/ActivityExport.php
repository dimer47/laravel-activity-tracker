<?php

namespace Dimer47\LaravelActivityTracker\App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActivityExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected ?string $startDate;
    protected ?string $endDate;

    public function __construct(?string $startDate = null, ?string $endDate = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function query(): Builder
    {
        $query = config('LaravelActivityTracker.defaultActivityModel')::query()
            ->orderBy('created_at', 'desc');

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Description',
            'Details',
            'User Type',
            'User ID',
            'User Email',
            'Route',
            'IP Address',
            'User Agent',
            'Locale',
            'Referer',
            'Method Type',
            'Created At',
            'Updated At',
        ];
    }

    public function map($activity): array
    {
        // Récupérer l'email de l'utilisateur si disponible
        $userEmail = 'N/A';
        if ($activity->userId) {
            $userModel = config('LaravelActivityTracker.defaultUserModel');
            $user = $userModel::find($activity->userId);
            $userEmail = $user?->email ?? 'N/A';
        }

        return [
            $activity->id,
            $activity->description,
            $activity->details,
            $activity->userType,
            $activity->userId,
            $userEmail,
            $activity->route,
            $activity->ipAddress,
            $activity->userAgent,
            $activity->locale,
            $activity->referer,
            $activity->methodType,
            $activity->created_at?->format('Y-m-d H:i:s'),
            $activity->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
