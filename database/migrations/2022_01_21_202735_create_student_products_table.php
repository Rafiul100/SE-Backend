<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('studentproducts', function (Blueprint $table) {
        
            //required
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('name');
            $table->string('price');
            $table->string('stock');
            $table->string('category');
            $table->string('subcategory');
            $table->string('delivery');

            //optional
            $table->string('image')->nullable();
            $table->mediumText('description')->nullable();
            $table->tinyInteger('featured')->default('0')->nullable();
            $table->tinyInteger('popular')->default('0')->nullable();
            $table->tinyInteger('status')->default('0'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('studentproducts');
    }
}
