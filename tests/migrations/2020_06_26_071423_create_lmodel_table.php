<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLmodelTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $enumValues = ['new', 'open', 'closed'];

        Schema::create('lmodel', function (Blueprint $table) use ($enumValues): void {
            $table->id();
            $table->string('code', 50)->comment('Code for constants etc.');
            $table->integer('number')->unsigned()->comment('Number to have fun with.');
            $table->string('string', 50)->comment('To use in constants etc.');
            $table->enum('status', $enumValues);
            $table->timestamp('created_at_test')->nullable();
            $table->timestamp('updated_at_test')->nullable();
            $table->timestamps();
        });

        $counter = 1;
        while ($counter <= 10) {
            $randomEnum = random_int(0, 2);

            DB::table('lmodel')->insert([
                'code' => 'Code ' . $counter,
                'number' => $counter,
                'string' => 'String ' . $counter,
                'status' => $enumValues[$randomEnum],
                'created_at_test' => Carbon::now(),
                'updated_at_test' => Carbon::now()
            ]);

            $counter++;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::drop('lmodel');
    }
}
