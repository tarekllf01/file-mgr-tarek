<?php

use App\Models\Settings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(with(new Settings)->getTable(), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('value')->nullable();
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });

        $values = array (
            array ('name'=>'max_upload_size','value'=>null),
            array ('name'=>'allowed_files','value'=>'png,jpg,gif,zip,txt,jpeg,mp3,mp4'),
        );
        Settings::insert($values);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(with(new Settings)->getTable());
    }
}
