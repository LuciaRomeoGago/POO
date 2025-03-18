<?php
abstract class ArrayIdManager
{
    protected $arreglo = [];


    //Get el valor del arreglo
    public function getArreglo()
    {
        return $this->arreglo;
    }

    // Agregar un objeto nuevo en la posición id del elemento
    public function agregar($elemento)
    {
        $id = $elemento->getId();
        $this->arreglo[$id] = $elemento;
    }

    //Busca si existe un id dentro de los elementos del arreglo	
    public function existeId($id)
    {
        foreach ($this->arreglo as $elemento) {
            if ($elemento->getId() == $id) {
                return TRUE;
            }
        }
        return FALSE;
    }

    // Eliminar un elemento por su ID
    public function eliminarPorId($id)
    {
        if (isset($this->arreglo[$id])) {
            unset($this->arreglo[$id]);
        }
    }

    // Retorna por id el elemento, retorna NULL si no está
    public function getPorId($id)
    {
        if (isset($this->arreglo[$id])) {
            return $this->arreglo[$id];
        }
        return NULL;
    }

    //Va a modificar recibiendo un objeto, el id permanece
    public function modificar($elementoModificado)
    {
        $id = $elementoModificado->getId();
        if (isset($this->arreglo[$id])) {
            $this->arreglo[$id] = $elementoModificado;
        }
    }

    //Retorna la cantidad de elementos del arreglo
    public function tamanio()
    {
        return  count($this->arreglo);
    }

    public abstract function mostrar();
}
