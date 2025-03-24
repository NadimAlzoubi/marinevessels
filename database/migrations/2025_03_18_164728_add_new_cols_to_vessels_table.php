<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->string('berth_no')->nullable();
            $table->string('voy')->nullable();
            $table->string('grt')->nullable();
            $table->string('nrt')->nullable();
            $table->string('dwt')->nullable();
            $table->dateTime('eosp')->nullable();
            $table->dateTime('aado')->nullable();
            $table->dateTime('nor_tendered')->nullable();
            $table->dateTime('nor_accepted')->nullable();
            $table->dateTime('dropped_anchor')->nullable();
            $table->dateTime('heaved_up_anchor')->nullable();
            $table->dateTime('pilot_boarded')->nullable();
            $table->dateTime('first_line')->nullable();
            $table->dateTime('berthed_on')->nullable();
            $table->dateTime('made_fast')->nullable();
            $table->dateTime('sailed_on')->nullable();
            $table->string('arrival_fuel_oil')->nullable();
            $table->string('arrival_diesel_oil')->nullable();
            $table->string('arrival_fresh_water')->nullable();
            $table->string('arrival_draft_fwd')->nullable();
            $table->string('arrival_draft_aft')->nullable();
            $table->string('departure_fuel_oil')->nullable();
            $table->string('departure_diesel_oil')->nullable();
            $table->string('departure_fresh_water')->nullable();
            $table->string('departure_draft_fwd')->nullable();
            $table->string('departure_draft_aft')->nullable();
            $table->string('next_port_of_call')->nullable();
            $table->dateTime('eta_next_port')->nullable();
            $table->text('any_requirements')->nullable();
        });
    }

    public function down()
    {
        Schema::table('vessels', function (Blueprint $table) {
            $table->dropColumn([
                'berth_no', 'voy', 'grt', 'nrt', 'dwt', 'eosp', 'aado', 'nor_tendered', 'nor_accepted',
                'dropped_anchor', 'heaved_up_anchor', 'pilot_boarded', 'first_line', 'berthed_on',
                'made_fast', 'sailed_on', 'arrival_fuel_oil', 'arrival_diesel_oil', 'arrival_fresh_water',
                'arrival_draft_fwd', 'arrival_draft_aft', 'departure_fuel_oil', 'departure_diesel_oil',
                'departure_fresh_water', 'departure_draft_fwd', 'departure_draft_aft', 'next_port_of_call',
                'eta_next_port', 'any_requirements'
            ]);
        });
    }
};
