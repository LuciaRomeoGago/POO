<?php
require_once('clases' . DIRECTORY_SEPARATOR . 'Veterinario.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');

class VeterinarioManager extends ArrayIdManager{

    public function levantar() {
        try {
            $sql = "SELECT * FROM Veterinario";
            $veterinarios = Conexion::query($sql);

            foreach ($veterinarios as $veterinario) {
                // Crear el objeto Veterinario y agregarlo al arreglo
                $nuevoVeterinario = new Veterinario(
                    $veterinario->nombre,
                    $veterinario->especialidad,
                    $veterinario->id  // Asignar ID desde la base de datos
                );
                
                // Agregar al arreglo gestionado por ArrayIdManager
                $this->agregar($nuevoVeterinario);
            }
        } catch (PDOException $e) {
            echo "Error al levantar veterinarios: " . htmlspecialchars($e->getMessage());
        }
    }

    public function alta() {
        echo "Ingrese nombre del veterinario: ";
        $nombre = trim(fgets(STDIN));

        echo "Ingrese especialidad del veterinario: ";
        $especialidad = trim(fgets(STDIN));

        // Crear nuevo objeto Veterinario
        $veterinario = new Veterinario($nombre, $especialidad);

        if ($veterinario->guardar()) {  // Guardar en base de datos
            echo "Veterinario agregado con éxito." . PHP_EOL;
            return true;
        } else {
            echo "Error al agregar el veterinario." . PHP_EOL;
            return false;
        }
    }

    public function baja() {
        echo "Ingrese ID del veterinario a eliminar: ";
        $id = trim(fgets(STDIN));

        if ($this->existeId($id)) {
			$veterinario = 	$this -> getPorId($id); 

			if ($veterinario === null) {
				echo "No se encontró ningún veterinario con ese ID." . PHP_EOL;
				return; 
			}

			// Mostrar información antes de eliminar
			$veterinario -> mostrar();

			// Confirmar eliminación
			echo '¿Está seguro que desea eliminar este veterinario? (S/N): ';
			$rta = trim(fgets(STDIN));

			if (strtolower($rta) === 's') {
				if ($veterinario -> borrar()) {  // Llamar al método borrar()
					$this -> eliminarPorId($id);  // Eliminar del arreglo
					echo "Veterinario eliminado con éxito." . PHP_EOL;
				} else {
					echo "Error al intentar eliminar el veterinario." . PHP_EOL;
				}
			} else {
				echo "Eliminación cancelada." . PHP_EOL;
			}
		} else {
			echo "El ID ingresado no existe." . PHP_EOL;
		}
	}

	public function modificar($elementoModificado) {
		echo "Ingrese ID del veterinario a modificar: ";
		$id = trim(fgets(STDIN));

		if ($this -> existeId($id)) {

			$veterinarioModificado = 	$this -> getPorId($id); 

			if ($veterinarioModificado === null) {
				echo "No se encontró ningún veterinario con ese ID." . PHP_EOL;
				return; 
			}

			// Mostrar información actual del veterinario antes de modificar
			echo 'Está por modificar al siguiente veterinario:' . PHP_EOL;
			$veterinarioModificado -> mostrar();

			echo '¿Está seguro que desea modificar este veterinario? (S/N): ';
			$rta = trim(fgets(STDIN));

			if (strtolower($rta) === 's') {

				echo "Ingrese nuevo nombre (deje en blanco para no modificar): ";
				$nombre = trim(fgets(STDIN));
				if ($nombre !== "") {  // Si no está vacío, actualizar nombre
					$veterinarioModificado -> setNombre($nombre);
				}

				echo "Ingrese nueva especialidad (deje en blanco para no modificar): ";
				$especialidad = trim(fgets(STDIN));
				if ($especialidad !== "") {  // Si no está vacío, actualizar especialidad
					$veterinarioModificado -> setEspecialidad($especialidad);
				}

				if ($veterinarioModificado -> modificar()) {  // Modificar en base de datos
					echo "Veterinario modificado con éxito." . PHP_EOL;
				} else {
					echo "Error al modificar el veterinario." . PHP_EOL;
				}
			} else {
				echo "Modificación cancelada." . PHP_EOL;
			}
        }
   }

   public function mostrar() {
	$veterinarios = $this->getArreglo(); // Obtener el arreglo de veterinarios
	Menu::cls(); // Limpiar la pantalla
	Menu::subtitulo('Lista de Veterinarios');

	if (empty($veterinarios)) {
		echo "No hay veterinarios para mostrar." . PHP_EOL;
		return;
	}

	foreach ($veterinarios as $veterinario) {
		$veterinario->mostrar(); // Llamar al método mostrar de cada veterinario
	}

	Menu::waitForEnter(); // Esperar a que el usuario presione Enter
}
}
