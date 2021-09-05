<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelWithRepositoriesTable extends Migration
{
    public function up(): void
    {
        Schema::create('model_with_repositories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });
    }
}
