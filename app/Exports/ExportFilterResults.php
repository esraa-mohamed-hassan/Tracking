<?php

namespace App\Exports;

use App\ExportAllFilterResults;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class ExportFilterResults  extends \PhpOffice\PhpSpreadsheet\Cell\StringValueBinder implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents, WithCustomValueBinder
{
    use Exportable;
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $items = ExportAllFilterResults::GetItems($this->data);
        return collect($items);
    }

    public function headings(): array
    {
        return [
            "Sku",
            "Name",
            "Description",
            "Category",
            "Golden Price",
            "Golden After Discount Price",
            "Dorepha Price",
            "Dorepha After Discount Price",
            "Maesta Price",
            "Maesta After Discount Price",
            "Niceone Price",
            "Niceone After Discount Price",
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_FORMULA,
        ];
    }
/**
 * @return array
 */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                // All headers - set font size to 16
                $cellRange = 'A1:Z1';
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(16)->getBold();

                // Set first row to height 20
                $event->sheet->getDelegate()->getRowDimension(1)->setRowHeight(20);
            },
        ];
    }

}
