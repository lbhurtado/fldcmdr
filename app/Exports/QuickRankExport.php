<?php

namespace App\Exports;

use App\QuickRank;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class QuickRankExport implements FromCollection, Responsable, WithHeadings, ShouldAutoSize
{
	use Exportable;

	private $fileName = 'quick_rank.xlsx';

    public function collection()
    {
        return QuickRank::all([
            'question',
            'answer',
            'interviewer',
            'respondent'
        ]);
    }

    public function headings(): array
    {
        return [
            'Question',
            'Answer',
            'Interviewer',
            'Respondent'
        ];
    }
}
