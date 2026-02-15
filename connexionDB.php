<?php

// -----------------------------------------------------------------
// 1. CONFIGURATION DE LA BASE DE DONNÉES (À ADAPTER !)
// -----------------------------------------------------------------
$host = "localhost"; // Généralement 'localhost'
$port = '3306';
$db = "monsite"; // Nom de la base de données
$user = "root"; // *À REMPLACER*
$pass = '123456'; // *À REMPLACER*
$charset = 'utf8mb4';

// Chaîne de connexion DSN
$dsn = "mysql:host=$host;port=$port;  dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Afficher les erreurs (pour le développement)
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Récupérer les résultats sous forme de tableau associatif
    PDO::ATTR_EMULATE_PREPARES   => false, // Utiliser les requêtes préparées natives (plus sécurisé)
];

// -----------------------------------------------------------------
// 2. VÉRIFICATION DE LA SOUMISSION DU FORMULAIRE
// -----------------------------------------------------------------
if (isset($_POST['Publier'])) {

    // Récupération des données du formulaire
    $productName = $_POST['product_Name'];
    $productDescription = $_POST['product_Description'];
    $productPrice = $_POST['product_Price'];
    $commercialNumber = $_POST['commercial_Number'];
    $image_url = "";

    // -------------------------------------------------------------
    // 3. CONNEXION À LA BASE DE DONNÉES (PDO)
    // -------------------------------------------------------------
    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // Afficher une erreur si la connexion échoue
        throw new \PDOException($e->getMessage(), (int)$e->getCode());
    }

    // -------------------------------------------------------------
    // 4. GESTION DU TÉLÉCHARGEMENT DE L'IMAGE
    // -------------------------------------------------------------
    if (isset($_FILES['product_Image']) && $_FILES['product_Image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/"; // *Assurez-vous que ce dossier existe et est inscriptible*
        $original_filename = basename($_FILES["product_Image"]["name"]);

        $imageFileType = strtolower(pathinfo($original_filename, PATHINFO_EXTENSION));
        $new_filename = uniqid('img_', true) . "." . $imageFileType;
        $target_file = $target_dir . $new_filename;

        if (move_uploaded_file($_FILES["product_Image"]["tmp_name"], $target_file)) {
            $image_url = $target_file;
            echo "L'image " . htmlspecialchars($original_filename) . " a été téléchargée avec succès. <br>";
        } else {
            echo "Erreur lors du téléchargement de l'image. <br>";
        }
    }

    // -------------------------------------------------------------
    // 5. PRÉPARATION ET EXÉCUTION DE LA REQUÊTE D'INSERTION
    // -------------------------------------------------------------
    try {
        // La requête préparée utilise des marqueurs nommés (:nom_du_marqueur)
        $sql = "INSERT INTO vendre (nom, description, prix, telephone, image_url, date_creation) 
                VALUES (:nom, :desc, :prix, :tel, :img_url, NOW())";

        $stmt = $pdo->prepare($sql);

        // Liaison des valeurs aux marqueurs de la requête préparée (Bind)
        $stmt->bindParam(':nom', $productName);
        $stmt->bindParam(':desc', $productDescription);
        $stmt->bindParam(':prix', $productPrice);
        $stmt->bindParam(':tel', $commercialNumber);
        $stmt->bindParam(':img_url', $image_url);

        // Exécution
        $stmt->execute();

        echo "✅ Félicitations ! Les données du produit ont été insérées avec succès dans la base de données (via PDO).";
    } catch (\PDOException $e) {
        // Gérer spécifiquement les erreurs SQL (par exemple, si une colonne est manquante)
        echo "❌ Erreur lors de l'insertion : " . $e->getMessage();
    }
} else {
    echo "Accès invalide. Veuillez soumettre le formulaire.";
}
