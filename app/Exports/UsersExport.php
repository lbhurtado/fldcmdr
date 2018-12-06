<?php

namespace App\Exports;

use App\User;
use App\Eloquent\Phone;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, Responsable, WithMapping, WithHeadings
{
	use Exportable;

	private $fileName = 'users.xlsx';

    public function collection()
    {
        return User::all();
    }

    public function map($user): array
    {
        return [
            $user->name,
            optional($user->mobile, function ($mobile) {
            	return Phone::number($mobile, 2);	
            }),
            $user->handle,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
            'Mobile',
            'Handle',
        ];
    }
}
