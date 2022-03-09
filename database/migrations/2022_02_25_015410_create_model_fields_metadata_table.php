<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelFieldsMetadataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_fields_metadata', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamps();
            $table->string('name', 255)->index();
            $table->enum('type', ['boolean', 'number', 'varchar', 'text', 'json'])->default('varchar'); // this must be compatible with EscolaLms\ModelFields\Enum\MetaFieldTypeEnum::getValues()
            $table->json('rules')->nullable();
            $table->json('extra')->nullable();
            $table->text('default')->nullable();
            $table->string('class_type', 255)->index();
            $table->integer('visibility')->default(1 << 0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('model_fields_metadata');
    }
}
