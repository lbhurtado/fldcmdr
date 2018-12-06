<?php

namespace App\Exports;

use Spatie\Permission\Models\Role;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Contracts\Support\Responsable;
use Maatwebsite\Excel\Concerns\FromCollection;

class RolesExport implements FromCollection, Responsable, WithMapping, WithHeadings
{
	use Exportable;

	private $fileName = 'roles.xlsx';

    public function collection()
    {
        return Role::all();
    }

    public function map($role): array
    {
        return [
            $role->name,
        ];
    }

    public function headings(): array
    {
        return [
            'Name',
        ];
    }
}
