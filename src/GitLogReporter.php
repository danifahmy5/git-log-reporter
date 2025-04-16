<?php

namespace Danifahmy5\GitLogReporter;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Font;

class GitLogReporter
{
  public function readLog()
  {
    $log = shell_exec('git log --pretty=format:"%ad|%s|%an" --date=format:"%d-%m-%Y"');
    return $log;
  }

  private function getDefaultFilePath()
  {
    $homeDir = getenv('HOME') ?: getenv('USERPROFILE');
    $downloadsDir = $homeDir . DIRECTORY_SEPARATOR . 'Downloads';
    $bulanTahun = date('m-Y');
    $timestamp = date('H-i-s');
    return $downloadsDir . DIRECTORY_SEPARATOR . 'LAPORAN KEGIATAN ' . $bulanTahun . ' ' . $timestamp . '.xlsx';
  }

  public function writeLogToSpreadsheet($filePath = null, $month = null, $program = null)
  {
    if ($filePath === null) {
      $filePath = $this->getDefaultFilePath();
    }

    $log = $this->readLog();
    $logEntries = explode("\n", $log);
    usort($logEntries, function ($a, $b) {
      $dateA = \DateTime::createFromFormat('d-m-Y', explode('|', $a)[0]);
      $dateB = \DateTime::createFromFormat('d-m-Y', explode('|', $b)[0]);
      return $dateA <=> $dateB;
    });

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $defaultFont = new Font();
    $defaultFont->setName('Arial');
    $spreadsheet->getDefaultStyle()->setFont($defaultFont);
    // Menulis header
    $headerStyleArray = [
      'font' => [
        'bold' => true,
      ],
      'fill' => [
        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
        'startColor' => [
          'argb' => 'FFD3D3D3', // Light gray background
        ],
      ],
    ];

    $sheet->setCellValue('A1', 'ALAT');
    $sheet->setCellValue('B1', 'JUMLAH');
    $sheet->setCellValue('C1', 'LOKASI');
    $sheet->setCellValue('D1', 'MASUK');
    $sheet->setCellValue('E1', 'KELUAR');
    $sheet->setCellValue('F1', 'HASIL PEMERIKSAAN');
    $sheet->setCellValue('G1', 'PERBAIKAN');
    $sheet->setCellValue('H1', 'RINCIAN BIAYA');

    // Apply style to header
    $sheet->getStyle('A1:H1')->applyFromArray($headerStyleArray);

    // Menulis log ke spreadsheet
    $row = 2;
    foreach ($logEntries as $entry) {
      $parts = explode('|', $entry);
      $date = \DateTime::createFromFormat('d-m-Y', $parts[0]);
      $message = $parts[1];
      $author = $parts[2];

      if ($date && $date->format('m') == $month) {
        $sheet->setCellValue('A' . $row, 'Program'); // Nama program
        $sheet->setCellValue('B' . $row, 1); // Jumlah
        $sheet->setCellValue('C' . $row, 'edp'); // Lokasi
        $sheet->setCellValue('D' . $row, $date->format('d-m-Y')); // Tanggal dari git log
        $sheet->setCellValue('E' . $row, $date->format('d-m-Y')); // Sama dengan kolom masuk
        $sheet->setCellValue('F' . $row, $program); // Nama program
        $sheet->setCellValue('G' . $row, $message); // Isi commit dari git log
        $sheet->setCellValue('H' . $row, ''); // String kosong

        $row++;
      }
    }

    $writer = new Xlsx($spreadsheet);
    $writer->save($filePath);
  }
}
