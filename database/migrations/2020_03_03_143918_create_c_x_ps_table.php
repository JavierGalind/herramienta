<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCXPsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('c_x_ps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_factura');
            $table->string('empresa');
            $table->string('tipo_comprobante');
            $table->string('referencia');
            $table->string('folio');
            $table->string('pedido');
            $table->date('fecha');
            $table->double('importe');
            $table->double('iva');
            $table->double('subtotal');
            $table->double('descuento');
            $table->date('fecha_cobro');
            $table->string('clave_clie');
            $table->string('nombre_cliente');
            $table->string('tipo');
            $table->string('concepto');
            $table->double('impuesto');
            $table->double('impuesto2');
            $table->string('clv_con');
            $table->string('cl_con');
            $table->date('fecha_doc');
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
        Schema::dropIfExists('c_x_ps');
    }
}
