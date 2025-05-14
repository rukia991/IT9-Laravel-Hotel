<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class ModifyCustomerGenderField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('customers', function (Blueprint $table) {
            // First, drop the existing enum constraint
            $table->string('gender')->change();
            
            // Then modify it to be a new enum with more options
            DB::statement("ALTER TABLE customers MODIFY gender ENUM('Male', 'Female', 'Other', 'Prefer not to say') NOT NULL");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('customers', function (Blueprint $table) {
            // Revert back to original enum
            DB::statement("ALTER TABLE customers MODIFY gender ENUM('Male', 'Female') NOT NULL");
        });
    }
} 