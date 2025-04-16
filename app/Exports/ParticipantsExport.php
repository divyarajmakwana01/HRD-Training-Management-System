<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ParticipantsExport implements FromCollection, WithHeadings, WithTitle
{
    protected $programmeId;
    protected $programmeName;

    public function __construct($programmeId)
    {
        $this->programmeId = $programmeId;

        // Fetch Programme Name
        $programme = DB::table('programme')->where('id', $programmeId)->first();
        $this->programmeName = $programme ? $programme->name : 'Programme';
    }

    public function collection()
    {
        return DB::table('participants')
            ->join('registration', 'participants.id', '=', 'registration.participant_id')
            ->join('programme', 'registration.programme_id', '=', 'programme.id')
            ->where('registration.programme_id', $this->programmeId)
            ->where('registration.active', 1)
            ->select(
                'participants.id',
                'participants.fname',
                'participants.lname',
                'participants.designation',
                'participants.email',
                'participants.mobile',
                'participants.institute_name',
                'programme.name as programme_name',
                'registration.category',
                'registration.payment',
                'registration.payment_verification'
            )
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'First Name',
            'Last Name',
            'Designation',
            'Email',
            'Mobile',
            'Institute Name',
            'Programme Name',  // Added Programme Name
            'Category',
            'Payment Mode',
            'Payment Verification'
        ];
    }

    // Set Sheet Name
    public function title(): string
    {
        return $this->programmeName;
    }
}
