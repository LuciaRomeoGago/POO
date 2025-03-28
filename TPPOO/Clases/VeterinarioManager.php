<?php
require_once('clases' . DIRECTORY_SEPARATOR . 'Veterinario.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'VeterinarioModelo.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'ABMinterface.php');

// no utilizo esta clase en este sistema todavia, podria utilizarlo si agrandara el sistema 
//utilizando un perfil de secretaria que me agregue los vetes al sistema y asocio a turnos 

class VeterinarioManager extends ArrayIdManager implements ABMinterface {
	public function __construct(){
		$this->levantar();
	}

	public function levantar(){
		try {
			$veterinarios = VeterinarioModelo::obtenerTodos();
	
			foreach ($veterinarios as $veterinario) {
				$this->agregar($veterinario);
			}
		} catch (PDOException $e) {
			echo "Error al levantar veterinarios: " . htmlspecialchars($e->getMessage());
		}
	}

	// Crea un Veterinario
	public function alta(){
		echo "Ingrese nombre del veterinario: ";
		$nombre = trim(fgets(STDIN));
		echo "Ingrese especialidad del veterinario: ";
		$especialidad = trim(fgets(STDIN));
		$veterinario = new Veterinario($nombre, $especialidad);

		$modelo = new VeterinarioModelo();

		if ($modelo->guardar($veterinario)) {  
			$this->agregar($veterinario);
			echo "Veterinario agregado con éxito." . PHP_EOL;
			return true;
		} else {
			echo "Error al agregar el veterinario." . PHP_EOL;
			return false;
		}
	}

	// Elimina un Veterinario
	public function baja(){
		echo "Ingrese ID del veterinario a eliminar: ";
		$id = trim(fgets(STDIN));

		if ($this->existeId($id)) {
			$veterinario = 	$this->getPorId($id);

			if ($veterinario === null) {
				echo "No se encontró ningún veterinario con ese ID." . PHP_EOL;
				return;
			}
			$veterinario->mostrar();

			echo '¿Está seguro que desea eliminar este veterinario? (S/N): ';
			$rta = trim(fgets(STDIN));

			if (strtolower($rta) === 's') {
				if ($veterinario->borrar()) {  
					$this->eliminarPorId($id);  
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

	// Modifica un Veterinario
	public function modificar($elementoModificado){
		echo "Ingrese ID del veterinario a modificar: ";
		$id = trim(fgets(STDIN));

		if ($this->existeId($id)) {
			$veterinarioModificado = 	$this->getPorId($id);
			if ($veterinarioModificado === null) {
				echo "No se encontró ningún veterinario con ese ID." . PHP_EOL;
				return;
			}

			echo 'Está por modificar al siguiente veterinario:' . PHP_EOL;
			$veterinarioModificado->mostrar();

			echo '¿Está seguro que desea modificar este veterinario? (S/N): ';
			$rta = trim(fgets(STDIN));

			if (strtolower($rta) === 's') {
				echo "Ingrese nuevo nombre (deje en blanco para no modificar): ";
				$nombre = trim(fgets(STDIN));
				if ($nombre !== "") {  
					$veterinarioModificado->setNombre($nombre);
				}

				echo "Ingrese nueva especialidad (deje en blanco para no modificar): ";
				$especialidad = trim(fgets(STDIN));
				if ($especialidad !== "") {  
					$veterinarioModificado->setEspecialidad($especialidad);
				}

				if ($veterinarioModificado->modificar()) { 
					echo "Veterinario modificado con éxito." . PHP_EOL;
				} else {
					echo "Error al modificar el veterinario." . PHP_EOL;
				}
			} else {
				echo "Modificación cancelada." . PHP_EOL;
			}
		}
	}

	// Muestra Veterinarios
	public function mostrar()
	{
		$veterinarios = $this->getArreglo(); 
		Menu::cls(); 
		Menu::subtitulo('Lista de Veterinarios');

		if (empty($veterinarios)) {
			echo "No hay veterinarios para mostrar." . PHP_EOL;
			return;
		}

		foreach ($veterinarios as $veterinario) {
			$veterinario->mostrar(); 
		}
		Menu::waitForEnter(); 
	}

	// Agrega Mascota a un Cliente
	public function altaMascota()
	{
		$clienteId = Menu::readln("Ingrese el Id del dueño de la mascota: ");
		$cliente = ClienteModelo::buscarPorId($clienteId);

		if ($cliente) {
			$nombreMascota = Menu::readln("Ingrese el nombre de la mascota: ");
			$edad = Menu::readln("Ingrese la edad de la mascota: ");
			$raza = Menu::readln("Ingrese la raza de la mascota: ");
			$historialMedico = Menu::readln("Ingrese el historial médico de la mascota: ");

			$mascota = new Mascota($nombreMascota, $edad, $raza, $historialMedico);
			$mascota->setClienteId($cliente->getId());

			$mascotaModelo = new MascotaModelo();
			if ($mascotaModelo->guardar($mascota)) {
				$cliente->agregarMascota($mascota);

				echo "La mascota se ha agregado exitosamente al cliente con Id " . $clienteId . PHP_EOL;
			}
		} else {
			echo "No se encontró ningún cliente con el Id ingresado." . PHP_EOL;
		}
	}

	// Muestra Mascotas de un Cliente
	public function mostrarMascota()
	{
		$clienteId = Menu::readln("Ingrese el ID del cliente para mostrar sus mascotas: ");
		$clienteModelo = new ClienteModelo();
		$cliente = $clienteModelo->buscarPorId($clienteId);

		if ($cliente === null) {
			echo "No se encontró un cliente con el ID ingresado." . PHP_EOL;
			return; 
		}
		$cliente->mostrarMascotas(); 
		Menu::waitForEnter(); 
	}
}
