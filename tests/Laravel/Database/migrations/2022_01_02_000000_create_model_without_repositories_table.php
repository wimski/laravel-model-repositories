<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelWithoutRepositoriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('model_without_repositories', function (Blueprint $table): void {
            $table->id();
            $table->timestamps();
        });
    }
}
