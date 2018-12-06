<?php

namespace App\Exports;

use App\QuickRank;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;

class QuickRankExport implements FromCollection, Responsable, WithHeadings
{
	use Exportable;

	private $fileName = 'quick_rank.xlsx';

    public function collection()
    {
        return QuickRank::all();
    }

    public function headings(): array
    {
        return [
            'Category',
            'Question',
            'Answer',
            'Interviewer',
            'Respondent'
        ];
    }
}
