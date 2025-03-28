<?php
abstract class ArrayIdManager {
    protected $arreglo = [];


    //Obtiene el valor del arreglo
    public function getArreglo() {
        return $this->arreglo;
    }

    // Agrega un objeto nuevo en la posición id del elemento
    public function agregar($elemento) {
        $id = $elemento->getId();
        $this->arreglo[$id] = $elemento;
    }

    //Busca si existe un id dentro de los elementos del arreglo	
    public function existeId($id) {
        foreach ($this->arreglo as $elemento) {
            if ($elemento->getId() == $id) {
                return true;
            }
        }
        return false;
    }

    // Eliminar un elemento por su ID
    public function eliminarPorId($id) {
        if (isset($this->arreglo[$id])) {
            unset($this->arreglo[$id]);
        }
    }

    // Retorna por id el elemento
    public function getPorId($id) {
        if (isset($this->arreglo[$id])) {
            return $this->arreglo[$id];
        }
        return NULL;
    }

    //Modifica recibiendo un objeto, el id permanece
    public function modificar($elementoModificado) {
        $id = $elementoModificado->getId();
        if (isset($this->arreglo[$id])) {
            $this->arreglo[$id] = $elementoModificado;
        }
    }

    public abstract function mostrar();
}
