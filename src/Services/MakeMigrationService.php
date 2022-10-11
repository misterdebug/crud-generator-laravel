<?php

namespace Mrdebug\Crudgen\Services;


use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Console\Concerns\InteractsWithIO;
use Symfony\Component\Console\Output\ConsoleOutput;

class MakeMigrationService
{
    use InteractsWithIO;

    public PathsAndNamespacesService $pathsAndNamespacesService;
    public MakeGlobalService $makeGlobalService;
    public function __construct(
        PathsAndNamespacesService $pathsAndNamespacesService,
        ConsoleOutput $consoleOutput,
        MakeGlobalService $makeGlobalService
    )
    {
        $this->pathsAndNamespacesService = $pathsAndNamespacesService;
        $this->output = $consoleOutput;
        $this->makeGlobalService = $makeGlobalService;
    }

    public function replaceContentMigrationStub($namingConvention)
    {
        $migrationStub = File::get($this->pathsAndNamespacesService->getMigrationStubPath());
        $table         = $namingConvention['table_name'];
        $migrationStub = str_replace('DummyTable', $table, $migrationStub);
        $migrationStub = str_replace('DummyClass', Str::studly('create_' . $table . '_table'), $migrationStub);

        return $migrationStub;
    }

    public function findAndReplaceMigrationPlaceholderColumns($columns, $migrationStub)
    {
        $fieldsMigration='';

        // we create our placeholders regarding columns
        foreach ($columns as $column)
        {
            $type     = explode(':', trim($column));
            $sqlType = (count($type)==2) ? $type[1] : 'string';
            $column   = $type[0];

            // our placeholders
            $fieldsMigration .= str_repeat("\t", 3).'$table'."->$sqlType('".trim($column)."');\n";
        }

        $fieldsMigration = $this->makeGlobalService->cleanLastLineBreak($fieldsMigration);

        // we replace our placeholders
        $migrationStub = str_replace('DummyFields', $fieldsMigration, $migrationStub);
        return $migrationStub;
    }

    public function generateNameMigrationFile($date, $namingConvention)
    {
        return $date . '_create_' . $namingConvention['table_name'] . '_table.php';
    }

    public function createMigrationFile($migrationStub, $namingConvention)
    {
        $date = date('Y_m_d_His');
        File::put($this->pathsAndNamespacesService->getRealpathBaseMigration() .$this->generateNameMigrationFile($date, $namingConvention) , $migrationStub);
        $this->line("<info>Created Migration:</info> $date"."_create_".$namingConvention['table_name']."_table.php");
    }

    public function makeCompleteMigrationFile($namingConvention, $columns)
    {
        $migrationStub = $this->replaceContentMigrationStub($namingConvention);
        $migrationStub = $this->findAndReplaceMigrationPlaceholderColumns($columns, $migrationStub);
        $this->createMigrationFile($migrationStub, $namingConvention);
    }
}
