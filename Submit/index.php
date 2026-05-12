<?php
require '../config/db.php';
require '../config/images.php';
require '../languages/index.php';
// Save it in session so it persists
$_SESSION['lang'] = $lang;
// Get language from URL, session, or default to English
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';



$token = $_GET['token'] ?? '';
$stmt = $pdo->prepare("SELECT * FROM clients WHERE token = ?");
$stmt->execute([$token]);
$client = $stmt->fetch();

if (!$client) die("Invalid client token.");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($client['name']) ?> - Whistleblower</title>
  <link rel="icon" type="image/x-icon" href="<?php echo FAVICON_URL; ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      font-family: 'Trebuchet MS', sans-serif;
      background: url('<?php echo BACKGROUND_IMAGE_URL; ?>') center center fixed;
      background-size: cover;
      background-attachment: fixed;
      background-repeat: no-repeat;
      background-position: center center;
      background-color: #f5f5f5; /* fallback */
      margin: 0;
      padding: 2rem 1rem;
      min-height: 100vh;
      color: #333;
    }

    .container {
      max-width: 900px;
      margin: 0 auto;
      background: rgba(255, 255, 255, 0.98);
      border-radius: 12px;
      padding: 2.5rem;
      box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
      backdrop-filter: blur(10px);
    }

    /* Progress Bar */
    .progress-container {
      margin-bottom: 2rem;
    }

    .progress-bar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .step {
      flex: 1;
      text-align: center;
      position: relative;
      padding: 0.5rem;
    }

    .step::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 2px;
      background: #e0e0e0;
      z-index: 1;
    }

    .step.active::before {
      background: #ED1A3B;
    }

    .step.completed::before {
      background: #009966;
    }

    .step-circle {
      width: 30px;
      height: 30px;
      border-radius: 50%;
      background: #e0e0e0;
      color: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: bold;
      position: relative;
      z-index: 2;
      margin: 0 auto;
      transition: all 0.3s ease;
    }

    .step.active .step-circle {
      background: #ED1A3B;
    }

    .step.completed .step-circle {
      background: #009966;
    }

    .step-label {
      display: block;
      margin-top: 0.5rem;
      font-size: 0.85rem;
      color: #666;
    }

    .step.active .step-label {
      color: #ED1A3B;
      font-weight: 600;
    }

    .step.completed .step-label {
      color: #009966;
    }

    .logos {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
      padding-bottom: 1rem;
      border-bottom: 1px solid #eee;
    }

    .logos img {
      width: 160px;
      object-fit: contain;
      transition: transform 0.3s ease;
    }

    .logos img:hover {
      transform: scale(1.05);
    }

    h2 {
      color: #ED1A3B;
      margin-bottom: 0.5rem;
      text-align: center;
      font-size: 1.8rem;
    }

    h3 {
      color: #009966;
      margin-top: 2rem;
      font-size: 1.3rem;
    }

    p, li {
      font-size: 1rem;
      color: #555;
      line-height: 1.7;
    }

    .other-guidelines {
      background: #f9f9f9;
      padding: 1.5rem;
      border-radius: 8px;
      border-left: 4px solid #ED1A3B;
      margin-bottom: 2rem;
    }

    form {
      margin-top: 2rem;
    }

    label {
      display: block;
      margin: 1.5rem 0 0.5rem;
      font-weight: 600;
      color: #444;
      font-size: 1rem;
    }

    input[type="text"],
    input[type="email"],
    input[type="tel"],
    input[type="date"],
    textarea {
      width: 100%;
      padding: 12px 15px;
      font-size: 1rem;
      border: 2px solid #e0e0e0;
      border-radius: 8px;
      transition: all 0.3s ease;
      box-sizing: border-box;
      font-family: 'Trebuchet MS', sans-serif;
      background: #fff;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    input[type="tel"]:focus,
    input[type="date"]:focus,
    textarea:focus {
      border-color: #ED1A3B;
      outline: none;
      box-shadow: 0 0 0 3px rgba(237, 26, 59, 0.1);
    }

    textarea {
      resize: vertical;
      min-height: 100px;
    }

    .radio-group {
      display: flex;
      flex-direction: column;
      gap: 0.8rem;
      margin-top: 0.5rem;
    }

    .radio-group label {
      font-weight: 500;
      display: flex;
      align-items: center;
      /* gap: 1px; */
      cursor: pointer;
      padding: 10px 1px;
      /* border-radius: 8px; */
      transition: all 0.3s ease;
      /* border: 2px solid transparent; */
    }

    .radio-group label:hover {
      /* background: #f8f8f8;
      border-color: #ED1A3B; */
    }

    .radio-group input[type="radio"] {
      /* margin: 0; */
      accent-color: #ED1A3B;
    }

    .checkbox-group {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      cursor: pointer;
      padding: 10px 15px;
      border-radius: 8px;
      transition: all 0.3s ease;
      border: 2px solid transparent;
      background: #fff8f8;
      display: none; /* Hidden by default for anonymous users */
    }

    .checkbox-group:hover {
      border-color: #ED1A3B;
    }

    .checkbox-group label {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      cursor: pointer;
      padding: 0;
      border-radius: 0;
      transition: none;
      border: none;
      background: transparent;
    }

    .checkbox-group input[type="checkbox"] {
      margin-top: 2px;
      accent-color: #ED1A3B;
    }

    .hidden {
      display: none;
    }

    .form-step {
      display: none;
      animation: fadeIn 0.5s ease-in-out;
    }

    .form-step.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .button-group {
      margin-top: 2.5rem;
      display: flex;
      justify-content: space-between;
      gap: 1rem;
    }

    button, .custom-file-upload {
      padding: 12px 24px;
      font-size: 1rem;
      font-weight: 600;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      font-family: 'Trebuchet MS', sans-serif;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    button {
      background-color: #ED1A3B;
      color: #fff;
    }

    button:hover {
      background-color: #c41632;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(237, 26, 59, 0.3);
    }

    button:disabled {
      background-color: #ccc;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    .btn-secondary {
      background-color: #666;
    }

    .btn-secondary:hover {
      background-color: #555;
    }

    .file-upload-wrapper {
      margin-top: 1rem;
    }

    input[type="file"] {
      display: none;
    }

    .custom-file-upload {
      background-color: #ED1A3B;
      color: #fff;
      display: inline-block;
    }

    .custom-file-upload:hover {
      background-color: #c71330;
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(237, 26, 59, 0.3);
    }

    #file-name-display {
      display: block;
      margin-top: 10px;
      font-size: 0.9rem;
      color: #666;
      font-style: italic;
    }

    .error-message {
      color: #d32f2f;
      font-size: 0.9rem;
      margin-top: 5px;
      display: none;
    }

    footer {
      text-align: center;
      font-size: 0.85rem;
      margin-top: 3rem;
      color: #777;
      padding-top: 2rem;
      border-top: 1px solid #eee;
    }

    @media (max-width: 768px) {
      .logos {
        flex-direction: column;
        gap: 1rem;
      }

      .button-group {
        flex-direction: column;
      }

      .progress-bar {
        flex-direction: column;
        gap: 1rem;
      }

      .step::before {
        display: none;
      }

      .container {
        padding: 1.5rem;
      }

      h2 {
        font-size: 1.5rem;
      }
    }
  </style>
  <script>
    function toggleIdentityFields(option) {
      const identityFields = document.getElementById('identityFields');
      const consentGroup = document.querySelector('.checkbox-group');
      
      if (option === 'reveal' || option === 'bdo') {
        identityFields.classList.remove('hidden');
        consentGroup.style.display = 'block';
      } else {
        identityFields.classList.add('hidden');
        consentGroup.style.display = 'none';
      }
    }

    function goToPreviousSection() {
      window.history.back();
    }

    function validateEmail() {
      const emailInput = document.getElementById("contact_email");
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailInput.value) {
        emailInput.style.borderColor = "#ccc";
        return;
      }

      if (!emailRegex.test(emailInput.value)) {
        emailInput.style.borderColor = "red";
      } else {
        emailInput.style.borderColor = "green";
      }
    }

    let currentStep = 1;

  function updateProgressBar(step) {
    // Reset all steps
    document.querySelectorAll('.step').forEach(el => {
      el.classList.remove('active', 'completed');
    });

    // Set active and completed steps
    for (let i = 1; i <= step; i++) {
      const stepEl = document.getElementById(`step${i}-indicator`);
      if (i < step) {
        stepEl.classList.add('completed');
      } else if (i === step) {
        stepEl.classList.add('active');
      }
    }
  }

  function goToStep(step) {
    document.querySelectorAll('.form-step').forEach(el => el.classList.remove('active'));
    document.getElementById(`step-${step}`).classList.add('active');
    currentStep = step;
    updateProgressBar(step);

    // Toggle other-guidelines visibility: show only on step 1
    const guidelines = document.querySelector('.other-guidelines');
    if (guidelines) {
      if (step === 1) {
        guidelines.classList.remove('hidden');
      } else {
        guidelines.classList.add('hidden');
      }
    }

    // Reset identity fields toggle in case step 1 is shown again
    if (step === 1) {
      const identityChoice = document.querySelector('input[name="identity_choice"]:checked');
      if (identityChoice) {
        if (identityChoice.value === "Anonymous") {
          toggleIdentityFields("hide");
        } else if (identityChoice.value === "Identifiable") {
          toggleIdentityFields("reveal");
        } else if (identityChoice.value === "Identifiable to BDO only") {
          toggleIdentityFields("bdo");
        }
      }
    }
  }

  window.addEventListener('DOMContentLoaded', () => {
    goToStep(1); // Show step 1 initially
    toggleIdentityFields('hide'); // Hide consent checkbox for anonymous by default

    // Attach click event for "Next" button from Step 1
    const nextBtn = document.getElementById("next-to-step-2");
    if (nextBtn) {
      nextBtn.addEventListener("click", function () {
        const selected = document.querySelector('input[name="affiliation"]:checked');
        const errorMsg = document.getElementById("affiliation-error");

        if (!selected) {
          if (errorMsg) errorMsg.style.display = "block";
          return;
        } else {
          if (errorMsg) errorMsg.style.display = "none";
        }

        // Check for consent only when identifiable
        const identityChoice = document.querySelector('input[name="identity_choice"]:checked');
        if (identityChoice && (identityChoice.value === 'Identifiable' || identityChoice.value === 'Identifiable to BDO only')) {
          const consentCheckbox = document.querySelector('input[name="privacyconsent"]');
          if (!consentCheckbox || !consentCheckbox.checked) {
            alert(alertMessages.consent);
            consentCheckbox.focus();
            return;
          }
        }

        goToStep(2);
      });
    }
  });
  </script>
 <script>
  document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById("incident_evidence");
    const display = document.getElementById("file-name-display");

    fileInput.addEventListener("change", function () {
      const files = Array.from(fileInput.files);
      if (files.length > 0) {
        display.textContent = files.map(f => f.name).join(", ");
      } else {
        display.textContent = "No files selected";
      }
    });
  });
</script>

 <!-- Before next -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // Function removed - consent checking is now handled in the main event listener
});
</script>
<script>
document.getElementById('incident_evidence').addEventListener('change', function() {
  const fileList = this.files;
  const display = document.getElementById('file-name-display');

  if (fileList.length === 0) {
    display.textContent = 'No files selected';
  } else if (fileList.length === 1) {
    display.textContent = fileList[0].name;
  } else {
    let names = [];
    for (let i = 0; i < fileList.length; i++) {
      names.push(fileList[i].name);
    }
    display.textContent = names.join(', ');
  }
});
</script>
</head>
<body>
  <div class="container">
    <!-- Language-specific alerts -->
    <script>
      const alertMessages = {
        consent: "<?php echo $lang_data[$lang]['consent_alert']; ?>"
      };
    </script>
    <!-- Logos -->
    <div class="logos">
      <img src="<?php echo ENTITY_LOGO_URL; ?>" alt="BDO Logo" />
      <img src="<?php echo CLIENT_LOGO_URL; ?>" alt="Bank of Kigali Plc Logo" />
    </div>
    <h2><?= htmlspecialchars($client['name']) ?> - <?php echo $lang_data[$lang]['systemname']; ?></h2>

    <!-- Progress Bar -->
    <div class="progress-container">
      <div class="progress-bar">
        <div class="step active" id="step1-indicator">
          <div class="step-circle">1</div>
          <span class="step-label"><?php echo $lang_data[$lang]['step1_label'] ?? 'Identity'; ?></span>
        </div>
        <div class="step" id="step2-indicator">
          <div class="step-circle">2</div>
          <span class="step-label"><?php echo $lang_data[$lang]['step2_label'] ?? 'Details'; ?></span>
        </div>
      </div>
    </div>

  <!-- OTHER GUIDELINES (Only shown on Step 1) -->
    <div class="other-guidelines">
    <!-- Intro Text -->
    <?php echo $lang_data[$lang]['guidelines']; ?> 
    </div>

    <!-- FORM START -->
    <form id="whistleForm" action="submit_case.php" method="POST"enctype="multipart/form-data">
      <!-- Step 1 -->
      <div class="form-step active" id="step-1">
            <!-- Hidden token field -->
    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
        <label><?php echo $lang_data[$lang]['Specify_type']; ?> </label>
        <div class="radio-group">
          <label><input type="radio" name="affiliation" value="Employee" required><?php echo $lang_data[$lang]['employee']; ?> </label>
          <label><input type="radio" name="affiliation" value="Supplier"> <?php echo $lang_data[$lang]['supplier']; ?> </label>
          <label><input type="radio" name="affiliation" value="Client"> <?php echo $lang_data[$lang]['client']; ?> </label>
          <label><input type="radio" name="affiliation" value="Implementing Partner"><?php echo $lang_data[$lang]['implementing_partner']; ?> </label>
          <label><input type="radio" name="affiliation" value="Other" checked><?php echo $lang_data[$lang]['other']; ?> </label>
        </div>
        <div id="affiliation-error" class="error-message">Please select your affiliation.</div>

        <label><?php echo $lang_data[$lang]['anonymity']; ?> </label>
        <div class="radio-group">
          <label><input type="radio" name="identity_choice" value="Anonymous" onclick="toggleIdentityFields('hide')" checked><?php echo $lang_data[$lang]['yes']; ?> </label>
          <label><input type="radio" name="identity_choice" value="Identifiable" onclick="toggleIdentityFields('reveal')"><?php echo $lang_data[$lang]['no']; ?></label>
          <label><input type="radio" name="identity_choice" value="Identifiable to BDO only" onclick="toggleIdentityFields('bdo')"><?php echo $lang_data[$lang]['onlytobdo']; ?></label>
        </div>

        <div id="identityFields" class="hidden">
          <div>
          <!-- Privacy Notice in different language -->
           <?php echo $lang_data[$lang]['privacy_notice']; ?>
           <!-- End of Privacy Notice in different language -->
          </div>

          <label for="fullname"><?php echo $lang_data[$lang]['fullname']; ?></label>
          <input type="text" id="fullname" name="fullname">

          <!-- <label for="department">Department</label> -->
          <input type="text" id="department" name="department" hidden>

          <label for="contact_email"><?php echo $lang_data[$lang]['email']; ?></label>
          <input type="email" id="contact_email" name="contact_email" oninput="validateEmail()">

          <label for="phone"><?php echo $lang_data[$lang]['telephone']; ?></label>
          <input type="tel" id="phone" name="phone">
        </div>

        <div class="checkbox-group">
          <label>
          <input type="checkbox" name="privacyconsent"><?php echo $lang_data[$lang]['privacy_consent']; ?>
          </label>
        </div>
        <div class="button-group">
          <a href="../?token=<?= $client['token'] ?>"><button type="button" class="btn-secondary"><?php echo $lang_data[$lang]['previous']; ?></button></a>
          <button type="button" id="next-to-step-2"><?php echo $lang_data[$lang]['next']; ?></button>
        </div>
        
      </div>

      <!-- Step 2 -->
      <div class="form-step" id="step-2">
        <?php echo $lang_data[$lang]['reported_activity_text']; ?>
        <label for="incident_description"><?php echo $lang_data[$lang]['when']; ?></label>
        <textarea id="incident_description" name="incident_when" rows="5" ></textarea>

        <label for="incident_description"><?php echo $lang_data[$lang]['where']; ?></label>
        <textarea id="incident_description" name="incident_where" rows="5" ></textarea>

        <label for="incident_description"><?php echo $lang_data[$lang]['division']; ?></label>
        <textarea id="incident_description" name="incident_division" rows="5" ></textarea>

        <label for="incident_description"><?php echo $lang_data[$lang]['indetails']; ?> </label>
        <textarea id="incident_description" name="incident_description" rows="5" ></textarea>

        <label for="incident_description"><?php echo $lang_data[$lang]['evidence']; ?></label>
        <div class="file-upload-wrapper">
       <label for="incident_evidence" class="custom-file-upload"><i class="fas fa-file-upload"></i> <?php echo $lang_data[$lang]['evidence_text']; ?></label>
        <input type="file" id="incident_evidence" name="incident_evidence" multiple>
        <span id="file-name-display"><?php echo $lang_data[$lang]['no_evidence_text']; ?></span>

<script>
document.getElementById('incident_evidence').addEventListener('change', function() {
  const fileList = this.files;
  const display = document.getElementById('file-name-display');

  if (fileList.length === 0) {
    display.textContent = 'No files selected';
  } else if (fileList.length === 1) {
    display.textContent = fileList[0].name;
  } else {
    let names = [];
    for (let i = 0; i < fileList.length; i++) {
      names.push(fileList[i].name);
    }
    display.textContent = names.join(', ');
  }
});
</script>
</div>
        <div class="button-group">
        <button type="button" class="btn-secondary" onclick="goToStep(1)"><?php echo $lang_data[$lang]['previous']; ?></button>
        <button type="submit"><?php echo $lang_data[$lang]['submitreport']; ?></button>
        </div>
      </div>
    </form>
    <div id="confirmation-message" style="display: none; max-width: 700px; margin-top: 2rem;">
  <p><strong>The information you have provided has been registered.</strong></p>
  <p>
    If you have not provided your name, address, email address or phone number, we will be unable to contact you.
    You will therefore also not be informed of the result or status of your notification.
  </p>
  <p>
    If you wish to receive a receipt of your responses, you may enter your email below:
  </p>
  <form id="email-receipt-form" style="margin-top: 1rem;">
    <label for="receipt_email">Your email address:</label>
    <input type="email" id="receipt_email" name="receipt_email" required style="width: 100%; padding: 8px; margin: 8px 0;">
    <button type="submit" class="submit-btn" style="background-color: #ED1A3B; color: white;">Request Receipt</button>
    <p id="receipt-status" style="margin-top: 1rem; color: green; display: none;">Receipt request received.</p>
  </form>
</div>

    <!-- FOOTER -->
    <footer>
      &copy; <?php echo date("Y"); ?> BDO East Africa (Rwanda) Ltd. All rights reserved.
    </footer>

  </div>

  <script>
    // Show the initial step and toggle "other guidelines"
    document.addEventListener('DOMContentLoaded', () => {
      goToStep(1);
    });
  </script>
</body>
</html>
