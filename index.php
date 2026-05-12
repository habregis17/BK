<?php
// Include centralized image configuration
require_once __DIR__ . '/config/images.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Welcome - BK WhistleBlower</title>
    <link rel="icon" type="image/x-icon" href="<?php echo FAVICON_URL; ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Trebuchet MS', sans-serif;
            background: url('<?php echo BACKGROUND_IMAGE_URL; ?>') center center fixed;
            background-size: cover;
            background-attachment: fixed;
            background-repeat: no-repeat;
            background-position: center center;
            background-color: #1a1a1a;
            min-height: 100vh;
            padding: 2rem 1rem;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.98);
            padding: 3rem 2.5rem;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(10px);
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logos {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            gap: 2rem;
        }

        .logos img {
            width: 150px;
            height: auto;
            object-fit: contain;
            /* filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.1)); */
            transition: transform 0.3s ease;
        }

        .logos img:hover {
            transform: scale(1.05);
        }

        h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #ED1A3B;
            text-align: center;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .language-guide {
            font-family: 'Trebuchet MS', sans-serif;
            font-size: 0.95rem;
            line-height: 1.8;
            margin-bottom: 2.5rem;
            text-align: center;
            color: #555;
        }

        .language-guide div {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0.6rem 0;
        }

        .language-guide img {
            width: 24px;
            height: 24px;
            margin-right: 12px;
            border-radius: 3px;
        }

        .button-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.2rem;
            margin-bottom: 3rem;
            padding: 0 1rem;
        }

        button {
            padding: 14px 24px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            border-radius: 10px;
            border: none;
            font-family: 'Trebuchet MS', sans-serif;
            color: white;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.8rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        button img {
            width: 24px;
            height: 24px;
            border-radius: 2px;
        }

        button:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        button:active {
            transform: translateY(-2px);
        }

        .btn-ki {
            background: linear-gradient(135deg, #009966 0%, #007a4d 100%);
        }

        .btn-en {
            background: linear-gradient(135deg, #0052cc 0%, #003d99 100%);
        }

        .btn-fr {
            background: linear-gradient(135deg, #cc0000 0%, #990000 100%);
        }

        .btn-sw {
            background: linear-gradient(135deg, #ff9933 0%, #cc7a29 100%);
        }

        .channels {
            margin-top: 3rem;
            padding-top: 2.5rem;
            border-top: 2px solid #f0f0f0;
        }

        .channels h3 {
            text-align: center;
            margin-bottom: 2rem;
            font-size: 1.4rem;
            color: #333;
            font-weight: 600;
        }

        .channel-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .channel-item {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem 1.5rem;
            border-radius: 12px;
            background: linear-gradient(135deg, #f8f9fa 0%, #f0f2f5 100%);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            text-align: center;
            border: 1px solid #e9ecef;
            min-height: 160px;
        }

        .channel-item:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-color: #ED1A3B;
        }

        .channel-item img {
            width: 40px;
            height: 40px;
            margin-bottom: 1rem;
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
            transition: transform 0.3s ease;
        }

        .channel-item:hover img {
            transform: scale(1.1);
        }

        .channel-item strong {
            font-size: 1.1rem;
            color: #ED1A3B;
            margin-bottom: 0.8rem;
            display: block;
            font-weight: 700;
        }

        .channel-item a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            display: inline-block;
            margin: 0.3rem 0;
        }

        .channel-item a:hover {
            color: #ED1A3B;
            text-decoration: underline;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 2rem 1.5rem;
            }

            .logos {
                flex-direction: column;
                margin-bottom: 2rem;
            }

            .logos img {
                width: 120px;
            }

            h2 {
                font-size: 1.5rem;
            }

            .button-group {
                grid-template-columns: 1fr;
                gap: 1rem;
            }

            .channel-row {
                grid-template-columns: 1fr;
            }

            .channels h3 {
                font-size: 1.1rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 1.5rem 1rem;
            }

            h2 {
                font-size: 1.3rem;
            }

            .language-guide {
                font-size: 0.85rem;
            }

            button {
                padding: 12px 16px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="logos">
        <img src="<?php echo ENTITY_LOGO_URL; ?>" alt="BDO OFFICIAL Logo" />
        <img src="<?php echo CLIENT_LOGO_URL; ?>" alt="Bank of Kigali Logo"  />
    </div>

    <h2>Welcome / Murakaza neza / Bienvenue / Karibu</h2>

    <div class="language-guide">
        <div>
            <img src="https://twemoji.maxcdn.com/v/latest/svg/1f1ec-1f1e7.svg" alt="UK">
            Choose your language below to continue
        </div>
        <div>
            <img src="https://twemoji.maxcdn.com/v/latest/svg/1f1f7-1f1fc.svg" alt="Rwanda">
            Hitamo ururimi rwawe kugirango ukomeze
        </div>
        <div>
            <img src="https://twemoji.maxcdn.com/v/latest/svg/1f1eb-1f1f7.svg" alt="France">
            Veuillez choisir votre langue pour continuer
        </div>
        <div>
            <img src="https://twemoji.maxcdn.com/v/latest/svg/1f1f0-1f1ea.svg" alt="Kenya">
            Tafadhali chagua lugha yako ili kuendelea
        </div>
    </div>


    <form method="get" action="Welcome">
        <input type="hidden" name="token" value="" />
        <div class="button-group">
            <button type="submit" name="lang" value="rw" class="btn-ki"><img src="https://twemoji.maxcdn.com/v/latest/svg/1f1f7-1f1fc.svg" alt="Rwanda" style="width:20px; vertical-align:middle; margin-right:6px;">Kinyarwanda<br></button>
            <button type="submit" name="lang" value="en" class="btn-en"><img src="https://twemoji.maxcdn.com/v/latest/svg/1f1ec-1f1e7.svg" alt="UK" style="width:20px; vertical-align:middle; margin-right:6px;">English<br></button>
            <button type="submit" name="lang" value="fr" class="btn-fr"><img src="https://twemoji.maxcdn.com/v/latest/svg/1f1eb-1f1f7.svg" alt="France" style="width:20px; vertical-align:middle; margin-right:6px;">Français<br></button>
            <button type="submit" name="lang" value="sw" class="btn-sw"> <img src="https://twemoji.maxcdn.com/v/latest/svg/1f1f0-1f1ea.svg" alt="Kenya" style="width:20px; vertical-align:middle; margin-right:6px;">Kiswahili<br></button>
        </div>
    </form>
    <div class="channels">
        <h3 style="text-align:center; margin-bottom:1rem;font-size: 18px;">
            Other Reporting Channels / Izindi nzira zo gutanga amakuru / Autres canaux de signalement
        </h3>
        <div class="channel-row">
            <div class="channel-item">
                    <img src="https://cdn-icons-png.flaticon.com/512/732/732200.png" alt="Email Icon">
                    <div>
                        <strong>Emails</strong><br>
                        <a href="mailto:bk.tangamakuru@bdo-ea.com">bk.tangamakuru@bdo-ea.com</a><br>
                        <a href="mailto:bk.whistleblowing@bdo-ea.com">bk.whistleblowing@bdo-ea.com</a>
                    </div>
            
            </div>
           <div class="channel-item">
               
               <div>
                   <strong>Whatsapp</strong><br>
                   <a href="https://wa.me/250796885059" target="_blank">+250796885059</a>
               </div>
           </div>

           <div class="channel-item">
               <img src="https://cdn-icons-png.flaticon.com/512/597/597177.png" alt="Hotline Icon">
               <div>
                   <strong>Hotline</strong><br>
                   <a href="tel:6041">6041</a>
               </div>
           </div>

        </div>
    </div>
</div>
</body>
</html>
