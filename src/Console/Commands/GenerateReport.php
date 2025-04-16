<?php

namespace Danifahmy5\GitLogReporter\Console\Commands;

use Illuminate\Console\Command;
use Danifahmy5\GitLogReporter\GitLogReporter;

class GenerateReport extends Command
{
  /**
   * The console command signature.
   *
   * This command generates a report.
   *
   * @param string|null $filePath Optional. The path to the file.
   * @param string|null $month Optional. The month for which the report is generated.
   * @param string|null $program Optional. The program for which the report is generated.
   *
   * Usage in terminal:
   * php artisan report:generate [filePath] [month] [program]
   */
  protected $signature = 'report:generate {--path=} {--month=} {--program=}';
  protected $description = 'Generate a Git log report and save it to a spreadsheet';

  public function __construct()
  {
    parent::__construct();
  }

  public function handle()
  {
    $filePath = $this->option('path') ?? null;
    $month = $this->option('month') ? $this->option('month') : date('m');
    $program = $this->option('program') ? $this->option('program') : config('app.name');

    $reporter = new GitLogReporter();
    $reporter->writeLogToSpreadsheet($filePath, $month, $program);

    $this->info('Git log report generated successfully.');
  }
}
