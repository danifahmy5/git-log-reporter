<?php

namespace Danifahmy5\GitLogReporter;

use Illuminate\Support\ServiceProvider;
use Danifahmy5\GitLogReporter\Console\Commands\GenerateReport;

class GitLogReporterServiceProvider extends ServiceProvider
{
  protected $commands = [
    GenerateReport::class,
  ];

  public function register()
  {
    $this->commands($this->commands);
  }

  public function boot()
  {
    // Bootstrapping code if needed
  }
}
