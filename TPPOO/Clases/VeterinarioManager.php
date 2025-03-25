<?php
require_once('clases' . DIRECTORY_SEPARATOR . 'Veterinario.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'VeterinarioModelo.php');
require_once('clases' . DIRECTORY_SEPARATOR . 'Cliente.php');
require_once('Lib' . DIRECTORY_SEPARATOR . 'interface.php');

// no utilizo esta clase en este sistema todavia, podria utilizarlo si agrandara el sistema 
//utilizando un perfil de secretaria que me agregue los vetes al sistema y asocio a turnos 

class VeterinarioManager extends ArrayIdManager
{
	public function __construct()
	{
		$this->levantar();
	}

	public function levantar()
	{
		try {
			$veterinarios = VeterinarioModelo::obtenerTodos();
	
			foreach ($veterinarios as $veterinario) {
				$this->agregar($veterinario);
			}
		} catch (PDOException $e) {
			echo "Error al levantar veterinarios: " . htmlspecialchars($e->getMessage());
		}
	}


	// permite ingresar detalles de un nuevo veterinario
	public function alta()
	{
		echo "Ingrese nombre del veterinario: ";
		$nombre = trim(fgets(STDIN));

		echo "Ingrese especialidad del veterinario: ";
		$especialidad = trim(fgets(STDIN));

		// Crear nuevo objeto Veterinario
		$veterinario = new Veterinario($nombre, $especialidad);

		$modelo = new VeterinarioModelo();

		if ($modelo->guardar($veterinario)) {  // Guardar en base de datos
			$this->agregar($veterinario);
			echo "Veterinario agregado con éxito." . PHP_EOL;
			return true;
		} else {
			echo "Error al agregar el veterinario." . PHP_EOL;
			return false;
		}
	}



	// elimina un veterinario existente
	public function baja()
	{
		echo "Ingrese ID del veterinario a eliminar: ";
		$id = trim(fgets(STDIN));

		if ($this->existeId($id)) {
			$veterinario = 	$this->getPorId($id);

			if ($veterinario === null) {
				echo "No se encontró ningún veterinario con ese ID." . PHP_EOL;
				return;
			}

			// Mostrar información antes de eliminar
			$veterinario->mostrar();

			// Confirmar eliminación
			echo '¿Está seguro que desea eliminar este veterinario? (S/N): ';
			$rta = trim(fgets(STDIN));

			if (strtolower($rta) === 's') {
				if ($veterinario->borrar()) {  // Llamar al método borrar()
					$this->eliminarPorId($id);  // Eliminar del arreglo
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

	// modifica detalles de un veterinario
	public function modificar($elementoModificado)
	{
		echo "Ingrese ID del veterinario a modificar: ";
		$id = trim(fgets(STDIN));

		if ($this->existeId($id)) {

			$veterinarioModificado = 	$this->getPorId($id);

			if ($veterinarioModificado === null) {
				echo "No se encontró ningún veterinario con ese ID." . PHP_EOL;
				return;
			}

			// Mostrar información actual del veterinario antes de modificar
			echo 'Está por modificar al siguiente veterinario:' . PHP_EOL;
			$veterinarioModificado->mostrar();

			echo '¿Está seguro que desea modificar este veterinario? (S/N): ';
			$rta = trim(fgets(STDIN));

			if (strtolower($rta) === 's') {

				echo "Ingrese nuevo nombre (deje en blanco para no modificar): ";
				$nombre = trim(fgets(STDIN));
				if ($nombre !== "") {  // Si no está vacío, actualizar nombre
					$veterinarioModificado->setNombre($nombre);
				}

				echo "Ingrese nueva especialidad (deje en blanco para no modificar): ";
				$especialidad = trim(fgets(STDIN));
				if ($especialidad !== "") {  // Si no está vacío, actualizar especialidad
					$veterinarioModificado->setEspecialidad($especialidad);
				}

				if ($veterinarioModificado->modificar()) {  // Modificar en base de datos
					echo "Veterinario modificado con éxito." . PHP_EOL;
				} else {
					echo "Error al modificar el veterinario." . PHP_EOL;
				}
			} else {
				echo "Modificación cancelada." . PHP_EOL;
			}
		}
	}


	public function mostrar()
	{
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

	// agrega una nueva mascota a un cliente existente
	public function altaMascota()
	{
		$clienteId = Menu::readln("Ingrese el Id del dueño de la mascota: ");
		$cliente = ClienteModelo::buscarPorId($clienteId);

		if ($cliente) {
			$nombreMascota = Menu::readln("Ingrese el nombre de la mascota: ");
			$edad = Menu::readln("Ingrese la edad de la mascota: ");
			$raza = Menu::readln("Ingrese la raza de la mascota: ");
			$historialMedico = Menu::readln("Ingrese el historial médico de la mascota: ");

			// Crea el nuevo objeto Mascota
			$mascota = new Mascota($nombreMascota, $edad, $raza, $historialMedico);
			$mascota->setClienteId($cliente->getId());

			$mascotaModelo = new MascotaModelo();
			if ($mascotaModelo->guardar($mascota)) {
				// Asocia la mascota al cliente
				$cliente->agregarMascota($mascota);

				echo "La mascota se ha agregado exitosamente al cliente con Id " . $clienteId . PHP_EOL;
			}
		} else {
			echo "No se encontró ningún cliente con el Id ingresado." . PHP_EOL;
		}
	}


	// muestra mascotas asociadas a un cliente especifico
	public function mostrarMascota()
	{
		// Pedir al veterinario que ingrese el ID del cliente
		$clienteId = Menu::readln("Ingrese el ID del cliente para mostrar sus mascotas: ");

		$clienteModelo = new ClienteModelo();
		// Buscar el cliente por ID
		$cliente = $clienteModelo->buscarPorId($clienteId);

		if ($cliente === null) {
			echo "No se encontró un cliente con el ID ingresado." . PHP_EOL;
			return; // Salir si no se encuentra el cliente
		}

		// Llamar directamente al método mostrarMascotas() del cliente
		$cliente->mostrarMascotas(); // Este método ya imprime las mascotas o un mensaje si no hay ninguna

		Menu::waitForEnter(); // Esperar a que el usuario presione Enter antes de continuar
	}
}
