<? defined('GIFTER_APP') or die("Don't look, I'm shy! >.<") ?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="public/css/style.css?v=<?= time() ?>">

    <? if(isset($appendStyles)) foreach($appendStyles as $style): ?>
    <link rel="stylesheet" href="<?= $style ?>.css?v=<?= time() ?>">
    <? endforeach; ?>

    <script src="https://kit.fontawesome.com/3d2537f2b4.js" crossorigin="anonymous"></script>
    <script src="public/scripts/script.js?v=<?= time() ?>" defer></script>

    <? if(isset($passDataToFront)): ?>
    <script type="text/javascript"><? foreach($passDataToFront as $key => $value) { echo "const $key = $value;"; } ?></script>
    <? endif; ?>

    <? if(isset($appendScripts)) foreach($appendScripts as $script): ?>
    <script src="<?= $script["src"] ?>.js?v=<?= time() ?>" <?= $script["defer"] ?? false ? "defer" : "" ?>></script>
    <? endforeach; ?>

    <title>Gifter</title>
</head>

<body>
    <? include "$page.php"; ?>
</body>

</html>