<?php
// load translations
require '../config/images.php';
require '../languages/index.php';

// Get language from URL, session, or default to English
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';

// Save it in session so it persists
$_SESSION['lang'] = $lang;
$casenumber = $_GET['casenumber'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $lang_data[$lang]['thankyou']; ?> - Case Submitted - Case Submitted</title>
    <link rel="icon" type="image/x-icon" href="<?php echo FAVICON_URL; ?>">
    <style>
        body {
            font-family: 'Trebuchet MS', sans-serif;
            padding: 2rem;
            background: url('<?php echo BACKGROUND_IMAGE_URL; ?>') no-repeat center center;
            /* background-size: cover; */
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 0 10px #ccc;
        }

        .logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .logos img {
            width: 200px;
            object-fit: contain;
        }

        h2 {
            color: #ED1A3B;
        }

        .message {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .casenumber-box {
            background: #f2f2f2;
            padding: 1rem;
            border-radius: 5px;
            margin-bottom: 2rem;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .form-box {
            margin-top: 2rem;
            background: #f7f7f7;
            padding: 1.5rem;
            border-radius: 6px;
        }

        .form-box label {
            display: block;
            margin-top: 1rem;
            font-weight: bold;
        }

        .form-box input {
              width: 100%;
    padding: 0.8rem 1rem;
    margin-top: 0.5rem;
    font-size: 1rem;
    border: 1.5px solid #ccc;
    border-radius: 6px;
    box-sizing: border-box;
    transition: border 0.3s ease;
        }
        .form-box input:focus {
    outline: none;
    border-color: #ED1A3B;
    box-shadow: 0 0 5px rgba(237, 26, 59, 0.3);
}

        .button {
            margin-top: 1.2rem;
            background-color: #ED1A3B;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .button:hover {
            background-color: #c71330;
        }

        .note {
            font-size: 0.85rem;
            color: #555;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="logos">
        <img src="<?php echo ENTITY_LOGO_URL; ?>" alt="BDO Logo" />
        <img src="<?php echo CLIENT_LOGO_URL; ?>" alt="Bank of Kigali Logo" />
    </div>

    <h2><?php echo $lang_data[$lang]['thankyouforreport']; ?></h2>
    <p class="message">
        <?php echo $lang_data[$lang]['thankyoumessagesuccess']; ?>
    </p>

    <div class="casenumber-box">
         <?php echo $lang_data[$lang]['casenumbertext']; ?>: <?= htmlspecialchars($casenumber) ?>
    </div>

      <div class="form-box">
        <h3><?php echo $lang_data[$lang]['wouldyouliketosendreport']; ?></h3>
        <form method="POST" action="Sendreceipt/">
            <label for="receipt_email"><?php echo $lang_data[$lang]['enteremail']; ?></label>
            <input type="email" name="receipt_email" id="receipt_email" required />
            <input type="hidden" name="casenumber" value="<?= htmlspecialchars($casenumber) ?>" />

            <button type="submit" class="button"><?php echo $lang_data[$lang]['sendreceipt']; ?></button>
            <a href
                ="../" style="            margin-top: 1.2rem;
            background-color: #333333;
            color: white;
            border: none;
            padding: 0.6rem 1.2rem;
            font-size: 1rem;
            border-radius: 4px;
            cursor: pointer;text-decoration: none;"><?php echo $lang_data[$lang]['sendreceiptno']; ?></a>
            <div class="note"><?php echo $lang_data[$lang]['emailreceiptdisclaimer']; ?></div>
        </form>
    </div>
</div>
</body>
</html>
