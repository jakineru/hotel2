<?php
require_once __DIR__ . '/includes/init.php';
require_once __DIR__ . '/models/Habitacion.php';

$habitaciones = Habitacion::obtenerTodas();

require_once __DIR__ . '/includes/header.php';
?>

<main>
<div class="hero">
    <h2>habitaciones disponibles</h2>
    <p>encontrá la opción perfecta para tu estadía</p>
</div>

<div class="grid-habitaciones">
    <?php foreach ($habitaciones as $hab): 
        $tipo = strtolower($hab['tipo']);
        $img = ($tipo === 'simple' || $tipo === 'suite') ? "{$tipo}.jpg" : "{$tipo}.webp";
    ?>
        <div class="card">
            <div class="card-img-container">
                <img src="/hotel2/img/<?php echo $img; ?>" alt="habitación <?php echo $hab['tipo']; ?>" class="card-img">
                <span class="badge-estado <?php echo htmlspecialchars($hab['estado']); ?>">
                    <?php echo htmlspecialchars($hab['estado']); ?>
                </span>
            </div>
            
            <div class="card-body">
                <div class="card-header-info">
                    <h3>habitación <?php echo htmlspecialchars($hab['numero']); ?></h3>
                    <p class="precio">$<?php echo htmlspecialchars($hab['precio']); ?> <span>/ noche</span></p>
                </div>
                <p class="detalle"><strong>tipo:</strong> <?php echo htmlspecialchars($hab['tipo']); ?></p>
                <p class="detalle"><strong>capacidad:</strong> <?php echo htmlspecialchars($hab['capacidad']); ?> personas</p>
                <p class="descripcion"><?php echo htmlspecialchars($hab['descripcion']); ?></p>
            </div>

            <div class="card-footer">
                <?php if ($hab['estado'] === 'disponible'): ?>
                    <?php if (isset($_SESSION['estado']) && $_SESSION['estado'] === 'logeado'): ?>
                        <a href="/hotel2/views/cliente/reservar.php?numero=<?php echo $hab['numero']; ?>" class="btn-reservar">reservar ahora</a>
                    <?php else: ?>
                        <a href="/hotel2/views/auth/login.php" class="btn-reservar btn-secondary">iniciá sesión para reservar</a>
                    <?php endif; ?>
                <?php else: ?>
                    <button class="btn-reservar btn-disabled" disabled>no disponible</button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>