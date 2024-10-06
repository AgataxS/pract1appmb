<?php
// views/admin/asignaciones.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

// Obtener datos para los select
$materias = $conn->query("SELECT * FROM materias WHERE estado = 'abierta'");
$docentes = $conn->query("SELECT * FROM docentes");
$aulas = $conn->query("SELECT * FROM aulas");
$horarios = $conn->query("SELECT * FROM horarios");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_asignacion'])) {
    $id_materia = intval($_POST['id_materia']);
    $id_docente = intval($_POST['id_docente']);
    $id_aula = intval($_POST['id_aula']);
    $id_horario = intval($_POST['id_horario']);
    $estado_asignacion = $conn->real_escape_string($_POST['estado_asignacion']);
    $cupo = intval($_POST['cupo']);

    $stmt = $conn->prepare("INSERT INTO asignaciones (id_materia, id_docente, id_aula, id_horario, estado_asignacion, cupo) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }
    $stmt->bind_param("iiiisi", $id_materia, $id_docente, $id_aula, $id_horario, $estado_asignacion, $cupo);

    if ($stmt->execute()) {
        $success = "Asignación agregada exitosamente.";
    } else {
        $error = "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Obtener asignaciones
$sql = "SELECT a.id_asignacion, m.nombre AS materia, d.nombre AS docente_nombre, d.apellido AS docente_apellido, au.nombre_aula, h.turno, h.hora_inicio, h.hora_fin, a.estado_asignacion, a.cupo
        FROM asignaciones a
        JOIN materias m ON a.id_materia = m.id_materia
        JOIN docentes d ON a.id_docente = d.id_docente
        JOIN aulas au ON a.id_aula = au.id_aula
        JOIN horarios h ON a.id_horario = h.id_horario";
$result = $conn->query($sql);
?>
<h2>Asignaciones</h2>

<?php if (isset($success)): ?>
    <p class="success"><?php echo htmlspecialchars($success); ?></p>
<?php endif; ?>
<?php if (isset($error)): ?>
    <p class="error"><?php echo htmlspecialchars($error); ?></p>
<?php endif; ?>

<form method="POST" action="">
    <label for="id_materia">Materia:</label>
    <select name="id_materia" id="id_materia" required>
        <option value="">Selecciona una materia</option>
        <?php while ($row = $materias->fetch_assoc()): ?>
            <option value="<?php echo $row['id_materia']; ?>"><?php echo htmlspecialchars($row['nombre']); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="id_docente">Docente:</label>
    <select name="id_docente" id="id_docente" required>
        <option value="">Selecciona un docente</option>
        <?php while ($row = $docentes->fetch_assoc()): ?>
            <option value="<?php echo $row['id_docente']; ?>"><?php echo htmlspecialchars($row['nombre'] . ' ' . $row['apellido']); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="id_aula">Aula:</label>
    <select name="id_aula" id="id_aula" required>
        <option value="">Selecciona un aula</option>
        <?php while ($row = $aulas->fetch_assoc()): ?>
            <option value="<?php echo $row['id_aula']; ?>"><?php echo htmlspecialchars($row['nombre_aula']); ?></option>
        <?php endwhile; ?>
    </select>

    <label for="id_horario">Horario:</label>
    <select name="id_horario" id="id_horario" required>
        <option value="">Selecciona un horario</option>
        <?php while ($row = $horarios->fetch_assoc()): ?>
            <option value="<?php echo $row['id_horario']; ?>">
                <?php echo htmlspecialchars($row['turno'] . ' ' . $row['hora_inicio'] . ' - ' . $row['hora_fin']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <label for="estado_asignacion">Estado:</label>
    <select name="estado_asignacion" id="estado_asignacion" required>
        <option value="activa">Activa</option>
        <option value="inactiva">Inactiva</option>
    </select>

    <label for="cupo">Cupo:</label>
    <input type="number" name="cupo" id="cupo" required>

    <button type="submit" name="add_asignacion">Agregar</button>
</form>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Materia</th>
            <th>Docente</th>
            <th>Aula</th>
            <th>Horario</th>
            <th>Estado</th>
            <th>Cupo</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id_asignacion']); ?></td>
                    <td><?php echo htmlspecialchars($row['materia']); ?></td>
                    <td><?php echo htmlspecialchars($row['docente_nombre'] . ' ' . $row['docente_apellido']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre_aula']); ?></td>
                    <td><?php echo htmlspecialchars($row['turno'] . ' ' . $row['hora_inicio'] . ' - ' . $row['hora_fin']); ?></td>
                    <td><?php echo htmlspecialchars($row['estado_asignacion']); ?></td>
                    <td><?php echo htmlspecialchars($row['cupo']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">No hay asignaciones registradas.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include '../../include/footer.php';
?>
