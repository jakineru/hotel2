<?php
require_once __DIR__ . '/../../includes/init.php';
require_once __DIR__ . '/../../models/Reserva.php';

requerir_login();

$ci_usuario = $_SESSION['usuario_actual']['ci'];
$reservas = Reserva::obtenerPorUsuario($ci_usuario);

require_once __DIR__ . '/../../includes/header.php';
?>

<h2>mis reservas</h2>

<?php if (empty($reservas)): ?>
    <p>no tenés reservas activas.</p>
<?php else: ?>
    <div class="table-container">
        <table class="tabla-reservas">
            <thead>
                <tr>
                    <th>habitación</th>
                    <th>tipo</th>
                    <th>desde</th>
                    <th>hasta</th>
                    <th>noches</th>
                    <th>total</th>
                    <th>estado pago</th>
                    <th>acción</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $res): 
                    $d1 = new DateTime($res['fecha_in']);
                    $d2 = new DateTime($res['fecha_out']);
                    $noches = $d1->diff($d2)->days;
                    $total = $noches * $res['precio'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($res['numero_hab']); ?></td>
                        <td><?php echo htmlspecialchars($res['tipo']); ?></td>
                        <td><?php echo htmlspecialchars($res['fecha_in']); ?></td>
                        <td><?php echo htmlspecialchars($res['fecha_out']); ?></td>
                        <td><?php echo $noches; ?></td>
                        <td>$<?php echo number_format($total, 2); ?></td>
                        <td><span class="badge <?php echo $res['estado_pago']; ?>"><?php echo htmlspecialchars($res['estado_pago']); ?></span></td>
                        <td>
                            <?php if ($res['estado_pago'] === 'pendiente'): ?>
                                <a href="pagar.php?id=<?php echo $res['id']; ?>" class="btn-pagar">pagar</a>
                            <?php else: ?>
                                <span class="text-pagado">completado</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>