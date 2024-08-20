<?php

namespace App\DataBase;

abstract class Migration
{
    /**
     * Método para aplicar a migração.
     *
     * @return void
     */
    abstract public function up();

    /**
     * Método para reverter a migração.
     *
     * @return void
     */
    abstract public function down();
}
