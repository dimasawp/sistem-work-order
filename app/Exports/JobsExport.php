<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobsExport implements FromCollection, WithHeadings {
    protected $jobs;

    public function __construct($jobs) {
        $this->jobs = $jobs;
    }

    public function collection() {
        return $this->jobs->map(function ($job) {
            return [
                'ID' => $job->id,
                'Title' => $job->title,
                'User' => $job->giver->username ?? '-',
                'Giver NIK' => $job->giver_nik,
                'Giver Name' => $job->giver_name,
                'Department Target' => $job->department->name ?? '-',
                'Ticket Number' => $job->ticket_number,
                'Tools & Materials' => $job->tools_and_materials,
                'Description' => $job->description,
                'Start Time' => $job->start_time,
                'End Time' => $job->end_time,
                'Status' => $job->status,
                'Giver Confirmation' => $job->giver_confirmation,
                'Created At' => $job->created_at,
                'Updated At' => $job->updated_at,
            ];
        });
    }

    public function headings(): array {
        return [
            'ID Job',
            'Title/Subject',
            'Username',
            'Giver NIK',
            'Giver Name',
            'Department',
            'Ticket Number',
            'Tools & Materials',
            'Description',
            'Start Time',
            'End Time',
            'Status',
            'Giver Confirmation',
            'Created At',
            'Updated At',
        ];
    }
}
