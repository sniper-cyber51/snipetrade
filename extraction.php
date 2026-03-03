<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'monsite'; // REMPLACEZ PAR LE NOM DE VOTRE BDD
$user = 'root';
$pass = '123456';

// Remplacez par l'URL de votre site une fois en ligne pour l'aperçu image WhatsApp
$baseUrl = "http://votre-site.com/";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des produits
    $query = $pdo->query("SELECT * FROM vendre ORDER BY idvendre DESC");

    while ($row = $query->fetch()) {
        // 1. Nettoyage du numéro (enlève espaces, points, tirets)
        $clean_phone = str_replace([' ', '.', '-'], '', $row['telephone']);

        // 2. Préparation du message WhatsApp avec lien image
        $full_image_url = $baseUrl . $row['image_url'];
        $message = "Bonjour, je souhaite acheter ce produit :\n\n"
            . "📦 *PRODUIT :* " . strtoupper($row['nom']) . "\n"
            . "💰 *PRIX :* " . $row['prix'] . " FCFA\n"
            . "🖼️ *IMAGE :* " . $full_image_url;

        $whatsapp_url = "https://wa.me/226" . $clean_phone . "?text=" . urlencode($message);
?>

        <div class="card">
            <div class="image-container">
                <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Produit">
            </div>

            <div class="details">
                <p class="price">PRIX : <span><?php echo htmlspecialchars($row['prix']); ?> FCFA</span></p>
                <p class="name">NOM : <?php echo strtoupper(htmlspecialchars($row['nom'])); ?></p>
            </div>

            <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="buy-link">
                BUY
            </a>

            <div class="description-box">
                <?php echo htmlspecialchars($row['description']); ?>
            </div>
        </div>

<?php
    }
} catch (PDOException $e) {
    echo "<p style='color:white;'>Erreur : " . $e->getMessage() . "</p>";
}
?>