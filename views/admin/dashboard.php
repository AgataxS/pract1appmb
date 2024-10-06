<?php
// views/admin/dashboard.php

include '../../include/auth.php';
requireAdmin();
include '../../include/db_connection.php';
include '../../include/header.php';

// Obtener estadísticas
// Total de estudiantes
$result_estudiantes = $conn->query("SELECT COUNT(*) as total_estudiantes FROM estudiantes");
$total_estudiantes = $result_estudiantes->fetch_assoc()['total_estudiantes'];

// Total de materias
$result_materias = $conn->query("SELECT COUNT(*) as total_materias FROM materias");
$total_materias = $result_materias->fetch_assoc()['total_materias'];

// Total de inscripciones pendientes
$result_inscripciones_pendientes = $conn->query("SELECT COUNT(*) as total_pendientes FROM inscripciones WHERE estado = 'pendiente'");
$total_pendientes = $result_inscripciones_pendientes->fetch_assoc()['total_pendientes'];

// Obtener materias solicitadas
$sql_solicitadas = "SELECT m.codigo, m.nombre, COUNT(i.id_inscripcion) as total_solicitudes
                    FROM inscripciones i
                    JOIN materias m ON i.id_materia = m.id_materia
                    WHERE i.estado = 'pendiente'
                    GROUP BY m.id_materia";
$result_solicitadas = $conn->query($sql_solicitadas);
?>

<h2>Dashboard de Administrador</h2>

<div class="dashboard">
    <div class="dashboard-item">
        <h3>Total de Estudiantes</h3>
        <p><?php echo $total_estudiantes; ?></p>
        <a href="<?php echo BASE_URL; ?>/views/admin/estudiantes.php" class="btn">Ver Estudiantes</a>
    </div>
    <div class="dashboard-item">
        <h3>Total de Materias</h3>
        <p><?php echo $total_materias; ?></p>
        <a href="<?php echo BASE_URL; ?>/views/admin/materias.php" class="btn">Ver Materias</a>
    </div>
    <div class="dashboard-item">
        <h3>Inscripciones Pendientes</h3>
        <p><?php echo $total_pendientes; ?></p>
        <a href="<?php echo BASE_URL; ?>/views/admin/inscripciones.php" class="btn">Gestionar Inscripciones</a>
    </div>
</div>

<h3>Materias Solicitadas</h3>

<?php if ($result_solicitadas && $result_solicitadas->num_rows > 0): ?>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Nombre de la Materia</th>
                <th>Total de Solicitudes</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result_solicitadas->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['total_solicitudes']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
<?php else: ?>
    <p>No hay materias solicitadas.</p>
<?php endif; ?>

<?php
$conn->close();
include '../../include/footer.php';
?>
