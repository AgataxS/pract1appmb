<?php
// views/admin/popularidad_materias.php
include '../../include/auth.php';
requireAdmin();
include '../../include/header.php';
include '../../include/db_connection.php';

// Obtener la popularidad de las materias
$sql = "SELECT pm.id_materia, m.nombre, m.codigo, pm.numero_solicitantes
        FROM popularidad_materias pm
        JOIN materias m ON pm.id_materia = m.id_materia
        ORDER BY pm.numero_solicitantes DESC";
$result = $conn->query($sql);

?>
<h2>Popularidad de Materias</h2>

<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre de la Materia</th>
            <th>Número de Solicitantes</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['codigo']); ?></td>
                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['numero_solicitantes']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="3">No hay datos de popularidad disponibles.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
$conn->close();
include '../../include/footer.php';
?>
