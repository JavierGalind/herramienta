<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre_factura');
            $table->string('nombre_cliente');
            $table->double('descuento');
            $table->string('tipo_comprobante');
            $table->string('referencia');
            $table->string('folio');
            $table->date('fecha');
            $table->double('importe');
            $table->double('iva');
            $table->double('subtotal');
            $table->date('fechacobro');
            $table->string('clave_clie');
            $table->string('tipo');
            $table->string('concepto');
            $table->string('descripcion');
            $table->string('empresa');
            $table->double('impuesto');
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
        Schema::dropIfExists('ventas');
    }
}
