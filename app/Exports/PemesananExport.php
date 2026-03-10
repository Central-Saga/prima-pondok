<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class PemesananExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents
{
    protected $bookings;
    protected $month;
    protected $year;

    public function __construct($bookings, $month, $year)
    {
        $this->bookings = $bookings;
        $this->month = $month;
        $this->year = $year;
    }

    public function collection()
    {
        return $this->bookings->map(function ($booking) {
            return [
                'wisatawan' => optional($booking->wisatawan)->name,
                'kamar' => optional($booking->kamar)->nama_kamar,
                'checkin' => optional($booking->tanggal_checkin)?->format('d/m/Y'),
                'checkout' => optional($booking->tanggal_checkout)?->format('d/m/Y'),
                'malam' => $booking->jumlah_hari,
                'total' => 'Rp ' . number_format($booking->total_bayar, 0, ',', '.'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Wisatawan',
            'Kamar',
            'Checkin',
            'Checkout',
            'Malam',
            'Total',
        ];
    }

    public function title(): string
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return 'Laporan ' . ($months[$this->month] ?? $this->month) . ' ' . $this->year;
    }

    public function styles(Worksheet $sheet)
    {
        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(25);
        $sheet->getColumnDimension('B')->setWidth(25);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(20);

        // Return empty array - we'll style header in registerEvents
        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $months = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];

                $sheet = $event->sheet->getDelegate();
                
                // Insert title row at the top
                $sheet->insertNewRowBefore(1, 1);
                $sheet->setCellValue('A1', 'Laporan Pemesanan Bulan ' . ($months[$this->month] ?? $this->month) . ' Tahun ' . $this->year);
                
                // Merge cells for title
                $sheet->mergeCells('A1:F1');
                
                // Style title row
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                $sheet->getRowDimension('1')->setRowHeight(25);

                // Get last row before adding total
                $lastRow = $sheet->getHighestRow();
                
                // Style header row (row 2) with yellow background and center alignment
                $sheet->getStyle('A2:F2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Apply borders to all cells including data rows
                $sheet->getStyle('A2:F' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);
                
                // Center align ONLY data rows (row 3 onwards), NO background color
                if ($lastRow > 2) {
                    $sheet->getStyle('A3:F' . $lastRow)->applyFromArray([
                        'alignment' => [
                            'horizontal' => Alignment::HORIZONTAL_CENTER,
                            'vertical' => Alignment::VERTICAL_CENTER,
                        ],
                    ]);
                }

                // Add total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'TOTAL UANG');
                $sheet->mergeCells('A' . $totalRow . ':E' . $totalRow);
                
                // Calculate total
                $total = $this->bookings->sum('total_bayar');
                $sheet->setCellValue('F' . $totalRow, 'Rp ' . number_format($total, 0, ',', '.'));
                
                // Style total row
                $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFF00']
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Right align the total amount in column E
                $sheet->getStyle('F' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
